<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_Taxonomy_Base {

	public $tax_name = '';

	public $object_types = [];

	protected $labels = [];

	protected $rewrite = [];

	protected $args = [];

	public $blocks = [];
	public $loaded_blocks = [];

	public $with = [
		'blocks' => [],
	];

	public function __construct(){
		$this->boot();
	}


	public function boot(){

		if( method_exists($this, 'tax_setup') ){
			$this->tax_setup();
			$this->register();
		}

		if( method_exists($this, 'init_ajax') ){
			$this->init_ajax($this->tax_name);
		}
		
		$this->register_blocks();

		// load eager loaded terms
		$this->with();		
	}


	public function register(){

		$defaults = [
			'public' => true,
			'label' => ucfirst($this->tax_name),
		];	

		$this->args['labels'] = $this->labels;
		$this->args['rewrite'] = $this->rewrite;

		register_taxonomy( $this->tax_name, $this->object_types, array_merge($defaults, $this->args) );

	}

	public function register_blocks(){

		foreach( $this->blocks as $block=>$data ){

			// $the_block = new $class([
			// 	'id' => $this->tax_name,
			// 	'object_types' => ['term'],
			// 	'taxonomies' => [$this->tax_name]
			// ]);


			if( !is_array($data)){
				$data = [
					'block_class'		=> $data,
					'block_settings'	=> ['key_prefix' => $this->tax_name],
					'location'			=> [],
				];
			}

			if( empty($data['location']) ){
				$data['location'] = [
					[
						[
							'param' 	=> 'taxonomy',
							'operator' 	=> '==',
							'value' 	=> $this->tax_name,						
						]
					]
				];
			}

			if( isset($data['block_class']) ) {
				$the_block = new $data['block_class']( $data['block_settings'], $data['location'] );	
			} else {
				// block on the fly
				$data['block_settings']['block_name'] = $block;
				$the_block = new Raiser_ACF_Block( $data['block_settings'], $data['location'] );
			}		

			$this->loaded_blocks[$block] = $the_block;
		}
	}

	public function with(){

		if( $this->with['blocks'] == null ){
			$this->with['blocks'] = [];
		}

		$tax_name = $this->tax_name;

		// attach block meta data
		foreach( $this->with['blocks'] as $block_name ){

			$block = $this->loaded_blocks[$block_name];

			add_action( 'get_term', function($term) use ($block_name, $block, $tax_name){
				if($term->taxonomy != $tax_name){
					return $term;
				}
				$term->{$block_name} = $block->get_term_fields($term->term_id);				
				return $term;
			});

		}

	}
}