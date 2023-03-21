<?php
/*
Plugin Name: Лотерея
Description: Вывод результатов различных лото.
Version: 1.0-beta
Author: dmitrit.dev@gmail.com
*/

function database_creation()
{
    global $wpdb;
    $lottery = $wpdb->prefix . 'lottery';

    $db_string = "CREATE TABLE " . $lottery . " (
                ID int NOT NULL auto_increment,
                CURRENT int NOT NULL,
                NUMBER1 int,
                NUMBER2 int,
                NUMBER3 int,
                LOTTERY_NAME varchar(255),
                PRIMARY KEY (ID)
    );";

    require_once ABSPATH.'wp-admin/includes/upgrade.php';
    dbDelta($db_string);

    $lottery_results = $wpdb->prefix . 'lottery_results';

    $db_string = "CREATE TABLE " . $lottery_results . " (
        ID int NOT NULL auto_increment,
        VALUE_ID varchar(255),
        VALUE_CONDITION varchar(255),
        VALUE_YES_NOW int,
        VALUE_YES int,
        VALUE_NO_NOW int,
        VALUE_NO int,
        LOTTERY_NAME varchar(255),
        PRIMARY KEY (ID)
    );";

    dbDelta($db_string);
}

register_activation_hook(__FILE__, 'database_creation');
?>