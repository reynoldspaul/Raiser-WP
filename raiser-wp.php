<?php
/**
 * Plugin Name:  Raiser WP
 * Plugin URI:   https://github.com/RaiserWeb/Raiser-WP
 * Description:  Raiser WP is a simple framework to assist with building custom WordPress themes.
 * Author:       raiserweb.com
 * Author URI:   raiserweb.com
 *
 * Version:      1.0.8
 *
 * Text Domain:  raiser-wp, metaboxes, blocks, fields, options, settings, theme, framework
 * Domain Path:  languages
 *
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * https://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Raiser_WP {
    
    const RAISER_THEME = '';
    const THEME_CONTENT_FOLDER = 'raiser-wp';

	public $config;

	public function __construct() {

        if ( !defined('RAISER_DIR') ) {
            define('RAISER_DIR', trailingslashit( dirname( __FILE__ ) ));
        }
        if ( !defined('__WP_TEMPLATE_DIR__') ) {
            define('__WP_TEMPLATE_DIR__', get_template_directory() );
        }
        
        // include files
        $this->include(RAISER_DIR.'/updater/Raiser_Update_Plugin.php');
        $this->include(RAISER_DIR.'/framework/base/Raiser_CPT_Base.php');
        $this->include(RAISER_DIR.'/framework/base/Raiser_Tax_Base.php');
        $this->include(RAISER_DIR.'/framework/base/Raiser_Block_Base.php');
        $this->include(RAISER_DIR.'/framework/base/blocks/Raiser_ACF_Block.php');
        $this->include(RAISER_DIR.'/framework/base/Raiser_Helpers.php');
        $this->include(RAISER_DIR.'/framework/generator/Raiser_Theme_Generator.php');
        
         // pro
        $this->include(RAISER_DIR.'/pro/raiser-pro.php');       

        // enquey admin scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts') );

        // load config
        include(RAISER_DIR.'/framework/config/Raiser_Config.php');
        $this->config = new Raiser_Config;

        // set gloabl vars
        if ( !defined('RAISER_THEME') ) {
            define('RAISER_THEME', $this->config->get('theme.theme_name'));
        }

        add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'init', array( $this, 'init' ) );
	}

    public function after_setup_theme(){
 

    }

    public function init(){  

        // load content-stucture files from theme       
        foreach(glob(get_template_directory()."/".self::THEME_CONTENT_FOLDER."/**/*.php") as $filename){
            include_once $filename;
        }  
        foreach(glob(get_template_directory()."/".self::THEME_CONTENT_FOLDER."/blocks/*.php") as $filename){
            if (strpos($filename, '/template.php') !== false) {
                continue;
            }
            include_once $filename;
        }          
        foreach(glob(get_template_directory()."/".self::THEME_CONTENT_FOLDER."/*.php") as $filename){
            include_once $filename;
        }             
 
        // load init config files
        foreach(glob(RAISER_DIR."/config/*.php") as $filename){
            include_once $filename;
        } 
 
    }

    public function admin_enqueue_scripts($hook){
        wp_enqueue_script('raiser-admin-js', plugin_dir_url(__FILE__) . 'framework/assets/raiser-admin.js');
    }

    private function include($file_path){
        if( file_exists($file_path) ) {
            include_once($file_path);
        }
    }

}
// the plugin
$Raiser_WP = new Raiser_WP;