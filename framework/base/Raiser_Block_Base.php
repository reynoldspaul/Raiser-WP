<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_Block_Base {

	public $block_name;
	public $block_id;

	public $block_settings = [];

	public $location = [];

	public $fields = [];

	public $render_hooks = [];

	public $block;

	/*
	* used in get_option() to retrieve wordpress page ID of the page
	*/
	public static $wp_db_page_options = [
		'front-page'				=> 'page_on_front',
		'privacy-policy-page' 		=> 'wp_page_for_privacy_policy',
		'woocommerce-shop-page' 	=> 'woocommerce_shop_page_id',
	];

	public function __construct($block_settings=[],$location=[],$hook_up=true){

		// init
		if( method_exists($this, 'init') ){
			$this->init();
		}

		// merge arrays passed into class
		$this->block_settings = array_merge($block_settings,$this->block_settings);
		$this->location = array_merge($location,$this->location);
		if( isset($this->block_settings['block_name']) ) {
			$this->block_name = $this->block_settings['block_name'];
		}

		// assign arrays to block_settings
		if( !isset($this->block_settings['fields']) ){
			$this->block_settings['fields'] = $this->fields;
		}
		if( !isset($this->block_settings['location']) ){
			$this->block_settings['location'] = $this->location;
		}

		// boot
		if( method_exists($this, 'boot') ){
			$this->boot();
		}

		// hooks
		if( method_exists($this, 'hooks') ){
			$this->hooks();
		}

		// hookup to admin
		if( method_exists($this, 'hook_up') && $hook_up ){
			$this->hook_up();
		}

	}

}