<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_Update_Plugin {

	public function __construct() {

		if ( ! class_exists( 'Puc_v4_Factory' ) ) {
			require RAISER_DIR.'/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';
		}

		add_action( 'plugins_loaded', array($this,'check'), 0 );
	}

	public function check(){

		Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/RaiserWeb/Raiser-WP',
			RAISER_DIR.'/raiser-wp.php',
			'raiser-wp'
		);		

	}
}
new Raiser_Update_Plugin();