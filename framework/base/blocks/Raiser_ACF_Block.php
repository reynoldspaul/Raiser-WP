<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_ACF_Block extends Raiser_Block_Base {

	public function boot(){

		$this->prepare_block_key();

		$this->prepare_location();

		$this->prepare_field_keys();

		$this->prepare_field_choices( $this->block_settings['fields'] );

		$this->render_hooks_callback();

	}	

	/*
	* Hook up to the admin
	*/ 
	public function hook_up(){

		$this->register_block_in_acf();

	}

	public function register_block_in_acf(){

		if( !class_exists('acf') ) { 
			return false;
		}

		acf_add_local_field_group($this->block_settings);

		$this->block = $this->block_settings;

	}	

	/*
	* Create block key
	*/
	public function prepare_block_key(){

		// make key based on the block name,
		if( !isset($this->block_settings['key']) ){
			$this->block_settings['key'] = $this->sanitize_title($this->block_name);
		}

		if( isset($this->block_settings['key_prefix']) ){
			$this->block_settings['key'] = $this->sanitize_title($this->block_settings['key_prefix']).'_'.$this->block_settings['key'];
		}

		// set block_id
		$this->block_id = $this->block_settings['key'];
	}

	/*
	* create unique field key from block name, and field name
	*/
	public function prepare_field_keys(){

		foreach( $this->block_settings['fields'] as $index=>$field ){

			if( !isset($field['name']) ){
				$field['name'] = $this->sanitize_title($field['label'] ?? 'not_set');
				$this->block_settings['fields'][$index]['name'] = $field['name'];
			} 
			

			// make field key if not set
			if( !isset($field['key']) ){
				$field['key'] = $this->sanitize_title($this->block_name).'_'.$this->sanitize_title($field['name']);
				$this->block_settings['fields'][$index]['key'] = $field['key'];
			}

			// flexible content
			// make sub_fields if set as other block classes
			if( isset($field['type']) && $field['type'] == 'flexible_content' ){
				foreach( $field['layouts'] as $layout_key=>$layout ){

					if( !isset($layout['sub_fields']) ){
						continue;
					}

					// another block class
					if( !is_array($layout['sub_fields']) && is_callable($layout['sub_fields'], 'init') ){
						// get the fields form the class (dont hook_up)
						$block = new $layout['sub_fields']([],[],false);
						$layout['sub_fields'] = $block->block_settings['fields'];
						$this->block_settings['fields'][$index]['layouts'][$layout_key]['sub_fields'] = $block->block_settings['fields'];

					}					

					// array to define the block
					foreach( $layout['sub_fields'] as $sub_field_index=>$layout_field ){
						// make field key if not set
						if( !isset($layout_field['key']) ){
							$this->block_settings['fields'][$index]['layouts'][$layout_key][$sub_field_index]['key'] = $this->sanitize_title($this->block_name).'_'.$this->sanitize_title($layout['name']).'_'.$this->sanitize_title($layout_field['name']);
						}							
					}
					

				}
			}

			// conditoinal logic fields
			if( isset($field['conditional_logic']) ){
				foreach( $field['conditional_logic'] as $conditional_logic_groups_index=>$conditional_logic_groups ){
					foreach( $conditional_logic_groups as $conditional_logic_group_index=>$conditional_logic_group ){
						if( isset($conditional_logic_group['field_name']) ){
							$field['conditional_logic'][$conditional_logic_groups_index][$conditional_logic_group_index]['field'] = $this->sanitize_title($this->block_name).'_'.$conditional_logic_group['field_name'];
							unset($field['conditional_logic'][$conditional_logic_groups_index][$conditional_logic_group_index]['field_name']);
						}
					}
				}
				$this->block_settings['fields'][$index] = $field;
			}

			// repaeter fields
			if( isset($field['type']) && ( $field['type'] == 'repeater' || $field['type'] == 'group' ) ){

				if( !isset($field['sub_fields']) ){
					continue;
				}				

				$field['sub_fields'] = $this->prepare_repeater_sub_field($field);
				$this->block_settings['fields'][$index] = $field;

			}
		}

	}

	public function prepare_repeater_sub_field($field){

		// array to define the block
		foreach( $field['sub_fields'] as $sub_field_index=>$sub_field ){

			// make field key if not set
			if( !isset($sub_field['key']) ){
				$sub_field['key'] = $field['key'].'_'.$this->sanitize_title($sub_field['name']);
				$field['sub_fields'][$sub_field_index]['key'] = $sub_field['key'];
				//$this->block_settings['fields'][$index]['sub_fields'][$sub_field_index]['key'] = sanitize_title($this->block_name).'_'.sanitize_title($field['name']).'_'.sanitize_title($sub_field['name']);
			}										

			if( $sub_field['type'] == 'repeater' || $sub_field['type'] == 'group' ){
				$field['sub_fields'][$sub_field_index]['sub_fields'] = $this->prepare_repeater_sub_field($sub_field);
				continue;
			}
				
		}	
		
		return $field['sub_fields'];

	}

	/*
	* Set location array for specefic cases
	*/ 
	public function prepare_location(){

		// options block
		if (strpos($this->block_settings['key'], '_options') !== false) {

			if( !isset($this->block_settings['menu_title']) ){
				$this->block_settings['menu_title'] = 'Theme Options';
			}

			if( !isset($this->block_settings['location']['parent_slug']) ){
				// create the options menu
				acf_add_options_page([
					'page_title' 		=> $this->block_settings['menu_title'],
					'menu_slug' 		=> sanitize_title($this->block_settings['menu_title'])//'theme-options',
				]);
			}

			acf_add_options_page([
				'page_title' 	=> $this->block_settings['title'],
				'menu_title'	=> $this->block_settings['title'],
				'menu_slug' 	=> $this->block_settings['key'],
				'parent_slug' 	=> $this->block_settings['location']['parent_slug'] ?? sanitize_title($this->block_settings['menu_title'])
			]);

			$this->block_settings['location'] = [
				[
					[
						'param' 	=> 'options_page',
						'operator' 	=> '==',
						'value' 	=> $this->block_settings['key'],						
					]
				]
			];

			// add key to field names ('cpt_options')
			foreach( $this->block_settings['fields'] as $index=>$field ){
				$name = isset($field['name']) ? $field['name'] : $field['label'];
				$this->block_settings['fields'][$index]['name'] = $this->sanitize_title($this->block_settings['key']).'_'.$this->sanitize_title($name);
			}			

			return;
		}

	}

	/*
	* adds a choices item to field if method get_[field_name]_choices exists on the class
	*/
	public function prepare_field_choices($fields,$name=''){
		foreach( $fields as $index=>$field ){

			$this_name = $name == '' ? $field['name'] : $name.'_'.$field['name'];	
			
			if( $field['type'] == 'repeater' || $field['type'] == 'group' ){
				$fields[$index]['sub_fields'] = $this->prepare_field_choices($field['sub_fields'], $this_name);
			} else {
				$fields[$index] = $this->field_choices($field, $this_name);
			}

		}
		$this->block_settings['fields'] = $fields;
		return $fields;
	}

	public function field_choices($field, $name=''){
		// options block - remove the prefix
		if (strpos($this->block_settings['key'], '_options') !== false) {
			$prefix = $this->sanitize_title($this->block_settings['key']).'_';
			if (substr($name, 0, strlen($prefix)) == $prefix) {
			    $name = substr($name, strlen($prefix));
			}
		}
		$choice_method = 'get_'.$name.'_choices';
		if( method_exists($this, $choice_method)){
			$field['choices'] = $this->$choice_method();
		}		
		return $field;
	}


	public function get_fields($post_id=null){
		if( !class_exists('acf') ) { 
			return [];
		}
		if (strpos($this->block_settings['key'], '_options') !== false) {
			$post_id = 'option';
		}
		$data = [];
		foreach( $this->block_settings['fields'] as $field ){	
			$data[ $field['name'] ] = get_field( $field['name'], $post_id );
		}
		return $data;
	}

	public function get_term_fields($term_id){
		$data = [];
		foreach( $this->block_settings['fields'] as $field ){	
			$data[ $field['name'] ] = get_term_meta( $term_id, $field['name'], true );
		}
		return $data;
	}	

	public function render_hooks_callback(){
		foreach( $this->render_hooks as $hook => $template ){
			add_action( $hook, function() use ($template){
				$block = [];
				$block['block_name'] = $this->block_name;
				$block['acf_data'] = $this->get_fields();
			    // Include template.
			    if( file_exists($template) ) {
				    include( $template );
			    }					
			});
		}
	}

	public function sanitize_title($val){
		return sanitize_title($val);
		//return sanitize_title(str_replace('-','_',$val));
	}
}