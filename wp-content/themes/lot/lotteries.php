<?php

interface LotteryActions
{
    function get_values_from_table(string $table_key, int $numbersCount): array;
    function calculate_values(string $table_key): array;
    function save_lottery_results_to_database(string $table_key, array $rows): void;
    function test_table(string $table_key)
    {

    }
}

class Lottery implements LotteryActions
{
    public string $key = '';
    public array $conditions;

    public function get_values_from_table(string $table_key, int $numbersCount): array
    {
        return array();
    }

    public function calculate_values(string $table_key): array
    {
        return array();
    }

    public function save_lottery_results_to_database(string $table_key, array $rows)
    {

    }
}


function save_lottery_results_to_database(string $lottery_key, array $rows)
{
    global $wpdb;

    foreach ($rows as $key => $res) {
        $wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='." . $lottery_key . ".' and VALUE_ID='" . $key . "'");
    }
}