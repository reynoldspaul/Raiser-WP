<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_Config {

    const THEME_CONFIG_FOLDER = 'config';

	protected $items = [];

    public function __construct(){
    	$this->init();
    }

    /*
    * load all files in config folder and pass to items array
    */
    public function init(){
        foreach (glob(get_template_directory()."/".self::THEME_CONFIG_FOLDER."/*.php") as $filename){
            $this->items[str_replace('.php', '', basename($filename))] = include $filename;
        }
    }

    /*
    * Get from dot notation
    */
    public function get($key){
    	$array = $this->items;

    	if( array_key_exists($key,$array) ){
    		return $array[$key];
    	}
        foreach (explode('.', $key) as $segment) {
            if ( is_array($array) && array_key_exists($segment, $array)) {
            	$array = $array[$segment];
            } else {
            	return null;
            }
        }
        return $array;	
    }
}