<?php
/**
 * Plugin Name: Search Report
 * Plugin URI: 
 * Description: It allows you to track what users are searching in your WooCommerce Store or WordPress Blog / Site.
 * Version: 1.0.0
 * Author: PluginFence
 * Author URI: 
 * Text Domain: pf-search-report
 * WC requires at least: 2.6.0
 * WC tested up to: 4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wpdb;
/**
 * Define search report root file.
 */
if ( ! defined( 'PF_SEARCH_REPORT_ROOT_FILE' ) ) {
	define( 'PF_SEARCH_REPORT_ROOT_FILE', __FILE__ );
}

if ( ! defined( 'PF_SEARCH_REPORT_ROOT_DIR' ) ) {
	define( 'PF_SEARCH_REPORT_ROOT_DIR', __DIR__ );
}

if ( ! defined( 'PF_SEARCH_REPORT_TABLE' ) ) {
	define( 'PF_SEARCH_REPORT_TABLE', $wpdb->prefix. "pf_search_history_report" );
}

if(! defined("PF_SEARCH_REPORT_SETTINGS_MENU_SLUG") ) {
    define( "PF_SEARCH_REPORT_SETTINGS_MENU_SLUG", "pf_search_report");
}

if(! defined("PF_SEARCH_REPORT_TEXT_DOMAIN") ) {
    define( "PF_SEARCH_REPORT_TEXT_DOMAIN", "pf-search-report");
}

if( ! class_exists("Pluginfence_Search_Report_Create_Table") ) {
    require_once "Pluginfence_Search_Report_Create_Table.php";
}


register_activation_hook( __FILE__, array(new Pluginfence_Search_Report_Create_Table(), "pf_search_report_create_table") );

if(! class_exists("PluginFence_Search_Report") ) {
    class PluginFence_Search_Report {
        /**
		 * Plugin settings.
		 */
        private static $settings;
        private $DATE_TIME_FORMAT = "timestamp";
        private $last_searched_keyword;
        private $last_searched_keyword_time;

        public function __construct() {
            add_action('admin_enqueue_scripts', array($this, "my_admin_scripts"));
            
            // Don't add the searched keyword on an administrative interface page
            if(! is_admin() ) {
                add_filter("get_search_query", array($this, "searched_keyword") );
            }

            if(! class_exists("PluginFence_Dependencies")) {
                require_once "dependencies/PluginFence_Dependencies.php";
            }

            if(is_admin()) {
                if(! class_exists("Pluginfence_Search_Report_Settings") ) {
                    require_once "includes/Pluginfence_Search_Report_Settings.php";
                }
                add_action( "admin_menu", array(new Pluginfence_Search_Report_Settings(), "Add_Settings_Menu") );
            }
            add_filter( 'plugin_action_links_' . plugin_basename( PF_SEARCH_REPORT_ROOT_FILE ), __CLASS__. '::plugin_action_links' );
        }

        /**
		 * Plugin action link.
		 */
		public static function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=pf_search_report' ) . '">' . __( 'Report', PF_SEARCH_REPORT_TEXT_DOMAIN )
			);
			return array_merge( $plugin_links, $links );
		}

        public function my_admin_scripts() {
            // wp_register_script('pf_load_angular', plugins_url('asset/js/angular.min.js', __FILE__));
            // wp_register_script('pf_load_angular_animate', plugins_url('asset/js/angular-animate.min.js', __FILE__));
            wp_register_script('pf_load_scripts', plugins_url('asset/js/script.js', __FILE__));
            wp_register_style( "pf_report_history", plugins_url('asset/css/Pluginfence_Search_Report_History.css', __FILE__) );

            wp_enqueue_style( 'pf_report_history' );
            // wp_enqueue_script('pf_load_angular');
            // wp_enqueue_script('pf_load_angular_animate');
            wp_enqueue_script('pf_load_scripts');
        }

        public function searched_keyword($searched_keyword) {
            if(gettype($searched_keyword) == "string" && strlen($searched_keyword) ) {
                // To prevnt the multiple entries for one search, filter is getting called multiple times
                if($this->last_searched_keyword != $searched_keyword || $this->last_searched_keyword_time - current_time( $this->DATE_TIME_FORMAT ) > 90 ) {
                    $this->last_searched_keyword_time = current_time( $this->DATE_TIME_FORMAT );
                    $this->last_searched_keyword = $searched_keyword;
                    $this->AddUpdateSearchedKeyword($searched_keyword);
                }
            }
            return $searched_keyword;
        }

        /**
		 * Get Plugin settings.
		 */
		public static function get_plugin_settings(){
			! empty(self::$settings) || self::$settings = get_option('pvalley_user_role_based_shipping', array());
			return self::$settings;
        }

        /**
         * Add or update the searched keyword to db.
         */
        private function AddUpdateSearchedKeyword($searched_keyword) {
            global $wpdb;

            // Sanitize for security purpose
            $searched_keyword = sanitize_text_field( $searched_keyword );
            // Fetch query
            $get_query = "SELECT * FROM ".PF_SEARCH_REPORT_TABLE." where searched_text = '".$searched_keyword."'";

            $result = $wpdb->get_results($get_query);
            
            if( sizeof($result) > 0) {
                $wpdb->update(
                    PF_SEARCH_REPORT_TABLE,
                    array(
                        "search_count"  => $result[0]->search_count + 1
                    ),
                    array(
                        "id" => $result[0]->id
                    )
                );
            } else {
                $wpdb->insert(
                    PF_SEARCH_REPORT_TABLE,
                    array(
                        "searched_text" =>  $searched_keyword,
                    )
                );
            }
        }

        /**
         * Get searched keywords history.
         */
        public static function Get_Searched_Keywords($limit = 100) {
            global $wpdb;
            $get_query = "SELECT id, UNIX_TIMESTAMP(last_searched_time) as last_searched_time, searched_text, search_count FROM ".PF_SEARCH_REPORT_TABLE. " ORDER BY last_searched_time DESC LIMIT $limit";
            $result = $wpdb->get_results($get_query);
            return $result;
        }
    }
}

new PluginFence_Search_Report();