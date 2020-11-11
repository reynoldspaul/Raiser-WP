<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// REMOVE WP EMOJI
if( rw_config('theme.wp_head.remove_wp_emoji') ) {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	add_filter( 'emoji_svg_url', '__return_false' );
}

// remove rest api output
if( rw_config('theme.wp_head.remove_rest_api_header') ){
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );	
}

// weblog client link
if( rw_config('theme.wp_head.remove_weblog_link') ){
	remove_action('wp_head', 'rsd_link');
}

// wlwmanifest_link
if( rw_config('theme.wp_head.remove_wlwmanifest_link') ){
	remove_action('wp_head', 'wlwmanifest_link');
}

// wp generaotr
if( rw_config('theme.wp_head.remove_wp_generator') ){
	remove_action('wp_head', 'wp_generator');
}

// permalink
if( rw_config('theme.permalink_structure') != null && is_admin() ){
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( rw_config('theme.permalink_structure') );
}

// styles
if( rw_config('theme.stylesheets') != null ){
	add_action( 'wp_enqueue_scripts', function(){
		foreach( rw_config('theme.stylesheets') as $name=>$style ){
			wp_enqueue_style( $name, $style['src'], $style['deps'] ?? [], $style['ver'] ?? false, $style['media'] ?? 'all' );
		}
	});
}

// scripts
if( rw_config('theme.scripts') != null ){
	add_action( 'wp_enqueue_scripts', function(){
		foreach( rw_config('theme.scripts') as $name=>$script ){
			wp_enqueue_script( $name, $script['src'], $script['deps'] ?? [], $script['ver'] ?? false, $script['in_footer'] ?? false );

			if( isset($script['localize']) ){
				foreach( $script['localize'] as $handle => $localize ){
					wp_localize_script( $name, $handle, $localize );
				}
			}
			if( isset($script['inline']) ){
				foreach( $script['inline'] as $inline ){
					wp_add_inline_script( $name, $inline['data'], isset($inline['position']) ? $inline['position'] : 'after' );
				}
			}			
		}
	});

	add_filter( 'script_loader_tag', function ( $tag, $handle ) {    
	    if( is_admin() ) {
	        return $tag;
	    }

	    $add = '';
	    if (strpos($handle, 'async') !== false) {
	    	$add .= ' async ';
	    }
	    if (strpos($handle, 'defer') !== false) {
	    	$add .= ' defer ';
	    }    
	    return str_replace( 'src', $add.'src', $tag );

	}, 10, 2 );

}

// menues
if( rw_config('theme.menus') != null ){
	register_nav_menus( rw_config('theme.menus') );
}

// custom logo
if( rw_config('theme.custom_login_image') != null ){

	add_action( 'login_enqueue_scripts', function(){
	    ?>
	    <style type="text/css">
	        body.login div#login h1 {
	        	<?php if(isset(rw_config('theme.custom_login_image')['background-color'])){?>
	            background-color: <?php echo rw_config('theme.custom_login_image')['background-color'];?>;
	        	<?php } ?> 
	        }
	        body.login div#login h1 a {
	        	<?php if(isset(rw_config('theme.custom_login_image')['src'])){?>
	            background-image: url('<?php echo rw_config('theme.custom_login_image')['src'];?>');
	            <?php } ?>
	            background-position: center;
	            <?php if(isset(rw_config('theme.custom_login_image')['size'])){?>
	            background-size: <?php echo rw_config('theme.custom_login_image')['size'];?>;
	            <?php } ?>
	            margin: 0 auto 0;
	            height: 80px;
	            width: 200px;
	        }
	    </style>
	    <?php
	});
	function rw_login_logo_url() {
	    return home_url();
	}
	add_filter( 'login_headerurl', 'rw_login_logo_url' );
	function rw_login_logo_url_title() {
	    return get_bloginfo( 'name', 'display' );
	}
	add_filter( 'login_headertext', 'rw_login_logo_url_title' );
}

// custom colors
if( rw_config('theme.editor_custom_colors') != null ){

	add_filter('tiny_mce_before_init', function($init){

		$custom_colors = '';

		foreach( rw_config('theme.editor_custom_colors') as $name=>$hex){
			$custom_colors .= '"'.str_replace('#','',$hex).'", "'.$name.'",';
		}
		$custom_colors = substr($custom_colors, 0, -1);
	    // build color grid default+custom colors
	    $init['textcolor_map'] = '['.$custom_colors.']';

	    // change the number of rows in the grid if the number of colors changes
	    // 8 swatches per row
	    //$init['textcolor_rows'] = 1;

	    return $init;
	});
}


// Register image sizes
if ( rw_config('theme.image_sizes') != null) {
	foreach ( rw_config('theme.image_sizes') as $option) {
		add_image_size( $option[0], $option[1], $option[2], $option[3] ?? false);
	}
}


// Register Widget Areas
if ( rw_config('theme.widgets') != null) {
	foreach ( rw_config('theme.widgets') as $widget) {
		register_sidebar( $widget );
	}
}

// Raiserweb Load More
if( rw_config('theme.raiser-wp.load-more') == true ) {
	new Raiser_Load_More;
}

