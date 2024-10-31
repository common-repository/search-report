<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists('Pluginfence_Search_Report_Settings') ) {
    class Pluginfence_Search_Report_Settings {

        public function Add_Settings_Menu() {
            add_menu_page(
                Pluginfence_Search_Report_Settings::Get_Page_Title(),
                "Search Report",
                "manage_options",
                PF_SEARCH_REPORT_SETTINGS_MENU_SLUG,
                array($this, "html")
            );
        }

        public function html() {
            $result = PluginFence_Search_Report::Get_Searched_Keywords();
            $content = "<table class='pf_search_report_history'>";
            $content .=  "<tr>";
            $content .=      "<th>Searched Keyword</th>";
            $content .=      "<th>Count</th>";
            $content .=      "<th>Last Searched Time</th>";
            $content .=  "</tr>";
            foreach ( $result as $keyword_search_data )   {
                $content .= "<tr>";
                $content .= "<td>".$keyword_search_data->searched_text."</td>";
                $content .= "<td>".$keyword_search_data->search_count."</td>";
                $content .= "<td>".wp_date(PluginFence_Dependencies::GetWpDateTimeFormat(), $keyword_search_data->last_searched_time )."</td>";
                $content .= "</tr>";
            }
            $content .= "</table>";
            echo $content;
        }

        public static function Get_Page_Title() {
            return "Search Report";
        }
    }
}