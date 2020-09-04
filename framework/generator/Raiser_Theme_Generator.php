<?php

class Raiser_Theme_Generator {
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}	

	public function init() {

		// register
		include_once(RAISER_DIR.'/framework/generator/Raiser_Generator_Actions.php');
		$Raiser_Generator_Actions = new Raiser_Generator_Actions();
		$Raiser_Generator_Actions->actions();

		if( rw_config('admin.theme_generator_user_logins') != null && !empty(rw_config('admin.theme_generator_user_logins')) ){
			$current_user = wp_get_current_user();
			if( !in_array($current_user->user_login, rw_config('admin.theme_generator_user_logins') )){
				return;
			}
			add_action('admin_menu', array( $this, 'set_admin_menu') );
		} else {
			add_action('admin_menu', array( $this, 'set_admin_menu') );
		}
	}

	public function set_admin_menu(){
		add_submenu_page( 'tools.php', 'Raiser Generator', 'Raiser Generator', 'manage_options', 'raiser-theme-generator', array($this, 'admin_page_settings' ));
	}

	public function admin_page_settings(){

	    if( isset($_GET['tab']) ){
	        $tab = $_GET['tab'];
	    } else {
	        $tab = 'config';
	    }	

	    switch ($tab) {
	    	case 'config':
	    		include(  __DIR__ .'/view/config.php' );
	    		break;

	    	case 'cpt':
	    		include(  __DIR__ .'/view/cpt.php' );
	    		break;

	    	case 'tax':
	    		include(  __DIR__ .'/view/tax.php' );
	    		break;	    		

	    	case 'blocks':
	    		include(  __DIR__ .'/view/blocks.php' );
	    		break;	 

	    	case 'gblocks':
	    		include(  __DIR__ .'/view/gblocks.php' );
	    		break;	 	

	    	case 'theme':
	    		include(  __DIR__ .'/view/theme.php' );
	    		break;		    		    		
	    }

	}	

	public static function tabs(){
	    if( isset($_GET['tab']) ){
	        $tab = $_GET['tab'];
	    } else {
	        $tab = 'config';
	    }		
		?>
	    <h2 class="nav-tab-wrapper">
	    	<a href="?page=raiser-theme-generator&tab=config" class="nav-tab <?php echo $tab == 'config' ? 'nav-tab-active' : ''; ?>">Config</a>
	        <a href="?page=raiser-theme-generator&tab=cpt" class="nav-tab <?php echo $tab == 'cpt' ? 'nav-tab-active' : ''; ?>">Cutom Post Types</a>
	        <a href="?page=raiser-theme-generator&tab=tax" class="nav-tab <?php echo $tab == 'tax' ? 'nav-tab-active' : ''; ?>">Taxonomies</a>
	        <a href="?page=raiser-theme-generator&tab=blocks" class="nav-tab <?php echo $tab == 'blocks' ? 'nav-tab-active' : ''; ?>">Blocks</a>
	        <a href="?page=raiser-theme-generator&tab=gblocks" class="nav-tab <?php echo $tab == 'gblocks' ? 'nav-tab-active' : ''; ?>">Gutenberg Blocks</a>
	        <a href="?page=raiser-theme-generator&tab=theme" class="nav-tab <?php echo $tab == 'theme' ? 'nav-tab-active' : ''; ?>">New Theme</a>
	    </h2>  		
	    <?php
	}

	public static function display_notice(){

		if(isset($_GET['success']) ) { 
			$message = 'Done!';
			if( get_transient( 'raiser_message' ) ) { 
				$message = get_transient( 'raiser_message' );
			} ?>
		    <div class="notice notice-success is-dismissible">
		        <p><?php echo $message; ?></p>
		    </div>
		<?php delete_transient( 'raiser_message' ); }

		if( $error = get_transient( 'raiser_error' ) ) { ?>
	    <div class="notice notice-error is-dismissible">
	        <p><?php echo $error; ?></p>
	    </div>
		<?php delete_transient( 'raiser_error' ); } 
	}

}
new Raiser_Theme_Generator;