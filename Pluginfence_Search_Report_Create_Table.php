<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists("Pluginfence_Search_Report_Create_Table") ) {
    class Pluginfence_Search_Report_Create_Table {
        /**
         * Create table for keyword search.
         */
        function pf_search_report_create_table() {
            global $wpdb;
            $table_name = PF_SEARCH_REPORT_TABLE;
            $charset_collate = $wpdb->get_charset_collate();
    
            $sql = "CREATE TABLE $table_name (
                    id int NOT NULL AUTO_INCREMENT,
                    last_searched_time timestamp NOT NULL,
                    searched_text varchar(55) NOT NULL,
                    search_count int DEFAULT 1 NOT NULL,
                    search_type text(30) DEFAULT '' NOT NULL,
                    UNIQUE KEY id (id),
                    UNIQUE KEY searched_text (searched_text)
                    ) $charset_collate";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
    }
}