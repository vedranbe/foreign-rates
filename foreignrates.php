<?php
/**
 * Plugin Name: Foreign rates by Vedran Bejatovic
 * Plugin URI: https://github.com/vedranbe/foreign-rates
 * Description: Currency converter using exchangeratesapi.io API.
 * Version: 1.0
 * Author: Vedran Bejatovic
 * Author URI: https://github.com/vedranbe
 */

define( 'ER_PLUGIN', __FILE__ );

define( 'ER_PLUGIN_BASENAME', plugin_basename( ER_PLUGIN ) );
 
define( 'ER_PLUGIN_NAME', trim( dirname( ER_PLUGIN_BASENAME ), '/' ) );

define( 'ER_PLUGIN_DIR', untrailingslashit( dirname( ER_PLUGIN ) ) );

define( 'ER_PLUGIN_INCLUDES_DIR', ER_PLUGIN_DIR . '/includes' );

/**
 * Includes
 */
global $pagenow;
if ( ( 'admin.php' === $pagenow ) && ( 'foreign-rates-settings' === $_GET['page'] ) ) {
  include(ER_PLUGIN_DIR.'/get_data.php'); // Data load from external JSON
}
include(ER_PLUGIN_INCLUDES_DIR.'/settings.php'); // Admin settings
include(ER_PLUGIN_INCLUDES_DIR.'/in_posts.php'); // Display currencies in posts 
include(ER_PLUGIN_INCLUDES_DIR.'/shortcode.php'); // Shortcode output ex. [foreign_rates base="EUR" currencies="CAD,CHF,USD"]
include(ER_PLUGIN_INCLUDES_DIR.'/widget.php'); // Widget

/**
 * Load Styles and Scripts
 */
function foreign_rates_styles()
{
    if(!is_admin()){
      wp_register_style('bootstrap', plugin_dir_url( __FILE__ ).'assets/css/bootstrap.min.css', array(), '4.0', 'all');
      array_unshift(wp_styles()->queue, 'bootstrap');
    }
    wp_register_style('fr-style', plugin_dir_url( __FILE__ ).'assets/css/style.min.css', array(), '1.0', 'all');
    wp_enqueue_style('fr-style'); 
    
}
 
function foreign_rates_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
        wp_register_script('fr-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/scripts.min.js', array('jquery'), '1.0.0', 'all'); // Custom scripts
        wp_enqueue_script('fr-scripts'); // Enqueue Custom scripts!
    }
}

add_action('init', 'foreign_rates_styles'); // Add Theme Stylesheet
add_action('init', 'foreign_rates_scripts'); // Add Custom Scripts to wp_head


/**
 * Add default post categories and tags
 */
add_action('init', function () {

    // Update the default category 'uncategorized'
	wp_update_term(
        1,
		'category',
		array(
          'name'        => 'Finance',
		  'description'	=> '',
		  'slug' 		=> 'finance'
		)
    );

	$parent = term_exists( 'Finance', 'category' );
    $parent_id = $parent['term_id'];
    
    // Add subcategory
    wp_insert_term(
        'Currency', // the term 
        'category', // the taxonomy
        array(
            'slug' => 'currency',
            'parent'=> $parent_id
        )
    );
    
    // Add tags
    $currency = array('eur' => 'EUR', 'chf' => 'CHF');
    foreach ( $currency as $slug => $name ) {
        if( !term_exists( $name, 'post_tag' ) ) {
            wp_insert_term(
                $name,
                'post_tag',
                array(
                	'slug' => $slug
                )
            );
        }
    }
});



// Admin CSS
add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
  .wp-admin {
    label {
      display: inline-block;
      margin-right: 15px;
    }
    input {
      &:focus {
        border: 1px solid $blue;
      }
    }
    select {
      min-width: 200px;
      border: 1px solid $darkgrey;
      &:focus {
        border: 1px solid $blue;
      }
    }
  }
  #your-profile label+a, label {
    margin-right: 15px;
  }
  *:disabled {
    display: none;
  }
  
  </style>';
}

// Set up the activation redirect
register_activation_hook( __FILE__, 'foreignrates' );
add_action( 'admin_init', 'fr_activation_redirect' );

/**
 * Plugin activation callback. Registers option to redirect on next admin load.
 */
function foreignrates() {
	// Don't do redirects when multiple plugins are bulk activated
	if (
		( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) &&
		( isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) {
		return;
	}
	add_option( 'fr_activation_redirect', wp_get_current_user()->ID );
}

/**
 * Redirects the user after plugin activation.
 */
function fr_activation_redirect() {
	// Make sure it's the correct user
	if ( intval( get_option( 'fr_activation_redirect', false ) ) === wp_get_current_user()->ID ) {
		// Make sure we don't redirect again after this one
		delete_option( 'fr_activation_redirect' );
		wp_safe_redirect( admin_url( '/admin.php?page=foreign-rates-settings' ) );
		exit;
	}
}


/** 
 * Set Cronjob
 */
if (!wp_next_scheduled('load_api_hook')) {
  wp_schedule_event( time(), 'daily', 'load_api_hook' );
}

add_action ( 'load_api_hook', 'load_api_function' );


  
function load_api_function() {

  $url = 'https://api.exchangeratesapi.io/latest';

  $data = wp_remote_get( $url ,
      array('headers' => array( 'Token' => 'tokenkey')
      ));

  $jsonfile = $data['body'];

  global $wp_filesystem;

  if (empty($wp_filesystem)) {
      require_once (ABSPATH . '/wp-admin/includes/file.php');
      WP_Filesystem();
  }

  $file = ER_PLUGIN_DIR . '/json/data.json';

  $wp_filesystem->put_contents($file, $jsonfile);
}

function plugin_deactivation() {
  wp_clear_scheduled_hook( 'load_api_hook' );
}

register_deactivation_hook( __FILE__, 'plugin_deactivation' );


  
