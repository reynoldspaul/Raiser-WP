<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_Update_Plugin {

	public function __construct() {

		require RAISER_DIR.'/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';
		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/RaiserWeb/Raiser-WP',
			__FILE__,
			'raiser-wp'
		);

	}
}
new Raiser_Update_Plugin();