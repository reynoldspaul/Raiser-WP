<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_CMB2_Block extends Raiser_Block_Base {

	public function boot(){

		$this->prepare_block_object_assignment();		

		$this->helpers_and_callbacks();

		add_action( 'cmb2_admin_init', [$this,'register_admin_block'] );
		add_action( 'cmb2_init', [$this,'register_block'] );
	}

	public function register_admin_block(){

		$block = new_cmb2_box($this->block_settings);

		foreach( $this->fields as $index=>$field ){
			$block->add_field($field);

			// register hooks
			$this->action_hooks($field);			
		}

		$this->block = $block;

	}	

	public function register_block(){
		$this->block = $this->block_settings;
	}

	public function prepare_block_id(){

		if( !isset($this->block_settings['post_type']) ){
			$this->block_settings['post_type'] = 'post';
		}
		if( !isset($this->block_settings['type']) ){
			$this->block_settings['type'] = 'blog';
		}

		if( !isset($this->block_settings['id']) ){
			$this->block_settings['id'] = sanitize_title($this->block_settings['post_type']) . '_' . sanitize_title($this->block_settings['type']) . '_' . sanitize_title($this->block_name);
		}

		// admin option block
		if( $this->block_settings['type'] == 'options' ){
			$this->block_settings['id'] = $this->block_settings['post_type'] .'_options';
		}

		// add leading underscore
		$this->block_settings['id'] = '_'.$this->block_settings['id'];

	}

	public function prepare_block_object_assignment(){

		if( isset($this->block_settings['object_types']) ){
			return;
		}

		// assign object type (either 'page', or the cpt name)
		$this->block_settings['object_types'] = [$this->block_settings['post_type']];

		// assign show on filter for pages
		// this is either a page template (.php file) or a page ID
		if($this->block_settings['post_type'] == 'page' ){

			if( strpos($this->block_settings['type'], '.php') != false ){ 

				// php template file
				$this->block_settings['show_on'] = ['key'=>'page-template', 'value'=>[$this->block_settings['type']]];

			} else { 

				// get page id
				if( isset(Raiser_Block_Base::$wp_db_page_options[$this->block_settings['type']]) ){
					$key = Raiser_Block_Base::$wp_db_page_options[$this->block_settings['type']];
				} else {
					throw new \Exception($this->block_settings['type'].' - This page type isn\'t supported');
				}
				$this->block_settings['show_on'] = ['key'=>'id','value'=>[get_option($key)]];	

			}
		}

		// options block
		if( $this->block_settings['type'] == 'options' ){

			$this->block_settings['hookup'] = false;
			$this->block_settings['cmb_styles'] = false;
			$this->block_settings['show_on'] = [
				'key'   => 'options-page',
				'value' => [ $this->block_settings['id'] ]				
			];

			// register the menu
			new Raiser_CMB_Admin_Option_Base(['id'=>$this->block_settings['id'], 'post_type'=>$this->block_settings['post_type'], 'title'=>'Options']);
		}

		// unset these vars that are not used by CMB2
		unset($this->block_settings['type']);
		//unset($this->block_settings['post_type']);

	}

	public function action_hooks($field){

		if( !isset($field['action_args']) ){
			return;
		}

		$args = $field['action_args'];

		// this updateds wp taxonomy for a taxonomy saved field
		if(isset($args['update_wp_taxonomy'])){
			add_action( 'save_post', function($post_id, $post )use($field){
				if( wp_is_post_revision( $post ) ) {
					return;
				}

				$post_type = get_post_type( $post );

				if( 'trash' === $post->post_status ) {
					// delete
					return;
				}

				// get the saved id
				$term_id = get_post_meta( $post_id, $field['id'], true );
				wp_set_post_terms($post_id,[$term_id],$field['action_args']['taxonomy']);

			}, 100, 2 );
		}
	}	

	public function helpers_and_callbacks(){

		// for a group repeater, 
		// set the after_group to make_group_title_a_field
		// and set group_title_field to the id of the field you wish to make display
		if( !function_exists('make_group_title_a_field') ){
			function make_group_title_a_field($block_settings) {

				add_action( is_admin() ? 'admin_footer' : 'wp_footer', function()use($block_settings) {

					?>
					<script type="text/javascript">
					jQuery( function( $ ) {
						var $block_settings = $( document.getElementById( 'cmb2-metablock_settings-<?php echo $block_settings['render_row_cb'][0]->prop('id');?>' ) );
						var replaceTitles = function() {
							$block_settings.find( '.cmb-group-title' ).each( function() {
								var $this = $( this );
								var txt = $this.next().find( '[id$="<?php echo $block_settings['group_title_field'];?>"]' ).val();
								var rowindex;
								if ( ! txt ) {
									txt = $block_settings.find( '[data-grouptitle]' ).data( 'grouptitle' );
									if ( txt ) {
										rowindex = $this.parents( '[data-iterator]' ).data( 'iterator' );
										txt = txt.replace( '{#}', ( rowindex + 1 ) );
									}
								}
								if ( txt ) {
									$this.text( txt );
								}
							});
						};
						var replaceOnKeyUp = function( evt ) {
							var $this = $( evt.target );
							var id = '<?php echo $block_settings['group_title_field'];?>';
							if ( evt.target.id.indexOf(id, evt.target.id.length - id.length) !== -1 ) {
								$this.parents( '.cmb-row.cmb-repeatable-grouping' ).find( '.cmb-group-title' ).text( $this.val() );
							}
						};
						$block_settings
							.on( 'cmb2_add_row cmb2_remove_row cmb2_shift_rows_complete', replaceTitles )
							.on( 'keyup', replaceOnKeyUp );
						replaceTitles();
					});
					</script>
					<?php
				});

			}
		}

		// makes wysiwyg fields of height 350px in group repeater fields
		add_action('after_wp_tiny_mce', function() { 
	        ?>
	        <script type="text/javascript">
	        jQuery(document).ready( function($){
	            setTimeout( function(){
	                $('.cmb-repeatable-group .mce-edit-area iframe').each( function(){
	                    $(this).css('height', '350px');
	                });
	            },100)
	        });
	        </script>
	        <?php
		});

	}	

	// gets taxonomy for select field (instead of CMB2 taxonomy_select https://github.com/CMB2/CMB2/wiki/Tips-&-Tricks#a-dropdown-for-taxonomy-terms-which-does-not-set-the-term-on-the-post)
	public function get_taxonomy_options($field){

		$args = $field->args( 'get_terms_args' );
		$args = is_array( $args ) ? $args : array();

		$args = wp_parse_args( $args, array( 'taxonomy' => 'category' ) );

		$taxonomy = $args['taxonomy'];

		$terms = (array) get_terms( $args );

		$term_options = array();
		$term_options[] = 'None';
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_options[ $term->term_id ] = $term->name;
			}
		}

		return $term_options;
	}		

	public function get_fields($post_id){
		$data = [];
		foreach( $this->fields as $field ){	
			$data[ $field['id'] ] = get_post_meta( $post_id, $field['id'], true );
		}
		return $data;
	}

	public function get_term_fields($term_id){
		$data = [];
		foreach( $this->fields as $field ){	
			$data[ $field['id'] ] = get_term_meta( $term_id, $field['id'], true );
		}
		return $data;
	}


}