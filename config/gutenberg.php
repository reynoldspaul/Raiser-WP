<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// register blocks
if( rw_config('gutenberg.blocks') != null ) {
	foreach( rw_config('gutenberg.blocks') as $block ){
		if( is_subclass_of($block, 'Raiser_ACF_G_Block') ){
			new $block();
		} else {
			// throw new Exception('Gutenberg block not an instace of Raiser_ACF_G_Block');
		}
	}
}


if( rw_config('gutenberg.theme_support') != null ) {
	foreach( rw_config('gutenberg.theme_support') as $theme_support ){
		add_theme_support( $theme_support );
	}
}

if( rw_config('gutenberg.editor_styles') != null ) {
	add_action( 'enqueue_block_editor_assets', function(){
		if ( is_admin()) {
			wp_enqueue_style( 'editor-style', rw_config('gutenberg.editor_styles') );
		}
	});
}

if( rw_config('gutenberg.block_categories') != null ) {
	add_filter( 'block_categories', function( $categories, $post ){
		$block_categories = [];
		foreach( rw_config('gutenberg.block_categories') as $block_category ){
			$block_categories[] = [
				'slug' => sanitize_title($block_category),
				'title' => ucfirst( str_replace( '-', ' ', $block_category)),
			];
		}
		return array_merge(
			$categories,
			$block_categories
		);		
	}, 10, 2);
}


// Allow/Deny WordPress Gutenberg Core Blocks
if( rw_config('gutenberg.allowed_core_blocks') != null 
	|| rw_config('gutenberg.deny_core_widget_blocks') != null) {

	add_filter( 'allowed_block_types', function(){

		// get widget blocks and registered by plugins and theme
		$registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

		// This also includes WordPress core widget blocks, so remove any we have denied
		if( rw_config('gutenberg.deny_core_widget_blocks') != null) {
			foreach ( rw_config('gutenberg.deny_core_widget_blocks') as $widget_block) {
				unset( $registered_blocks[$widget_block] );
			}
		}
		// now $registered_blocks contains only blocks registered by our theme, and plugins
		// get the array keys
		$registered_blocks = array_keys( $registered_blocks );

		// If any standrd WordPress core allowed blocks are set
		if ( rw_config('gutenberg.allowed_core_blocks') != null) {
			// merge the whitelist with $registered_blocks;
			return array_merge( rw_config('gutenberg.allowed_core_blocks'), $registered_blocks );
		} else {
			// else, return the $registered blocks as is
			return $registered_blocks;
		}

	});
}
