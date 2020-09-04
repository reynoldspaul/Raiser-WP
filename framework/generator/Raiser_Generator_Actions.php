<?php

require RAISER_DIR . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Raiser_Generator_Actions {

	public function get_application(){

		$application = new Application();
		$application->setAutoExit(false);

		$application->add(new makeCPT());
		$application->add(new makePage());
		$application->add(new makeTax());
		$application->add(new makeBlock());
		$application->add(new initConfig());
		$application->add(new makeTheme());

		return $application;

	}

	public function actions(){

		add_action( 'admin_post_raiser_command_config', array($this, 'config' ) );
		add_action( 'admin_post_raiser_command_cpt', array($this, 'cpt' ) );
		add_action( 'admin_post_raiser_command_tax', array($this, 'tax' ) );
		add_action( 'admin_post_raiser_command_block', array($this, 'block' ) );
		add_action( 'admin_post_raiser_command_gblock', array($this, 'gblock' ) );
		add_action( 'admin_post_raiser_command_new_theme', array($this, 'new_theme' ) );
	}

	// redirect back
	private function redirect( $success = true ) {

		// To make the Coding Standards happy, we have to initialize this.
		if ( ! isset( $_SERVER['HTTP_REFERER']  ) ) { // Input var okay.
			$_SERVER['HTTP_REFERER']  = wp_login_url();
		}

		// Sanitize the value of the $_POST collection for the Coding Standards.
		$url = sanitize_text_field(
			wp_unslash( $_SERVER['HTTP_REFERER'] ) // Input var okay.
		);

		$url = ( !$success ) ?
			add_query_arg( 'error', 'true', remove_query_arg( 'settings-saved', $url ) ) :
			add_query_arg( 'success', 'true', $url );

		wp_safe_redirect( urldecode( $url ) );
		exit;
	}	

	private function check_nonce($action,$name){
		// check nonce
		if( !isset( $_POST[$name] ) || !wp_verify_nonce( $_POST[$name], $action) ) {
			echo "Error";
			exit;
		}		
	}

	public function new_theme(){

		$this->check_nonce('raiser_command_new_theme', 'new_theme');

		$application = $this->get_application();

		$input = [
            'command' => 'make:theme',
            'theme_name' => sanitize_text_field($_POST['theme_name']),
		];	

		if( isset($_POST['flags']) ){
			foreach( $_POST['flags'] as $flag=>$val ){
				if( $val == 'true' ){
					$input['--'.$flag] = true;
					continue;
				}
			}
		}
		
        // run the command
        $output = new BufferedOutput();
        $application->run(new ArrayInput($input), $output);

        $content = $output->fetch();

        set_transient('raiser_message', nl2br($content), 45 );
        $this->redirect();		

	}

	public function config(){

		$this->check_nonce('raiser_command_config', 'config');

		$application = $this->get_application();

		$input = [
            'command' => 'init:config',
		];		

		$configs = [];
		if( isset($_POST['configs']) ){
			foreach( $_POST['configs'] as $val ){
				$configs[] = sanitize_text_field($val);
			}
		}

		if( !empty($configs)){
			$input['--configs'] = $configs;
		}		

        // run the command
        $output = new BufferedOutput();
        $application->run(new ArrayInput($input), $output);

        $content = $output->fetch();

        set_transient('raiser_message', nl2br($content), 45 );
        $this->redirect();		

	}


	public function cpt(){

		$this->check_nonce('raiser_command_cpt', 'cpt');

		$application = $this->get_application();

		$input = [
            'command' => 'make:cpt',
            'post_type_name' => sanitize_text_field($_POST['post_type_name']),
		];

		if( isset($_POST['flags']) ){
			foreach( $_POST['flags'] as $flag=>$val ){
				if( $val == 'true' ){
					$input['--'.$flag] = true;
					continue;
				}
			}
		}

        // run the command
        $output = new BufferedOutput();
        $application->run(new ArrayInput($input), $output);

        $content = $output->fetch();

        set_transient('raiser_message', nl2br($content), 45 );
        $this->redirect();

	}


	public function tax(){

		$this->check_nonce('raiser_command_tax', 'tax');

		$application = $this->get_application();

		$input = [
            'command' => 'make:tax',
            'tax_name' => sanitize_text_field($_POST['tax_name']),
		];

		$object_types = [];
		if( isset($_POST['object_types']) ){
			foreach( $_POST['object_types'] as $val ){
				$object_types[] = sanitize_text_field($val);
			}
		}
		if( isset($_POST['object_type_custom']) && $_POST['object_type_custom'] != '' ){
			$object_type_custom = explode(',', $_POST['object_type_custom']);
			$object_types = array_merge($object_type_custom,$object_types);
		}

		if( !empty($object_types)){
			$input['--object_types'] = $object_types;
		}

        // run the command
        $output = new BufferedOutput();
        $application->run(new ArrayInput($input), $output);

        $content = $output->fetch();

        set_transient('raiser_message', nl2br($content), 45 );
        $this->redirect();

	}

	public function block(){

		$this->check_nonce('raiser_command_block', 'block');

		$application = $this->get_application();

		$input = [
            'command' => 'make:block',
            'block_name' => sanitize_text_field($_POST['block_name']),
		];

        // run the command
        $output = new BufferedOutput();
        $application->run(new ArrayInput($input), $output);

        $content = $output->fetch();

        set_transient('raiser_message', nl2br($content), 45 );
        $this->redirect();

	}

	public function gblock(){

		$this->check_nonce('raiser_command_gblock', 'gblock');

		$application = $this->get_application();

		$input = [
            'command' => 'make:block',
            'block_name' => sanitize_text_field($_POST['block_name']),
            '--type'	=> 'gacf'
		];

        // run the command
        $output = new BufferedOutput();
        $application->run(new ArrayInput($input), $output);

        $content = $output->fetch();

        set_transient('raiser_message', nl2br($content), 45 );
        $this->redirect();

	}


}
