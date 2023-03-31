<?


function filter_numbers(array $range, array $numbers)
{
    return array_filter($range, function ($num) use ($numbers) {
        return !in_array($num, $numbers);
    });
}

function prepare_table_values()
{
    return array('VALUE_YES_NOW' => 0, 'VALUE_NO_NOW' => 0, 'VALUE_YES_MAX' => 0, 'VALUE_NO_MAX' => 0);
}

// $value_to_change = array('VALUE_YES_NOW' => 0, 'VALUE_NO_NOW' => 0, 'VALUE_YES_MAX' => 0, 'VALUE_NO_MAX' => 0);
function calculate_case($condition, &$numbers, &$numbers_prev, &$value_to_change)
{
    $condition_result = $condition($numbers);
    $condition_result_prev = $condition($numbers_prev);

    if ($condition_result)
        if ($condition_result_prev)
            $value_to_change['VALUE_YES_NOW'] += 1;
        else {
            $value_to_change['VALUE_YES_NOW'] = 1;
            $value_to_change['VALUE_NO_NOW'] = 0;
        }


    if (!$condition_result)
        if (!$condition_result_prev)
            $value_to_change['VALUE_NO_NOW'] += 1;
        else {
            $value_to_change['VALUE_NO_NOW'] = 1;
            $value_to_change['VALUE_YES_NOW'] = 0;
        }


    if ($value_to_change['VALUE_YES_MAX'] < $value_to_change['VALUE_YES_NOW'])
        $value_to_change['VALUE_YES_MAX'] = $value_to_change['VALUE_YES_NOW'];

    if ($value_to_change['VALUE_NO_MAX'] < $value_to_change['VALUE_NO_NOW'])
        $value_to_change['VALUE_NO_MAX'] = $value_to_change['VALUE_NO_NOW'];
}

function get_table_rows(string $table_key): array
{
    global $wpdb;
    $rows = $wpdb->get_results('SELECT * FROM `wp_lottery_' . $table_key . '`');
    return $rows;
}

function save_table_results_to_database(string $table_key, array $rows): void
{
    global $wpdb;
    foreach ($rows as $key => $res) {
        $wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='" . $table_key . "' and VALUE_ID='" . $key . "'");
    }
}

function get_table_keys(string $table_key): array
{
    global $wpdb;
    $rows = $wpdb->get_results("SELECT VALUE_ID from `wp_lottery_results` where `LOTO_TYPE`='mechtalion_for_participants'", ARRAY_A);
    return $rows;
}

function init_table_values(array $rows): array
{
    $result = array();

    foreach ($rows as $row) {
        $result[$row['VALUE_ID']] = prepare_table_values();
    }

    return $result;
}

function clear_table(string $key)
{
    global $wpdb;
    $wpdb->query('DELETE FROM `wp_lottery_'.$key.'`');
}

function create_custom_sql_string_for_insert_table(string $key, int $numbers, $row)
{
    
    // довольно сложный код на первый взгляд, но он просто прибавляет NUMBER столько раз сколько написано в numbers, и прибавляет сами значения в VALUES;
    // на выходе получается примерно такая строка: INSERT INTO wp_lottery_mechtalion_for_participants (`CURRENT`,NUMBER...) VALUES(0,...);
    $first_part = "INSERT INTO `wp_lottery_" . $key . "` (`CURRENT`,";
    $str = '';
    for ($i = 1; $i <= $numbers; $i++) {
        $str .= 'NUMBER' . $i;
        if ($i < $numbers) {
            $str .= ',';
        } else {
            $str .= ')';
        }
    }
    $str1 = $first_part . $str;
    // теперь надо правильно всё сформировать.

    $sql_request_about_insert = $str1 . ' VALUES(' . $row->number . ',';
    for ($i = 1; $i <= $numbers; $i++) {
        $sql_request_about_insert .= $row->numbers[$i - 1];
        if ($i < $numbers) {
            $sql_request_about_insert .= ",";
        } else {
            $sql_request_about_insert .= ");";
        }
    }
    return $sql_request_about_insert;
}

function insert_values_to_table(string $key, int $numbers, array $rows)
{
    clear_table($key);

    global $wpdb;
    foreach ($rows as $item) {
        $sql_request_about_insert = create_custom_sql_string_for_insert_table($key, $numbers, $item);
        $wpdb->query($sql_request_about_insert);
    }
}

function calculate_cases_mecthalion_for_participants($rows, $array_res)
{
    // тут нужны невыпавшие числа.
    $numbers_prev = array();
    foreach ($rows as $row) {
        $array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9, $row->NUMBER10, $row->NUMBER11, $row->NUMBER12, $row->NUMBER13, $row->NUMBER14, $row->NUMBER15, $row->NUMBER16, $row->NUMBER17, $row->NUMBER18, $row->NUMBER19, $row->NUMBER20, $row->NUMBER21, $row->NUMBER22, $row->NUMBER23, $row->NUMBER24, $row->NUMBER25, $row->NUMBER26, $row->NUMBER27, $row->NUMBER28, $row->NUMBER29, $row->NUMBER30, $row->NUMBER31, $row->NUMBER32, $row->NUMBER33, $row->NUMBER34, $row->NUMBER35, $row->NUMBER36, $row->NUMBER37);
        $numbers = filter_numbers(range(1, 80), $array);

        for ($i = 1; $i <= 80; $i++) {
            // выпадет номер $i
            calculate_case(function ($nums) use ($i) {
                return in_array($i, $nums);
            }, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
        }
        $numbers_prev = $numbers;
    }

    return $array_res;
}