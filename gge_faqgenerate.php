<?php 

/*
    Plugin Name: Ghostly Games - Generate FAQ
    Description: Generate FAQ with Google Arch
    Version: 1.0
    Author: Mike Mikic
    Author URI: https://www.ghostlygames.com.au
*/

/**
 * Define the plugin version
 */

 
define("GGE_FAQ_VERSION", "1.0.5");
define( 'GGE_FAQ_PATH', plugin_dir_path( __FILE__ ));
define( 'GGE_FAQ_URL_PATH', plugin_dir_url( __FILE__ ));

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

if (!class_exists('GgeFaqGenerate')) {
    class GgeFaqGenerate {
        function __construct() {

        }

        public function initialize()
        {
            //Grab the functions file for utils
            include_once GGE_FAQ_PATH . 'inc/gge_functions.php';
            //create admin page - and register post type
            include_once GGE_FAQ_PATH . 'admin/admin.php';
            $admin = new GgeFaqAdmin();

            //Create the front end content
            //This will have a shortcode to send over to the page to indicate which page can view the faqs
            include_once GGE_FAQ_PATH . 'frontend/front-end-faq.php';
            $faq_front_end = new FrontEndFAQ();

        }


    }

    function gge_faq() {
		global $gge_faq;

		// Instantiate only once.
		if ( ! isset( $gge_faq ) ) {
			$gge_faq = new GgeFaqGenerate();
			$gge_faq->initialize();
		}
		return $gge_faq;
	}

	// Instantiate.
	gge_faq();
}

//initialise 
$ggeFaqGenerate = new GgeFaqGenerate();