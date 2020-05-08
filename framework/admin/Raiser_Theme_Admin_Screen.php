<?php

class Raiser_Theme_Admin_Screen {
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}	

	public function init() {

		if( rw_config('admin.theme_status_user_logins') != null ){
			$current_user = wp_get_current_user();
			if( !in_array($current_user->user_login, rw_config('admin.theme_status_user_logins'))){
				return;
			}
			add_action('admin_menu', array( $this, 'set_admin_menu') );
		}
	}

	public function set_admin_menu(){
		add_submenu_page( 'tools.php', 'Theme Status', 'Theme Status', 'manage_options', 'raiser-theme', array($this, 'admin_page_settings' ));
	}

	public function admin_page_settings(){
		include(  __DIR__ .'/view/admin.php' );
	}

}
new Raiser_Theme_Admin_Screen;