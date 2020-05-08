<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Raiser_CMB_Admin_Option_Base {
	/**
 	 * Option key, and option page slug
 	 */
	private $key = '';
	/**
 	 * Options page metabox id
 	 */
	private $metabox_id = '';
	/**
	 * Options Page title
	 */
	protected $title = '';
	/**
	 * Options Page hook
	 */
	protected $options_page = '';
	
	/**
	 * This post Type
	 */
	protected $post_type = '';
	/**
	 * Holds an instance of the object
	 **/
	private static $instance = null;

	public function __construct( $args ) {
		if(isset($args['post_type'])){
			$this->post_type = $args['post_type'];
		}
		if(isset($args['title'])){
			$this->title = $args['title'];
		}
		$this->block_name = $args['post_type'];

		$post_type = $this->post_type;
		// Set our vars
		$this->key = $this->block_name;
		$this->metabox_id = $this->block_name;
		
		add_action( 'admin_init', array( &$this, 'init' ) );
		add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( &$this, 'add_options_page_notices' ) );
		//add_action( 'cmb2_admin_init', array( &$this, 'add_options_page_metabox' ) );
	}
	/**
	 * Register our setting to WP
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}
	/**
	 * Add menu options page
	 */
	public function add_options_page() {
		if( $this->post_type == 'post' ){
			$base_url = 'edit.php';
		} else {
			$base_url = 'edit.php?post_type=' . $this->post_type;
		}
		$this->options_page = add_submenu_page( $base_url, $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );
		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}
	/**
	 * Admin page markup. Mostly handled by CMB2
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	public function add_options_page_notices(){
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );
	}
	/**
	 * Register settings notices for display
	 *
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}
		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}
	/**
	 * Public getter method for retrieving protected/private variables
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		throw new Exception( 'Invalid property: ' . $field );
	}
}