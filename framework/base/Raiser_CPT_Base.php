<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_CPT_Base {

	/*
	* cpt name, or page
	*/
	public $post_type = '';

	public $template = '';

	private $config = [
		'boot' => true
	];

	/*
	* For the CPT setup
	*/
	protected $labels;
	protected $rewrite;
	protected $args;

	/*
	* The blocks
	*/
	public $blocks = [];
	public $loaded_blocks = [];

	/*
	* Append for get data
	*/
	public $appends = [];

	/*
	* The admin block
	*/
	public $admin_block = '';
	public $admin_block_fields = [];

	/*
	* Admin Pages
	*/
	public $admin_pages = [];

	public function __construct($config=[]){

		$this->config = array_merge($config, $this->config);

		if( method_exists($this, 'init') ){
			$this->init();
		}

		if( method_exists($this, 'init_ajax') ){
			$this->init_ajax($this->post_type);
		}

		if( $this->config['boot'] ){
			$this->boot();
		}
	}

	protected function boot(){

		if( method_exists( $this, 'post_type_setup' )  ){
			// init the post type data
			$this->post_type_setup();
			// register the post type
			$this->register_post_type();
		}

		// register blocks
		$this->register_blocks();	

		// load eager loaded terms
		$this->with();

		// load attributes
		$this->attributes();

		// load appends
		$this->appends();

		// admin options
		$this->load_admin_options();

		// admin pages
		$this->load_admin_pages();	

		// pre posts hook register
		$this->pre_get_posts_hook();

		// permalink filter
		$this->the_permalink();

		do_action( 'raiser_cpt_base_boot', $this );					

	}

	protected function register_post_type(){

		$defaults = [
			'public' => true,
			'label' => ucfirst($this->post_type),
		];	

		$this->args['labels'] = $this->labels;
		$this->args['rewrite'] = $this->rewrite;

		register_post_type( $this->post_type, array_merge($defaults, $this->args) );

	}

	protected function register_blocks(){
		foreach( $this->blocks as $block=>$data ){

			if( !is_array($data)){
				$data = [
					'block_class'		=> $data,
					'block_settings'	=> ['key_prefix' => $this->post_type . ( $this->template == '' ? '' : '_'.$this->template ) ],
					'location'			=> [],
				];
			}


			// create location array for ACF based on setting of class
			if( empty($data['location']) ){

				if( $this->post_type == 'page' ){

					// when a template is specefied
					if( $this->template != '' ){

						$data['location'] = [
							[
								[
									'param' 	=> 'page_template',
									'operator' 	=> '==',
									'value' 	=> $this->template,						
								]
							]
						];

					} else {

						$data['location'] = [
							[
								[
									'param' 	=> 'post_type',
									'operator' 	=> '==',
									'value' 	=> $this->post_type,						
								]
							]
						];
					}

				} else {

					$data['location'] = [
						[
							[
								'param' 	=> 'post_type',
								'operator' 	=> '==',
								'value' 	=> $this->post_type,						
							]
						]
					];

				}

			}

			$the_block = new $data['block_class']( $data['block_settings'], $data['location'] );

			$this->loaded_blocks[$block] = $the_block;
		}
	}

	protected function with(){

		if( !isset($this->with['blocks']) || $this->with['blocks'] == null ){
			$this->with['blocks'] = [];
		}
		if( !isset($this->with['terms']) || $this->with['terms'] == null ){
			$this->with['terms'] = [];
		}

		$post_type = $this->post_type;

		// attach block meta data
		foreach( $this->with['blocks'] as $block_name ){

			$block = $this->loaded_blocks[$block_name];

			add_action( 'posts_results', function($posts) use ($block_name, $block, $post_type){

				foreach( $posts as $index=>$post_object ){
					if( $post_object->post_type == $post_type ){
						$posts[$index]->{$block_name} = $block->get_fields($post_object->ID);
					}
				}

				return $posts;
			});

		}

		// attach terms
		foreach( $this->with['terms'] as $tax ){

			$tax = sanitize_text_field($tax);

			add_action( 'the_posts', function( $posts, $query ) use ($tax, $post_type) {
				foreach($posts as $index=>$post_object){
					if( $post_object->post_type == $post_type ){
						if( $terms = get_the_terms( $post_object , $tax) ){
							$posts[$index]->{$tax.'_term_ids'} = wp_list_pluck( $terms, 'term_id' );
							$posts[$index]->{$tax.'_terms'} = $terms;
						}
					}
				}
				return $posts;
			}, 20, 2);
		}

	}

	protected function attributes(){

		$post_type = $this->post_type;

		add_action( 'the_posts', function( $posts, $query ) use ($post_type) {
			if( rw_query_is_post_type( $post_type, $query ) ){
				foreach($posts as $index=>$post_object){
					foreach( $post_object as $key => $val ){
						$method = 'get_'.$key.'_attribute';
						if( method_exists($this, $method) ){
							$posts[$index]->{$key} = $this->$method($val);
						}
					}
				}
			}
			return $posts;
		},9999, 2);

	}

	protected function appends(){

		$post_type = $this->post_type;
		$appends = $this->appends;
		if(empty($appends)){
			return;
		}

		add_action( 'the_posts', function( $posts, $query ) use ($post_type, $appends) {
			if( rw_query_is_post_type( $post_type, $query ) ){
				foreach($posts as $index=>$post_object){
					foreach( $appends as $append ){
						$method = 'get_'.$append.'_append';
						if( method_exists($this, $method) ){
							$posts[$index]->{$append} = $this->$method($post_object);
						}
					}
				}
			}
			return $posts;
		},9999, 2);

	}

	protected function load_admin_options(){

		if( $this->admin_block == ''){
			return;
		}

		$the_block = new $this->admin_block(['key' => $this->post_type.'_options'],['parent_slug'=>'edit.php?post_type='.$this->post_type]);

		$this->admin_block_fields = $the_block->fields;				

	}	

	protected function load_admin_pages(){
		foreach($this->admin_pages as $page){
			new $page();
		}
	}	

	protected function pre_get_posts_hook(){

		$post_type = $this->post_type;

		add_action( 'pre_get_posts', function($query)use($post_type) {  

		    // return early on admin or attachment pages
		    if ( is_admin() ) {
		        return;
		    }

			if( rw_query_is_post_type( $post_type, $query ) ){  
				if( method_exists($this, 'query_on_archive_page') ){
					$query = $this->query_on_archive_page($query);
				}
			} 

		});
		 
	}	

	protected function the_permalink(){

		$post_type = $this->post_type;

		if( method_exists($this, 'the_permalink_filter') ){

			add_filter( 'the_permalink', function($permalink, $post)use($post_type) {
				if($post == 0){
					global $post;
				}

			    if ( is_admin() ) {
			        return $permalink;
			    }

				if( $post->post_type == $post_type ){
					return $this->the_permalink_filter($permalink, $post);
				}
				return $permalink;

			}, 10, 2 );
		}
	}

}