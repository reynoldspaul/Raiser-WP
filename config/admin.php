<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// admin bar
if( rw_config('admin.show_admin_bar') === false ) {
	add_filter('show_admin_bar', '__return_false');
}

// welcome panel
if( rw_config('admin.dashboard.show_welcome_panel') === false ) {
	remove_action('welcome_panel', 'wp_welcome_panel');
}

// clean up dashboard
if( rw_config('admin.dashboard.clean_dashboard') === true ) {
	function remove_dashboard_meta() {
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', '' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', '' );
		remove_meta_box( 'dashboard_primary', 'dashboard', '' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', '' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', '' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', '' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', '' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', '' );
		remove_meta_box( 'dashboard_activity', 'dashboard', '');
		remove_meta_box( 'dashboard_site_health', 'dashboard', '');
	}
	add_action( 'admin_init', 'remove_dashboard_meta' );
}

// shows specefic meta boxes
if( rw_config('admin.dashboard.show_meta_boxes') != null && !empty(rw_config('admin.dashboard.show_meta_boxes')) ) {

	$to_show = rw_config('admin.dashboard.show_meta_boxes');

	add_action( 'wp_dashboard_setup', function()use($to_show){
		global $wp_meta_boxes;

		$default = [];
		isset($wp_meta_boxes['dashboard']['normal']['core']) ? $default['normal'] = array_keys($wp_meta_boxes['dashboard']['normal']['core']) : null;
		isset($wp_meta_boxes['dashboard']['side']['core']) ? $default['side'] = array_keys($wp_meta_boxes['dashboard']['side']['core']) : null;

		foreach( $default as $position => $meta_boxes ){
			foreach($meta_boxes as $meta_box){
				if( !in_array($meta_box, $to_show) ){
					remove_meta_box($meta_box, 'dashboard', $position);
				}
			}
		}
	});
}

//gutenberg_editor
if( rw_config('admin.gutenberg_editor') === false ) {
	add_filter('use_block_editor_for_post', '__return_false');
	function rw_remove_block_css(){
		wp_dequeue_style( 'wp-block-library' );
	}
	add_action( 'wp_enqueue_scripts', 'rw_remove_block_css', 100 );	
}

// theme support
if( rw_config('admin.theme_support') != null ) {
	foreach( rw_config('admin.theme_support') as $key=>$option ){
		if( is_array($option) ){
			add_theme_support( $key, $option );
		} else {
			add_theme_support( $option );
		}
	}
}

// admin scripts
if( rw_config('admin.scripts') != null ){
	add_action( 'admin_enqueue_scripts', function(){
		foreach( rw_config('admin.scripts') as $name=>$src ){
			wp_enqueue_script( $name, $src );
		}
	});
}

// admin styles
if( rw_config('admin.stylesheets') != null ){
	add_action( 'admin_enqueue_scripts', function(){
		foreach( rw_config('admin.stylesheets') as $name=>$src ){
			wp_enqueue_style( $name, $src );
		}
	});
}