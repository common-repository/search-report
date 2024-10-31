<?php

if(! class_exists("PluginFence_Dependencies")) {
    class PluginFence_Dependencies {
        /**
         * Constructor.
         */
        private static $ctr;
        /**
         * Wordpress date format.
         */
        private static $wp_date_format;
        /**
         * Wordpress time format.
         */
        private static $wp_time_format;
        /**
         * Private constructor so it cannot be instantiated.
         */
        private function __construct() {

        }

        public static function GetInstance() {
            if(! self::$ctr) {
                self::$ctr = new PluginFence_Dependencies();
            }
            return $ctr;
        }

        public static function GetWpDateFormat() {
            if(! self::$wp_date_format) {
                self::$wp_date_format = get_option('date_format');
            }
            return self::$wp_date_format;
        }

        public static function GetWpTimeFormat() {
            if(! self::$wp_time_format) {
                self::$wp_time_format = get_option('time_format');
            }
            return self::$wp_time_format;
        }

        /**
         * Get wordpress date and time format seperated by given seperator.
         * @param $date_time_seperator
         */
        public static function GetWpDateTimeFormat($date_time_seperator = " ") {
            return self::GetWpDateFormat(). $date_time_seperator .self::GetWpTimeFormat();
        }
    }
}
