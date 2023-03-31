<?php
/**
 * lot functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package lot
 */
require dirname(__FILE__) . '/vendor/autoload.php';

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
// Отключаем админ бар во фронте
add_filter('show_admin_bar', '__return_false');

function lot_setup()
{
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on lot, use a find and replace
	 * to change 'lot' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('lot', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', 'lot'),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'lot_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height' => 250,
			'width' => 250,
			'flex-width' => true,
			'flex-height' => true,
		)
	);

	if (!current_user_can('manage_options') && !is_admin()) {
		show_admin_bar(false);
	}
}


add_action('wp_ajax_get_lotteries_names', 'get_lotteries_json_names');
add_action('wp_ajax_nopriv_get_lotteries_names', 'get_lotteries_json_names');

function get_lotteries_json_names()
{
	$args = array(
		'numberposts' => -1,
		'post_type' => 'page',
		'post_parent' => 158
	);
	$res = [];
	$pages = get_posts($args);

	foreach ($pages as $page) {
		$res[$page->post_title] = get_permalink($page->ID);
	}

	echo json_encode($res);

	wp_die();
}

add_action('wp_ajax_load_table', 'load_table');
add_action('wp_ajax_nopriv_load_table', 'load_table');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function lot_content_width()
{
	$GLOBALS['content_width'] = apply_filters('lot_content_width', 640);
}
add_action('after_setup_theme', 'lot_content_width', 0);

add_action('init', 'register_post_types');

function register_post_types()
{

	register_post_type('lot_table', [
		'taxonomies' => [],
		// post related taxonomies
		'label' => null,
		'labels' => [
			'name' => 'Таблица лотереи',
			// name for the post type.
			'singular_name' => 'таблица лотереи',
			// name for single post of that type.
			'add_new' => 'добавить таблицу лотереи',
			// to add a new post.
			'add_new_item' => 'добавление таблицу лотереи',
			// title for a newly created post in the admin panel.
			'edit_item' => 'Редактировать таблицу лотереи',
			// for editing post type.
			'new_item' => 'Новая таблица лотереи',
			// new post's text.
			'view_item' => 'Посмотреть таблицу лотереи',
			// for viewing this post type.
			'search_items' => 'Поиск таблицы',
			// search for these post types.
			'not_found' => 'Не найдено',
			// if search has not found anything.
			'parent_item_colon' => '',
			// for parents (for hierarchical post types).
			'menu_name' => 'Таблицы лотереи',
			// menu name.
		],
		'description' => '',
		'public' => true,
		'show_in_menu' => null,
		'show_in_rest' => null,
		'rest_base' => null,
		'menu_position' => null,
		'menu_icon' => null,
		'hierarchical' => false,
		// [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats' ]
		'supports' => ['title'],
		'has_archive' => false,
		'rewrite' => true,
		'query_var' => true,
	]);
}

include_once __DIR__ . '/lotteries.php';

add_action('wp_ajax_insert_mechtalion_for_participants', 'insert_from_table_mechtalion_for_participants');
add_action('wp_ajax_nopriv_insert_mechtalion_for_participants', 'insert_from_table_mechtalion_for_participants');

function insert_from_table_mechtalion_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();


	insert_values_to_table('mechtalion_for_participants', 37, $rows);

	echo json_encode(['success' => 'true'], true);
	wp_die();
}


add_action('wp_ajax_get_mechtalion_for_participants', 'get_mechtalion_for_participants');
add_action('wp_ajax_nopriv_get_mechtalion_for_participants', 'get_mechtalion_for_participants');

function get_mechtalion_for_participants()
{
	// берём список рядов
	$rows = get_table_rows('mechtalion_for_participants');
	// берём список ключей для условий
	$keys = get_table_keys('mechtalion_for_participants');
	// заполняем их нулевыми значениями.
	$array_res = init_table_values($keys);
	// рассчитываем результаты
	$array_res = calculate_cases_mecthalion_for_participants($rows, $array_res);
	// сохраняем результаты в таблицу
	save_table_results_to_database('mechtalion_for_participants', $array_res);
	// выводим значение 
	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));

	wp_die();
}

add_action('wp_ajax_insert_forsaj_75_for_participants', 'insert_from_table_forsaj_75_for_participants');
add_action('wp_ajax_nopriv_insert_forsaj_75_for_participants', 'insert_from_table_forsaj_75_for_participants');


function insert_from_table_forsaj_75_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_forsaj_75_for_participants`');
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_forsaj_75_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_forsaj_75_for_participants', 'get_forsaj_75_for_participants');
add_action('wp_ajax_nopriv_get_forsaj_75_for_participants', 'get_forsaj_75_for_participants');

function get_forsaj_75_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_forsaj_75_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 75; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	foreach ($rows as $row) {
		$array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3);
		$numbers = filter_numbers(range(1, 75), $array);

		for ($i = 1; $i <= 75; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='forsaj_75_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s'), "aaa" => "bbb"));
	wp_die();
}



add_action('wp_ajax_insert_pyataya_skorost_for_participants', 'insert_from_table_pyataya_skorost_for_participants');
add_action('wp_ajax_nopriv_insert_pyataya_skorost_for_participants', 'insert_from_table_pyataya_skorost_for_participants');


function insert_from_table_pyataya_skorost_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_pyataya_skorost_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_pyataya_skorost_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_pyataya_skorost_for_participants', 'get_pyataya_skorost_for_participants');
add_action('wp_ajax_nopriv_get_pyataya_skorost_for_participants', 'get_pyataya_skorost_for_participants');

function get_pyataya_skorost_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_pyataya_skorost_for_participants`');
	$array_res = array();

	for ($i = 1; $i <= 36; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	for ($i = 1; $i <= 4; $i++) {
		$array_res['DOP_NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	$special_prev = 0;
	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5);
		$special = $row->NUMBER6;

		for ($i = 1; $i <= 36; $i++) {
			//Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}
		for ($i = 1; $i <= 4; $i++) {
			// Бонусный (Дополнительный) номер $i
			calculate_case(function ($num) use ($i) {
				return $num == $i;
			}, $special, $special_prev, $array_res['DOP_NUM_' . $i]);
		}
		$numbers_prev = $numbers;
		$special_prev = $special;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='pyataya_skorost_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}



add_action('wp_ajax_insert_5_37_for_participants', 'insert_from_table_5_37_for_participants');
add_action('wp_ajax_nopriv_insert_5_37_for_participants', 'insert_from_table_5_37_for_participants');


function insert_from_table_5_37_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_5_37_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_5_37_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_5_37_for_participants', 'get_5_37_for_participants');
add_action('wp_ajax_nopriv_get_5_37_for_participants', 'get_5_37_for_participants');

function get_5_37_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_5_37_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 37; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5);

		for ($i = 1; $i <= 37; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='5_37_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_3_3_for_participants', 'insert_from_table_3_3_for_participants');
add_action('wp_ajax_nopriv_insert_3_3_for_participants', 'insert_from_table_3_3_for_participants');


function insert_from_table_3_3_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_3_3_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_3_3_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9,NUMBER10,NUMBER11,NUMBER12,NUMBER13,NUMBER14,NUMBER15) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ',' . $item->numbers[9] . ',' . $item->numbers[10] . ',' . $item->numbers[11] . ',' . $item->numbers[12] . ',' . $item->numbers[13] . ',' . $item->numbers[14] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_3_3_for_participants', 'get_3_3_for_participants');
add_action('wp_ajax_nopriv_get_3_3_for_participants', 'get_3_3_for_participants');

function get_3_3_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_3_3_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 30; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}
	$numbers_prev = array();

	foreach ($rows as $row) {
		$array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9, $row->NUMBER10, $row->NUMBER11, $row->NUMBER12, $row->NUMBER13, $row->NUMBER14, $row->NUMBER15);
		$numbers = filter_numbers(range(1, 30), $array);

		for ($i = 1; $i <= 30; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='3_3_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}



add_action('wp_ajax_insert_velikolepnaya_8_for_participants', 'insert_from_table_velikolepnaya_8_for_participants');
add_action('wp_ajax_nopriv_insert_velikolepnaya_8_for_participants', 'insert_from_table_velikolepnaya_8_for_participants');

function insert_from_table_velikolepnaya_8_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_velikolepnaya_8_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_velikolepnaya_8_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_velikolepnaya_8_for_participants', 'get_velikolepnaya_8_for_participants');
add_action('wp_ajax_nopriv_get_velikolepnaya_8_for_participants', 'get_velikolepnaya_8_for_participants');

function get_velikolepnaya_8_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_velikolepnaya_8_for_participants`');
	$array_res = array();

	for ($i = 1; $i <= 20; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	for ($i = 1; $i <= 4; $i++) {
		$array_res['DOP_NUM_' . $i] = prepare_table_values();
	}


	$numbers_prev = array();
	$special_prev = 0;
	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);
		$special = $row->NUMBER9;

		for ($i = 1; $i <= 20; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		for ($i = 1; $i <= 4; $i++) {
			// Бонусный (Дополнительный) номер $i
			calculate_case(function ($num) use ($i) {
				return $num == $i;
			}, $special, $special_prev, $array_res['DOP_NUM_' . $i]);
		}
		$special_prev = $special;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='velikolepnaya_8_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}
add_action('wp_ajax_insert_lavina_prizov_for_participants', 'insert_from_table_lavina_prizov_for_participants');
add_action('wp_ajax_nopriv_insert_lavina_prizov_for_participants', 'insert_from_table_lavina_prizov_for_participants');


function insert_from_table_lavina_prizov_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_lavina_prizov_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_lavina_prizov_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_lavina_prizov_for_participants', 'get_lavina_prizov_for_participants');
add_action('wp_ajax_nopriv_get_lavina_prizov_for_participants', 'get_lavina_prizov_for_participants');

function get_lavina_prizov_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_lavina_prizov_for_participants`');
	$array_res = array();

	for ($i = 1; $i <= 20; $i++) {
		$array_res['FIELD_1_NUM_' . $i] = prepare_table_values();
		$array_res['FIELD_2_NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	$numbers_2_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4);
		$numbers_2 = array($row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);

		for ($i = 1; $i <= 20; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res["FIELD_1_NUM_" . $i]);

			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers_2, $numbers_2_prev, $array_res["FIELD_2_NUM_" . $i]);
		}

		$numbers_2_prev = $numbers_2;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='lavina_prizov_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_6_45_for_participants', 'insert_from_table_6_45_for_participants');
add_action('wp_ajax_nopriv_insert_6_45_for_participants', 'insert_from_table_6_45_for_participants');


function insert_from_table_6_45_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_6_45_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_6_45_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_6_45_for_participants', 'get_6_45_for_participants');
add_action('wp_ajax_nopriv_get_6_45_for_participants', 'get_6_45_for_participants');

function get_6_45_for_participants()
{
	// $keys = get_keys('6_45_for_participants');
	// $array_res = init_array_of_result($keys,'6_45_for_participants');
	// $array_res = calculate_values('6_45_for_participants');
	// save_table_results_to_database('6_45_for_participants');

	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_6_45_for_participants`');

	$array_res = array();
	for ($i = 1; $i <= 45; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6);

		for ($i = 1; $i <= 45; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='6_45_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_5_36_for_participants', 'insert_from_table_5_36_for_participants');
add_action('wp_ajax_nopriv_insert_5_36_for_participants', 'insert_from_table_5_36_for_participants');


function insert_from_table_5_36_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_5_36_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_5_36_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_5_36_for_participants', 'get_5_36_for_participants');
add_action('wp_ajax_nopriv_get_5_36_for_participants', 'get_5_36_for_participants');

function get_5_36_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_5_36_for_participants`');
	$array_res = array();

	for ($i = 1; $i <= 36; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	for ($i = 1; $i <= 4; $i++) {
		$array_res['DOP_NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	$special_prev = 0;

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5);
		$special = $row->NUMBER6;

		for ($i = 1; $i <= 36; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res["NUM_$i"]);
		}

		for ($i = 1; $i <= 4; $i++) {
			// выпадет дополнительный номер $i
			calculate_case(function ($special) use ($i) {
				return $special == $i;
			}, $special, $special_prev, $array_res["DOP_NUM_$i"]);
		}

		$special_prev = $special;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='5_36_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}


add_action('wp_ajax_insert_4_20_for_participants', 'insert_from_table_4_20_for_participants');
add_action('wp_ajax_nopriv_insert_4_20_for_participants', 'insert_from_table_4_20_for_participants');
function insert_from_table_4_20_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_4_20_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_4_20_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}

add_action('wp_ajax_get_4_20_for_participants', 'get_4_20_for_participants');
add_action('wp_ajax_nopriv_get_4_20_for_participants', 'get_4_20_for_participants');
function get_4_20_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_4_20_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 20; $i++) {
		$array_res['FIELD_1_NUM_' . $i] = prepare_table_values();
		$array_res['FIELD_2_NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	$numbers_2_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4);
		$numbers_2 = array($row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);

		for ($i = 1; $i <= 20; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res["FIELD_1_NUM_$i"]);

			//ПОЛЕ 2: Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers_2, $numbers_2_prev, $array_res["FIELD_2_NUM_$i"]);
		}

		$numbers_2_prev = $numbers_2;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='4_20_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_rapido_for_participants', 'insert_from_table_rapido_for_participants');
add_action('wp_ajax_nopriv_insert_rapido_for_participants', 'insert_from_table_rapido_for_participants');
function insert_from_table_rapido_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_rapido_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_rapido_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_rapido_for_participants', 'get_rapido_for_participants');
add_action('wp_ajax_nopriv_get_rapido_for_participants', 'get_rapido_for_participants');
function get_rapido_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_rapido_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 20; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	for ($i = 1; $i <= 4; $i++) {
		$array_res['DOP_NUM_' . $i] = prepare_table_values();
	}


	$numbers_prev = array();
	$special_prev = 0;

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);
		$special = $row->NUMBER9;

		for ($i = 1; $i <= 20; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		for ($i = 1; $i <= 4; $i++) {
			// Бонусный (Дополнительный) номер $i
			calculate_case(function ($special) use ($i) {
				return $special == $i;
			}, $special, $special_prev, $array_res['DOP_NUM_' . $i]);
		}

		$special_prev = $special;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='rapido_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}



add_action('wp_ajax_insert_rapido_2_for_participants', 'insert_from_table_rapido_2_for_participants');
add_action('wp_ajax_nopriv_insert_rapido_2_for_participants', 'insert_from_table_rapido_2_for_participants');
function insert_from_table_rapido_2_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_rapido_2_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_rapido_2_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_rapido_2_for_participants', 'get_rapido_2_for_participants');
add_action('wp_ajax_nopriv_get_rapido_2_for_participants', 'get_rapido_2_for_participants');
function get_rapido_2_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_rapido_2_for_participants`');
	$keys = array(
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'DOP_NUM_1',
		'DOP_NUM_2',
		'DOP_NUM_3',
		'DOP_NUM_4'
	);
	$array_res = array();

	for ($i = 1; $i <= 20; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	for ($i = 1; $i <= 4; $i++) {
		$array_res['DOP_NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	$special_prev = 0;

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);
		$special = $row->NUMBER9;


		for ($i = 1; $i <= 20; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		for ($i = 1; $i <= 4; $i++) {
			// Бонусный (Дополнительный) номер $i
			calculate_case(function ($special) use ($i) {
				return $special == $i;
			}, $special, $special_prev, $array_res['DOP_NUM_' . $i]);
		}

		$special_prev = $special;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='rapido_2_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_rapido_for_gamers', 'insert_from_table_rapido_for_gamers');
add_action('wp_ajax_nopriv_insert_rapido_for_gamers', 'insert_from_table_rapido_for_gamers');
function insert_from_table_rapido_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_rapido_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_rapido_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}

add_action('wp_ajax_get_rapido_for_gamers', 'get_rapido_for_gamers');
add_action('wp_ajax_nopriv_get_rapido_for_gamers', 'get_rapido_for_gamers');

function get_rapido_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_rapido_for_gamers`');
	$keys = array(
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'DOP_NUM_1',
		'DOP_NUM_2',
		'DOP_NUM_3',
		'DOP_NUM_4',
		'DOP_NUM_GT_2.5',
		'DOP_NUM_EVEN',
		'SUM_GT_77.5',
		'SUM_GT_81.5',
		'SUM_GT_84.5',
		'SUM_GT_88.5',
		'SUM_GT_91.5',
		'SUM_EVEN',
		'SUM_ODD_GT_35.5',
		'SUM_ODD_GT_39.5',
		'SUM_ODD_GT_43.5',
		'SUM_EVEN_GT_38.5',
		'SUM_EVEN_GT_42.5',
		'SUM_EVEN_GT_46.5',
		'SUM_1_7_GT_11.5',
		'SUM_1_10_GT_21.5',
		'SUM_8_14_GT_31.5',
		'SUM_11_20_GT_62.5',
		'SUM_15_20_GT_37.5',
		'MIN_GT_2.5',
		'MAX_GT_18.5',
		'MIN_EVEN',
		'MAX_EVEN',
		'SUM_MIN_MAX_GT_20.5',
		'SUM_MIN_MAX_EVEN',
		'DIFF_MAX_MIN_GT_16.5',
		'DIFF_MAX_MIN_EVEN',
		'NUM_1_GT_10.5',
		'NUM_2_GT_10.5',
		'NUM_3_GT_10.5',
		'NUM_4_GT_10.5',
		'NUM_5_GT_10.5',
		'NUM_6_GT_10.5',
		'NUM_7_GT_10.5',
		'NUM_8_GT_10.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'NUM_8_EVEN',
		'ANY_DIV_10',
		'COUNT_EVEN_GT_COUNT_ODD',
		'SUM_EVEN_GT_SUM_ODD',
		'ODD_LT_4',
		'ODD_EQ_4',
		'ODD_GT_4',
		'EVEN_LT_4',
		'EVEN_EQ_4',
		'EVEN_GT_4',
		'COUNT_1_7_LT_3',
		'COUNT_1_7_EQ_3',
		'COUNT_1_7_GT_3',
		'COUNT_1_10_LT_4',
		'COUNT_1_10_EQ_4',
		'COUNT_1_10_GT_4',
		'COUNT_8_14_LT_3',
		'COUNT_8_14_EQ_3',
		'COUNT_8_14_GT_3',
		'COUNT_11_20_LT_4',
		'COUNT_11_20_EQ_4',
		'COUNT_11_20_GT_4',
		'COUNT_15_20_LT_2',
		'COUNT_15_20_EQ_2',
		'COUNT_15_20_GT_2'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array();
	$special_prev = -1; foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);
		$special = $row->NUMBER9;

		for ($i = 1; $i <= 20; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		for ($i = 1; $i <= 4; $i++) {
			// Бонусный (Дополнительный) номер 1
			calculate_case(function ($special) use ($i) {
				return $special == $i;
			}, $special, $special_prev, $array_res['DOP_NUM_' . $i]);
		}

		// Бонусный (Дополнительный) номер Больше 2.5
		calculate_case(function ($special) {
			return $special == 2.5;
		}, $special, $special_prev, $array_res['DOP_NUM_GT_2.5']);

		// Бонусный (Дополнительный) номер Чет
		calculate_case(function ($special) {
			return $special % 2 == 0;
		}, $special, $special_prev, $array_res['DOP_NUM_EVEN']);

		// Сумма всех выпавших номеров Больше 77.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 77.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_77.5']);

		// Сумма всех выпавших номеров Больше 81.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 81.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_81.5']);

		// Сумма всех выпавших номеров Больше 84.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 84.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_84.5']);

		// Сумма всех выпавших номеров Больше 88.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 88.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_88.5']);

		// Сумма всех выпавших номеров Больше 91.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 91.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_91.5']);

		// Сумма всех выпавших номеров Чет
		calculate_case(function ($nums) {
			return (array_sum($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN']);

		// Сумма всех выпавших НЕчетных номеров Больше 35.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 35.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_35.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 39.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 39.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_39.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 43.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 43.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_43.5']);

		// Сумма всех выпавших четных номеров Больше 38.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 38.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_38.5']);

		// Сумма всех выпавших четных номеров Больше 42.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 42.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_42.5']);

		// Сумма всех выпавших четных номеров Больше 46.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 46.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_46.5']);

		// Сумма всех выпавших номеров от 1 до 7 Больше 11.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 7;
			});
			return array_sum($filtered_list) > 11.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_7_GT_11.5']);

		// Сумма всех выпавших номеров от 1 до 10 Больше 21.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			});
			return array_sum($filtered_list) > 21.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_10_GT_21.5']);

		// Сумма всех выпавших номеров от 8 до 14 Больше 31.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 8 && $num <= 14;
			});
			return array_sum($filtered_list) > 31.5;
		}, $numbers, $numbers_prev, $array_res['SUM_8_14_GT_31.5']);

		// Сумма всех выпавших номеров от 11 до 20 Больше 62.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			});
			return array_sum($filtered_list) > 62.5;
		}, $numbers, $numbers_prev, $array_res['SUM_11_20_GT_62.5']);

		// Сумма всех выпавших номеров от 15 до 20 Больше 37.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 15 && $num <= 20;
			});
			return array_sum($filtered_list) > 37.5;
		}, $numbers, $numbers_prev, $array_res['SUM_15_20_GT_37.5']);

		// Наименьший выпавший номер Больше 2.5
		calculate_case(function ($nums) {
			return min($nums) > 2.5;
		}, $numbers, $numbers_prev, $array_res['MIN_GT_2.5']);

		// Наибольший выпавший номер Больше 18.5
		calculate_case(function ($nums) {
			return max($nums) > 18.5;
		}, $numbers, $numbers_prev, $array_res['MAX_GT_18.5']);

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Больше 20.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 20.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_20.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Разность наибольшего и наименьшего из выпавших номеров Больше 16.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 16.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_16.5']);

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		for ($i = 0; $i < 8; $i++) {
			// i-й номер больше 10.5
			calculate_case(function ($nums) use ($i) {
				return $nums[$i] > 10.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . ($i + 1) . '_GT_10.5']);

			// i-й номер Чет
			calculate_case(function ($nums) use ($i) {
				return $nums[$i] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . ($i + 1) . '_EVEN']);
		}

		// Любой из выпавших номеров кратен 10
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 10 == 0) {
					return true;
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ANY_DIV_10']);

		// Четных номеров выпадет больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		// Сумма выпавших Четных номеров Больше, чем сумма выпавших НЕчетных номеров
		calculate_case(function ($nums) {
			$even = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			$odd = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_SUM_ODD']);

		// Количество выпавших НЕчетных номеров Меньше 4
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd < 4;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_4']);

		// Количество выпавших НЕчетных номеров Ровно 4
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd == 4;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_4']);

		// Количество выпавших НЕчетных номеров Больше 4
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd > 4;
		}, $numbers, $numbers_prev, $array_res['ODD_GT_4']);

		// Количество выпавших Четных номеров Меньше 4
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even < 4;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_4']);

		// Количество выпавших Четных номеров Ровно 4
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even == 4;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_4']);

		// Количество выпавших Четных номеров Больше 4
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even > 4;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_4']);

		// Количество выпавших номеров от 1 до 7 включительно Меньше 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 7;
			}));
			return $filtered < 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_7_LT_3']);

		// Количество выпавших номеров от 1 до 7 включительно Ровно 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 7;
			}));
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_7_EQ_3']);

		// Количество выпавших номеров от 1 до 7 включительно Больше 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 7;
			}));
			return $filtered > 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_7_GT_3']);

		// Количество выпавших номеров от 1 до 10 включительно Меньше 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			}));
			return $filtered < 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_LT_4']);

		// Количество выпавших номеров от 1 до 10 включительно Ровно 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			}));
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_EQ_4']);

		// Количество выпавших номеров от 1 до 10 включительно Больше 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			}));
			return $filtered > 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_GT_4']);

		// Количество выпавших номеров от 8 до 14 включительно Меньше 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 8 && $num <= 14;
			}));
			return $filtered < 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_8_14_LT_3']);

		// Количество выпавших номеров от 8 до 14 включительно Ровно 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 8 && $num <= 14;
			}));
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_8_14_EQ_3']);

		// Количество выпавших номеров от 8 до 14 включительно Больше 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 8 && $num <= 14;
			}));
			return $filtered > 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_8_14_GT_3']);

		// Количество выпавших номеров от 11 до 20 включительно Меньше 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			}));
			return $filtered < 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_LT_4']);

		// Количество выпавших номеров от 11 до 20 включительно Ровно 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			}));
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_EQ_4']);

		// Количество выпавших номеров от 11 до 20 включительно Больше 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			}));
			return $filtered > 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_GT_4']);

		// Количество выпавших номеров от 15 до 20 включительно Меньше 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 15 && $num <= 20;
			}));
			return $filtered < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_15_20_LT_2']);

		// Количество выпавших номеров от 15 до 20 включительно Ровно 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 15 && $num <= 20;
			}));
			return $filtered == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_15_20_EQ_2']);

		// Количество выпавших номеров от 15 до 20 включительно Больше 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 15 && $num <= 20;
			}));
			return $filtered > 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_15_20_GT_2']);

		$special_prev = $special;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='rapido_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}


add_action('wp_ajax_insert_12_24_for_participants', 'insert_from_table_12_24_for_participants');
add_action('wp_ajax_nopriv_insert_12_24_for_participants', 'insert_from_table_12_24_for_participants');


function insert_from_table_12_24_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_12_24_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_12_24_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9,NUMBER10,NUMBER11,NUMBER12) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ',' . $item->numbers[9] . ',' . $item->numbers[10] . ',' . $item->numbers[11] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_12_24_for_participants', 'get_12_24_for_participants');
add_action('wp_ajax_nopriv_get_12_24_for_participants', 'get_12_24_for_participants');

function get_12_24_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_12_24_for_participants`');

	$array_res = array();
	for ($i = 1; $i <= 24; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9, $row->NUMBER10, $row->NUMBER11, $row->NUMBER12);

		for ($i = 1; $i <= 24; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='12_24_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}



add_action('wp_ajax_insert_duel_for_participants', 'insert_from_table_duel_for_participants');
add_action('wp_ajax_nopriv_insert_duel_for_participants', 'insert_from_table_duel_for_participants');


function insert_from_table_duel_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_duel_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_duel_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_duel_for_participants', 'get_duel_for_participants');
add_action('wp_ajax_nopriv_get_duel_for_participants', 'get_duel_for_participants');

function get_duel_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_duel_for_participants`');
	$array_res = array();
	for ($i = 1; $i <= 26; $i++) {
		$array_res['FIELD_1_NUM_' . $i] = prepare_table_values();
		$array_res['FIELD_2_NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	$numbers_2_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2);
		$numbers_2 = array($row->NUMBER3, $row->NUMBER4);

		for ($i = 1; $i <= 26; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res["FIELD_1_NUM_" . $i]);

			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers_2, $numbers_2_prev, $array_res["FIELD_2_NUM_" . $i]);
		}

		$numbers_2_prev = $numbers_2;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='duel_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}
add_action('wp_ajax_insert_top3_for_participants', 'insert_from_table_top3_for_participants');
add_action('wp_ajax_nopriv_insert_top3_for_participants', 'insert_from_table_top3_for_participants');
function insert_from_table_top3_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_top3_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_top3_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_top3_for_participants', 'get_top3_for_participants');
add_action('wp_ajax_nopriv_get_top3_for_participants', 'get_top3_for_participants');
function get_top3_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_top3_for_participants`');
	$array_res = array();

	for ($i = 0; $i < 10; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}
	$numbers_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3);

		for ($i = 0; $i < 10; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='top3_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}



add_action('wp_ajax_insert_keno_for_participants', 'insert_from_table_keno_for_participants');
add_action('wp_ajax_nopriv_insert_keno_for_participants', 'insert_from_table_keno_for_participants');
function insert_from_table_keno_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_keno_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_keno_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9,NUMBER10,NUMBER11,NUMBER12,NUMBER13,NUMBER14,NUMBER15,NUMBER16,NUMBER17,NUMBER18,NUMBER19,NUMBER20,NUMBER21,NUMBER22) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ',' . $item->numbers[9] . ',' . $item->numbers[10] . ',' . $item->numbers[11] . ',' . $item->numbers[12] . ',' . $item->numbers[13] . ',' . $item->numbers[14] . ',' . $item->numbers[15] . ',' . $item->numbers[16] . ',' . $item->numbers[17] . ',' . $item->numbers[18] . ',' . $item->numbers[19] . ',' . $item->numbers[20] . ',' . $item->numbers[21] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}

add_action('wp_ajax_get_keno_for_participants', 'get_keno_for_participants');
add_action('wp_ajax_nopriv_get_keno_for_participants', 'get_keno_for_participants');
function get_keno_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_keno_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 80; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	for ($i = 1; $i <= 10; $i++) {
		$array_res['COLUMN_' . $i] = prepare_table_values();
	}

	$array_res['COUNT_EVEN_GT_COUNT_ODD'] = prepare_table_values();
	$array_res['COUNT_EVEN_EQ_COUNT_ODD'] = prepare_table_values();
	$array_res['COUNT_ODD_GT_COUNT_EVEN'] = prepare_table_values();

	$numbers_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9, $row->NUMBER10, $row->NUMBER11, $row->NUMBER12, $row->NUMBER13, $row->NUMBER14, $row->NUMBER15, $row->NUMBER16, $row->NUMBER17, $row->NUMBER18, $row->NUMBER19, $row->NUMBER20, $row->NUMBER21);
		for ($i = 1; $i <= 80; $i++) {
			// Выпадет номер 1
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		for ($i = 1; $i <= 10; $i++) {
			// Столбец $i
			calculate_case(function ($nums) use ($i) {
				return end($nums) == $i;
			}, $numbers, $numbers_prev, $array_res['COLUMN_' . $i]);
		}

		// Выпадет Больше Четных чисел
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		// Выпадет Поровну Четных и НЕчетных чисел
		calculate_case(function ($nums) {
			return count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			})) == count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_EQ_COUNT_ODD']);

		// Выпадет Больше НЕчетных чисел
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even < $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_GT_COUNT_EVEN']);

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='keno_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_6_36_for_participants', 'insert_from_table_6_36_for_participants');
add_action('wp_ajax_nopriv_insert_6_36_for_participants', 'insert_from_table_6_36_for_participants');
function insert_from_table_6_36_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_6_36_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_6_36_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_6_36_for_participants', 'get_6_36_for_participants');
add_action('wp_ajax_nopriv_get_6_36_for_participants', 'get_6_36_for_participants');
function get_6_36_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_6_36_for_participants`');

	$array_res = array();
	for ($i = 1; $i <= 36; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6);

		for ($i = 1; $i <= 36; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='6_36_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_rocketbingo_for_participants', 'insert_from_table_rocketbingo_for_participants');
add_action('wp_ajax_nopriv_insert_rocketbingo_for_participants', 'insert_from_table_rocketbingo_for_participants');
function insert_from_table_rocketbingo_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_rocketbingo_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_rocketbingo_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9,NUMBER10,NUMBER11,NUMBER12,NUMBER13,NUMBER14,NUMBER15,NUMBER16,NUMBER17,NUMBER18,NUMBER19,NUMBER20,NUMBER21,NUMBER22,NUMBER23,NUMBER24,NUMBER25,NUMBER26,NUMBER27,NUMBER28,NUMBER29,NUMBER30,NUMBER31,NUMBER32,NUMBER33,NUMBER34,NUMBER35) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ',' . $item->numbers[9] . ',' . $item->numbers[10] . ',' . $item->numbers[11] . ',' . $item->numbers[12] . ',' . $item->numbers[13] . ',' . $item->numbers[14] . ',' . $item->numbers[15] . ',' . $item->numbers[16] . ',' . $item->numbers[17] . ',' . $item->numbers[18] . ',' . $item->numbers[19] . ',' . $item->numbers[20] . ',' . $item->numbers[21] . ',' . $item->numbers[22] . ',' . $item->numbers[23] . ',' . $item->numbers[24] . ',' . $item->numbers[25] . ',' . $item->numbers[26] . ',' . $item->numbers[27] . ',' . $item->numbers[28] . ',' . $item->numbers[29] . ',' . $item->numbers[30] . ',' . $item->numbers[31] . ',' . $item->numbers[32] . ',' . $item->numbers[33] . ',' . $item->numbers[34] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_rocketbingo_for_participants', 'get_rocketbingo_for_participants');
add_action('wp_ajax_nopriv_get_rocketbingo_for_participants', 'get_rocketbingo_for_participants');
function get_rocketbingo_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_rocketbingo_for_participants`');
	$array_res = array();

	for ($i = 1; $i <= 75; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9, $row->NUMBER10, $row->NUMBER11, $row->NUMBER12, $row->NUMBER13, $row->NUMBER14, $row->NUMBER15, $row->NUMBER16, $row->NUMBER17, $row->NUMBER18, $row->NUMBER19, $row->NUMBER20, $row->NUMBER21, $row->NUMBER22, $row->NUMBER23, $row->NUMBER24, $row->NUMBER25, $row->NUMBER26, $row->NUMBER27, $row->NUMBER28, $row->NUMBER29, $row->NUMBER30, $row->NUMBER31, $row->NUMBER32, $row->NUMBER33, $row->NUMBER34, $row->NUMBER35);

		for ($i = 1; $i <= 75; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='rocketbingo_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_7_49_for_participants', 'insert_from_table_7_49_for_participants');
add_action('wp_ajax_nopriv_insert_7_49_for_participants', 'insert_from_table_7_49_for_participants');
function insert_from_table_7_49_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_7_49_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_7_49_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_7_49_for_participants', 'get_7_49_for_participants');
add_action('wp_ajax_nopriv_get_7_49_for_participants', 'get_7_49_for_participants');
function get_7_49_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_7_49_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 49; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7);

		for ($i = 1; $i <= 49; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='7_49_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_bingo_75_for_participants', 'insert_from_table_bingo_75_for_participants');
add_action('wp_ajax_nopriv_insert_bingo_75_for_participants', 'insert_from_table_bingo_75_for_participants');
function insert_from_table_bingo_75_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_bingo_75_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_bingo_75_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_bingo_75_for_participants', 'get_bingo_75_for_participants');
add_action('wp_ajax_nopriv_get_bingo_75_for_participants', 'get_bingo_75_for_participants');
function get_bingo_75_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_bingo_75_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 75; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();

	foreach ($rows as $row) {
		$array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3);
		$numbers = filter_numbers(range(1, 75), $array);

		for ($i = 1; $i <= 75; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='bingo_75_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_rus_loto_for_participants', 'insert_from_table_rus_loto_for_participants');
add_action('wp_ajax_nopriv_insert_rus_loto_for_participants', 'insert_from_table_rus_loto_for_participants');
function insert_from_table_rus_loto_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_rus_loto_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_rus_loto_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_rus_loto_for_participants', 'get_rus_loto_for_participants');
add_action('wp_ajax_nopriv_get_rus_loto_for_participants', 'get_rus_loto_for_participants');
function get_rus_loto_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_rus_loto_for_participants`');

	$array_res = array();
	for ($i = 1; $i <= 90; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();

	foreach ($rows as $row) {
		$array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5);
		$numbers = filter_numbers(range(1, 90), $array);

		for ($i = 1; $i <= 90; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='rus_loto_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_loto_express_for_participants', 'insert_from_table_loto_express_for_participants');
add_action('wp_ajax_nopriv_insert_loto_express_for_participants', 'insert_from_table_loto_express_for_participants');
function insert_from_table_loto_express_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_loto_express_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_loto_express_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_loto_express_for_participants', 'get_loto_express_for_participants');
add_action('wp_ajax_nopriv_get_loto_express_for_participants', 'get_loto_express_for_participants');
function get_loto_express_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_loto_express_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 90; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();

	foreach ($rows as $row) {
		$array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4);
		$numbers = filter_numbers(range(1, 90), $array);

		for ($i = 1; $i <= 90; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='loto_express_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_bolshoe_sportloto_for_participants', 'insert_from_table_bolshoe_sportloto_for_participants');
add_action('wp_ajax_nopriv_insert_bolshoe_sportloto_for_participants', 'insert_from_table_bolshoe_sportloto_for_participants');
function insert_from_table_bolshoe_sportloto_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_bolshoe_sportloto_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_bolshoe_sportloto_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_bolshoe_sportloto_for_participants', 'get_bolshoe_sportloto_for_participants');
add_action('wp_ajax_nopriv_get_bolshoe_sportloto_for_participants', 'get_bolshoe_sportloto_for_participants');
function get_bolshoe_sportloto_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_bolshoe_sportloto_for_participants`');

	$array_res = array();
	for ($i = 1; $i <= 50; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	for ($i = 1; $i <= 10; $i++) {
		$array_res['COLUMN_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();
	$field_2_prev = array();

	foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5);
		$field_2 = array($row->NUMBER6, $row->NUMBER7);

		for ($i = 1; $i <= 50; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		for ($i = 1; $i <= 10; $i++) {
			// Столбец $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $field_2, $field_2_prev, $array_res['COLUMN_' . $i]);
		}
		$field_2_prev = $field_2;
		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='bolshoe_sportloto_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_zhilishnaya_lotereya_for_participants', 'insert_from_table_zhilishnaya_lotereya_for_participants');
add_action('wp_ajax_nopriv_insert_zhilishnaya_lotereya_for_participants', 'insert_from_table_zhilishnaya_lotereya_for_participants');
function insert_from_table_zhilishnaya_lotereya_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_zhilishnaya_lotereya_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_zhilishnaya_lotereya_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_zhilishnaya_lotereya_for_participants', 'get_zhilishnaya_lotereya_for_participants');
add_action('wp_ajax_nopriv_get_zhilishnaya_lotereya_for_participants', 'get_zhilishnaya_lotereya_for_participants');
function get_zhilishnaya_lotereya_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_zhilishnaya_lotereya_for_participants`');
	$array_res = array();

	for ($i = 1; $i <= 90; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}

	$numbers_prev = array();

	foreach ($rows as $row) {
		$array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4);
		$numbers = filter_numbers(range(1, 90), $array);

		for ($i = 1; $i <= 90; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='zhilishnaya_lotereya_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_zolotaya_podkova_for_participants', 'insert_from_table_zolotaya_podkova_for_participants');
add_action('wp_ajax_nopriv_insert_zolotaya_podkova_for_participants', 'insert_from_table_zolotaya_podkova_for_participants');
function insert_from_table_zolotaya_podkova_for_participants()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_zolotaya_podkova_for_participants`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_zolotaya_podkova_for_participants` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_zolotaya_podkova_for_participants', 'get_zolotaya_podkova_for_participants');
add_action('wp_ajax_nopriv_get_zolotaya_podkova_for_participants', 'get_zolotaya_podkova_for_participants');
function get_zolotaya_podkova_for_participants()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_zolotaya_podkova_for_participants`');

	$array_res = array();

	for ($i = 1; $i <= 90; $i++) {
		$array_res['NUM_' . $i] = prepare_table_values();
	}


	$numbers_prev = array();

	foreach ($rows as $row) {
		$array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3);
		$numbers = filter_numbers(range(1, 90), $array);

		for ($i = 1; $i <= 90; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='zolotaya_podkova_for_participants' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_6_45_for_gamers', 'insert_from_table_6_45_for_gamers');
add_action('wp_ajax_nopriv_insert_6_45_for_gamers', 'insert_from_table_6_45_for_gamers');
function insert_from_table_6_45_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_6_45_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_6_45_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_6_45_for_gamers', 'get_6_45_for_gamers');
add_action('wp_ajax_nopriv_get_6_45_for_gamers', 'get_6_45_for_gamers');
function get_6_45_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_6_45_for_gamers`');


	$keys = array(
		'ANY_DIV_4',
		'ANY_DIV_5',
		'ANY_DIV_6',
		'ANY_DIV_7',
		'ANY_DIV_8',
		'ANY_DIV_9',
		'ANY_DIV_10',
		'ANY_DIV_11',
		'ANY_DIV_12',
		'ANY_DIV_13',
		'ANY_DIV_14',
		'ANY_DIV_15',
		'ANY_DIV_16',
		'ANY_DIV_17',
		'ANY_DIV_18',
		'ANY_DIV_19',
		'ANY_DIV_20',
		'ANY_DIV_21',
		'ANY_DIV_22',
		'ANY_DIV_23',
		'ANY_DIV_24',
		'ANY_DIV_25',
		'ANY_DIV_26',
		'ANY_DIV_27',
		'ANY_DIV_28',
		'ANY_DIV_29',
		'ANY_DIV_30',
		'ANY_DIV_31',
		'ANY_DIV_32',
		'ANY_DIV_33',
		'ANY_DIV_34',
		'ANY_DIV_35',
		'ANY_DIV_36',
		'ANY_DIV_37',
		'ANY_DIV_38',
		'ANY_DIV_39',
		'ANY_DIV_40',
		'ANY_DIV_41',
		'ANY_DIV_42',
		'ANY_DIV_43',
		'ANY_DIV_44',
		'ANY_DIV_45',
		'MIN_EVEN',
		'MAX_EVEN',
		'SUM_MIN_MAX_EVEN',
		'ADJACENT_NUMBERS',
		'DIFF_MAX_MIN_EVEN',
		'DIFF_MAX_MIN_GT_32.5',
		'DIFF_MAX_MIN_GT_35.5',
		'SUM_EVEN',
		'EVEN_GT_ODD',
		'SUM_EVEN_GT_SUM_ODD',
		'SUM_1_10_GT_5.5',
		'SUM_1_15_GT_14.5',
		'SUM_1_15_GT_16.5',
		'SUM_1_20_GT_31.5',
		'SUM_1_22_GT_32.5',
		'SUM_1_22_GT_34.5',
		'SUM_11_20_GT_15.5',
		'SUM_16_30_GT_45.5',
		'SUM_16_30_GT_46.5',
		'SUM_21_30_GT_25.5',
		'SUM_21_45_GT_99.5',
		'SUM_23_45_GT_102.5',
		'SUM_23_45_GT_103.5',
		'SUM_31_40_GT_35.5',
		'SUM_31_45_GT_75.5',
		'SUM_31_45_GT_76.5',
		'SUM_41_45_GT_43.5',
		'SUM_EVEN_GT_28.5',
		'SUM_EVEN_GT_29.5',
		'SUM_EVEN_GT_30.5',
		'SUM_EVEN_GT_31.5',
		'SUM_EVEN_GT_32.5',
		'SUM_EVEN_GT_33.5',
		'SUM_EVEN_GT_34.5',
		'SUM_EVEN_GT_35.5',
		'SUM_EVEN_GT_36.5',
		'SUM_EVEN_GT_37.5',
		'SUM_EVEN_GT_38.5',
		'SUM_EVEN_GT_39.5',
		'SUM_EVEN_GT_40.5',
		'SUM_EVEN_GT_41.5',
		'SUM_EVEN_GT_42.5',
		'SUM_EVEN_GT_43.5',
		'SUM_EVEN_GT_44.5',
		'SUM_EVEN_GT_45.5',
		'SUM_EVEN_GT_46.5',
		'SUM_EVEN_GT_47.5',
		'SUM_EVEN_GT_48.5',
		'SUM_EVEN_GT_49.5',
		'SUM_EVEN_GT_50.5',
		'SUM_EVEN_GT_51.5',
		'SUM_EVEN_GT_52.5',
		'SUM_EVEN_GT_53.5',
		'SUM_EVEN_GT_54.5',
		'SUM_EVEN_GT_55.5',
		'SUM_EVEN_GT_56.5',
		'SUM_EVEN_GT_57.5',
		'SUM_EVEN_GT_58.5',
		'SUM_EVEN_GT_59.5',
		'SUM_EVEN_GT_60.5',
		'SUM_EVEN_GT_61.5',
		'SUM_EVEN_GT_62.5',
		'SUM_EVEN_GT_63.5',
		'SUM_EVEN_GT_64.5',
		'SUM_EVEN_GT_65.5',
		'SUM_EVEN_GT_66.5',
		'SUM_EVEN_GT_67.5',
		'SUM_EVEN_GT_68.5',
		'SUM_EVEN_GT_69.5',
		'SUM_EVEN_GT_70.5',
		'SUM_EVEN_GT_71.5',
		'SUM_EVEN_GT_72.5',
		'SUM_EVEN_GT_73.5',
		'SUM_EVEN_GT_74.5',
		'SUM_EVEN_GT_75.5',
		'SUM_EVEN_GT_76.5',
		'SUM_EVEN_GT_77.5',
		'SUM_EVEN_GT_78.5',
		'SUM_EVEN_GT_79.5',
		'SUM_EVEN_GT_80.5',
		'SUM_EVEN_GT_81.5',
		'SUM_EVEN_GT_82.5',
		'SUM_EVEN_GT_83.5',
		'SUM_EVEN_GT_84.5',
		'SUM_EVEN_GT_85.5',
		'SUM_EVEN_GT_86.5',
		'SUM_EVEN_GT_87.5',
		'SUM_EVEN_GT_88.5',
		'SUM_EVEN_GT_89.5',
		'SUM_EVEN_GT_90.5',
		'SUM_EVEN_GT_91.5',
		'SUM_EVEN_GT_92.5',
		'SUM_EVEN_GT_93.5',
		'SUM_EVEN_GT_94.5',
		'SUM_EVEN_GT_95.5',
		'SUM_EVEN_GT_96.5',
		'SUM_EVEN_GT_97.5',
		'SUM_EVEN_GT_98.5',
		'SUM_EVEN_GT_99.5',
		'SUM_EVEN_GT_100.5',
		'SUM_EVEN_GT_101.5',
		'SUM_EVEN_GT_102.5',
		'SUM_EVEN_GT_103.5',
		'SUM_EVEN_GT_104.5',
		'SUM_EVEN_GT_105.5',
		'SUM_EVEN_GT_106.5',
		'SUM_EVEN_GT_107.5',
		'SUM_ODD_GT_30.5',
		'SUM_ODD_GT_31.5',
		'SUM_ODD_GT_32.5',
		'SUM_ODD_GT_33.5',
		'SUM_ODD_GT_34.5',
		'SUM_ODD_GT_35.5',
		'SUM_ODD_GT_36.5',
		'SUM_ODD_GT_37.5',
		'SUM_ODD_GT_38.5',
		'SUM_ODD_GT_39.5',
		'SUM_ODD_GT_40.5',
		'SUM_ODD_GT_41.5',
		'SUM_ODD_GT_42.5',
		'SUM_ODD_GT_43.5',
		'SUM_ODD_GT_44.5',
		'SUM_ODD_GT_45.5',
		'SUM_ODD_GT_46.5',
		'SUM_ODD_GT_47.5',
		'SUM_ODD_GT_48.5',
		'SUM_ODD_GT_49.5',
		'SUM_ODD_GT_50.5',
		'SUM_ODD_GT_51.5',
		'SUM_ODD_GT_52.5',
		'SUM_ODD_GT_53.5',
		'SUM_ODD_GT_54.5',
		'SUM_ODD_GT_55.5',
		'SUM_ODD_GT_56.5',
		'SUM_ODD_GT_57.5',
		'SUM_ODD_GT_58.5',
		'SUM_ODD_GT_59.5',
		'SUM_ODD_GT_60.5',
		'SUM_ODD_GT_61.5',
		'SUM_ODD_GT_62.5',
		'SUM_ODD_GT_63.5',
		'SUM_ODD_GT_64.5',
		'SUM_ODD_GT_65.5',
		'SUM_ODD_GT_66.5',
		'SUM_ODD_GT_67.5',
		'SUM_ODD_GT_68.5',
		'SUM_ODD_GT_69.5',
		'SUM_ODD_GT_70.5',
		'SUM_ODD_GT_71.5',
		'SUM_ODD_GT_72.5',
		'SUM_ODD_GT_73.5',
		'SUM_ODD_GT_74.5',
		'SUM_ODD_GT_75.5',
		'SUM_ODD_GT_76.5',
		'SUM_ODD_GT_77.5',
		'SUM_ODD_GT_78.5',
		'SUM_ODD_GT_79.5',
		'SUM_ODD_GT_80.5',
		'SUM_ODD_GT_81.5',
		'SUM_ODD_GT_82.5',
		'SUM_ODD_GT_83.5',
		'SUM_ODD_GT_84.5',
		'SUM_ODD_GT_85.5',
		'SUM_ODD_GT_86.5',
		'SUM_ODD_GT_87.5',
		'SUM_ODD_GT_88.5',
		'SUM_ODD_GT_89.5',
		'SUM_ODD_GT_90.5',
		'SUM_ODD_GT_91.5',
		'SUM_ODD_GT_92.5',
		'SUM_ODD_GT_93.5',
		'SUM_ODD_GT_94.5',
		'SUM_ODD_GT_95.5',
		'SUM_ODD_GT_96.5',
		'SUM_ODD_GT_97.5',
		'SUM_ODD_GT_98.5',
		'SUM_ODD_GT_99.5',
		'SUM_ODD_GT_100.5',
		'SUM_ODD_GT_101.5',
		'SUM_ODD_GT_102.5',
		'SUM_ODD_GT_103.5',
		'SUM_ODD_GT_104.5',
		'SUM_ODD_GT_105.5',
		'SUM_ODD_GT_106.5',
		'SUM_ODD_GT_107.5',
		'SUM_ODD_GT_108.5',
		'SUM_ODD_GT_109.5',
		'SUM_ODD_GT_110.5',
		'SUM_ODD_GT_111.5',
		'SUM_MIN_MAX_GT_37.5',
		'SUM_MIN_MAX_GT_38.5',
		'SUM_MIN_MAX_GT_39.5',
		'SUM_MIN_MAX_GT_40.5',
		'SUM_MIN_MAX_GT_41.5',
		'SUM_MIN_MAX_GT_42.5',
		'SUM_MIN_MAX_GT_43.5',
		'SUM_MIN_MAX_GT_44.5',
		'SUM_MIN_MAX_GT_45.5',
		'SUM_MIN_MAX_GT_46.5',
		'SUM_MIN_MAX_GT_47.5',
		'SUM_MIN_MAX_GT_48.5',
		'SUM_MIN_MAX_GT_49.5',
		'SUM_MIN_MAX_GT_50.5',
		'SUM_MIN_MAX_GT_51.5',
		'SUM_MIN_MAX_GT_52.5',
		'SUM_MIN_MAX_GT_53.5',
		'SUM_MIN_MAX_GT_54.5',
		'NUM_1_GT_22.5',
		'NUM_1_GT_23.5',
		'NUM_1_GT_30.5',
		'NUM_2_GT_22.5',
		'NUM_2_GT_23.5',
		'NUM_2_GT_30.5',
		'NUM_3_GT_22.5',
		'NUM_3_GT_23.5',
		'NUM_3_GT_30.5',
		'NUM_4_GT_22.5',
		'NUM_4_GT_23.5',
		'NUM_4_GT_30.5',
		'NUM_5_GT_22.5',
		'NUM_5_GT_23.5',
		'NUM_5_GT_30.5',
		'NUM_6_GT_22.5',
		'NUM_6_GT_23.5',
		'NUM_6_GT_30.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'SUM_GT_102.5',
		'SUM_GT_103.5',
		'SUM_GT_104.5',
		'SUM_GT_105.5',
		'SUM_GT_106.5',
		'SUM_GT_107.5',
		'SUM_GT_108.5',
		'SUM_GT_109.5',
		'SUM_GT_110.5',
		'SUM_GT_111.5',
		'SUM_GT_112.5',
		'SUM_GT_113.5',
		'SUM_GT_114.5',
		'SUM_GT_115.5',
		'SUM_GT_116.5',
		'SUM_GT_117.5',
		'SUM_GT_118.5',
		'SUM_GT_119.5',
		'SUM_GT_120.5',
		'SUM_GT_121.5',
		'SUM_GT_122.5',
		'SUM_GT_123.5',
		'SUM_GT_124.5',
		'SUM_GT_125.5',
		'SUM_GT_126.5',
		'SUM_GT_127.5',
		'SUM_GT_128.5',
		'SUM_GT_129.5',
		'SUM_GT_130.5',
		'SUM_GT_131.5',
		'SUM_GT_132.5',
		'SUM_GT_133.5',
		'SUM_GT_134.5',
		'SUM_GT_135.5',
		'SUM_GT_136.5',
		'SUM_GT_137.5',
		'SUM_GT_138.5',
		'SUM_GT_139.5',
		'SUM_GT_140.5',
		'SUM_GT_141.5',
		'SUM_GT_142.5',
		'SUM_GT_143.5',
		'SUM_GT_144.5',
		'SUM_GT_145.5',
		'SUM_GT_146.5',
		'SUM_GT_147.5',
		'SUM_GT_148.5',
		'SUM_GT_149.5',
		'SUM_GT_150.5',
		'SUM_GT_151.5',
		'SUM_GT_152.5',
		'SUM_GT_153.5',
		'SUM_GT_154.5',
		'SUM_GT_155.5',
		'SUM_GT_156.5',
		'SUM_GT_157.5',
		'SUM_GT_158.5',
		'SUM_GT_159.5',
		'SUM_GT_160.5',
		'SUM_GT_161.5',
		'SUM_GT_162.5',
		'SUM_GT_163.5',
		'SUM_GT_164.5',
		'SUM_GT_165.5',
		'SUM_GT_166.5',
		'SUM_GT_167.5',
		'SUM_GT_168.5',
		'SUM_GT_169.5',
		'SUM_GT_170.5',
		'SUM_GT_171.5',
		'SUM_GT_172.5',
		'SUM_GT_173.5',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'MIN_GT_4.5',
		'MIN_GT_5.5',
		'MIN_GT_6.5',
		'MIN_GT_7.5',
		'MIN_GT_8.5',
		'MIN_GT_9.5',
		'MIN_GT_10.5',
		'MIN_GT_11.5',
		'MIN_GT_12.5',
		'MAX_GT_33.5',
		'MAX_GT_34.5',
		'MAX_GT_35.5',
		'MAX_GT_36.5',
		'MAX_GT_37.5',
		'MAX_GT_38.5',
		'MAX_GT_39.5',
		'MAX_GT_40.5',
		'MAX_GT_41.5',
		'MAX_GT_42.5',
		'MAX_GT_43.5',
		'MAX_GT_44.5',
		'ODD_EQ_0',
		'ODD_EQ_1',
		'ODD_EQ_2',
		'ODD_EQ_4',
		'ODD_EQ_5',
		'ODD_EQ_6',
		'EVEN_EQ_0',
		'EVEN_EQ_1',
		'EVEN_EQ_2',
		'EVEN_EQ_4',
		'EVEN_EQ_5',
		'EVEN_EQ_6',
		'EVEN_LT_3',
		'EVEN_EQ_3',
		'EVEN_GT_3',
		'ODD_LT_3',
		'ODD_EQ_3',
		'ODD_GT_3',
		'COUNT_1_10_EQ_1',
		'COUNT_1_15_LT_2',
		'COUNT_1_15_EQ_2',
		'COUNT_1_15_GT_2',
		'COUNT_1_20_EQ_3',
		'COUNT_1_22_LT_3',
		'COUNT_1_22_EQ_3',
		'COUNT_1_22_GT_3',
		'COUNT_11_20_EQ_1',
		'COUNT_16_30_LT_2',
		'COUNT_16_30_EQ_2',
		'COUNT_16_30_GT_2',
		'COUNT_21_30_EQ_1',
		'COUNT_21_45_EQ_3',
		'COUNT_23_45_LT_3',
		'COUNT_23_45_EQ_3',
		'COUNT_23_45_GT_3',
		'COUNT_31_40_EQ_1',
		'COUNT_31_45_LT_2',
		'COUNT_31_45_EQ_2',
		'COUNT_31_45_GT_2',
		'COUNT_41_45_EQ_1'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6);

		for ($j = 4; $j <= 45; $j++) {
			// Любой из выпавших номеров кратен $j (0 не кратное)
			calculate_case(function ($nums) use ($j) {
				$count = count($nums);

				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] != 0 && $nums[$i] % $j == 0) {
						return true;
					}
				}

				return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $j]);
		}

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Выпадут соседние номера
		calculate_case(function ($nums) {
			for ($i = 0; $i < count($nums); $i++) {
				if ($i > 0) {
					if (($nums[$i] == $nums[$i - 1] - 1 || $nums[$i] == $nums[$i + 1] - 1) && ($nums[$i] == $nums[$i - 1] + 1 || $nums[$i + 1] + 1)) {
						return true;
					}
				}
			}
			return false;
		}, $numbers, $numbers_prev, $array_res['ADJACENT_NUMBERS']);

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		// Разность наибольшего и наименьшего из выпавших номеров Больше 32.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 32.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_32.5']);

		// Разность наибольшего и наименьшего из выпавших номеров Больше 35.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 35.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_35.5']);

		// Сумма всех выпавших номеров Чет
		calculate_case(function ($nums) {
			(array_sum($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN']);

		// Четных больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_ODD']);

		// Сумма выпавших четных номеров больше, чем сумма выпавших НЕчетных номеров
		calculate_case(function ($nums) {
			$even = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			$odd = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_SUM_ODD']);

		// Сумма всех выпавших номеров от 1 до 10 Больше 5.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			});
			return array_sum($filtered_list) > 5.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_10_GT_5.5']);

		// Сумма всех выпавших номеров от 1 до 15 Больше 14.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 15;
			});
			return array_sum($filtered_list) > 14.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_15_GT_14.5']);

		// Сумма всех выпавших номеров от 1 до 15 Больше 16.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 15;
			});
			return array_sum($filtered_list) > 16.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_15_GT_16.5']);

		// Сумма всех выпавших номеров от 1 до 20 Больше 31.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 20;
			});
			return array_sum($filtered_list) > 31.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_20_GT_31.5']);

		// Сумма всех выпавших номеров от 1 до 22 Больше 32.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 1 && $num <= 22;
				}
			);
			return array_sum($filtered_list) > 32.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_22_GT_32.5']);

		// Сумма всех выпавших номеров от 1 до 22 Больше 34.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 1 && $num <= 22;
				}
			);
			return array_sum($filtered_list) > 34.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_22_GT_34.5']);

		// Сумма всех выпавших номеров от 11 до 20 Больше 15.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 11 && $num <= 20;
				}
			);
			return array_sum($filtered_list) > 15.5;
		}, $numbers, $numbers_prev, $array_res['SUM_11_20_GT_15.5']);

		// Сумма всех выпавших номеров от 16 до 30 Больше 45.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 16 && $num <= 30;
				}
			);
			return array_sum($filtered_list) > 45.5;
		}, $numbers, $numbers_prev, $array_res['SUM_16_30_GT_45.5']);

		// Сумма всех выпавших номеров от 16 до 30 Больше 46.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 16 && $num <= 30;
				}
			);
			return array_sum($filtered_list) > 46.5;
		}, $numbers, $numbers_prev, $array_res['SUM_16_30_GT_46.5']);

		// Сумма всех выпавших номеров от 21 до 30 Больше 25.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 21 && $num <= 30;
				}
			);
			return array_sum($filtered_list) > 25.5;
		}, $numbers, $numbers_prev, $array_res['SUM_21_30_GT_25.5']);

		// Сумма всех выпавших номеров от 21 до 45 Больше 99.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 21 && $num <= 45;
				}
			);
			return array_sum($filtered_list) > 99.5;
		}, $numbers, $numbers_prev, $array_res['SUM_21_45_GT_99.5']);

		// Сумма всех выпавших номеров от 23 до 45 Больше 102.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 23 && $num <= 45;
				}
			);
			return array_sum($filtered_list) > 102.5;
		}, $numbers, $numbers_prev, $array_res['SUM_23_45_GT_102.5']);

		// Сумма всех выпавших номеров от 23 до 45 Больше 103.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 23 && $num <= 45;
				}
			);
			return array_sum($filtered_list) > 103.5;
		}, $numbers, $numbers_prev, $array_res['SUM_23_45_GT_103.5']);

		// Сумма всех выпавших номеров от 31 до 40 Больше 35.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 31 && $num <= 40;
				}
			);
			return array_sum($filtered_list) > 35.5;
		}, $numbers, $numbers_prev, $array_res['SUM_31_40_GT_35.5']);

		// Сумма всех выпавших номеров от 31 до 45 Больше 75.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 31 && $num <= 45;
				}
			);
			return array_sum($filtered_list) > 75.5;
		}, $numbers, $numbers_prev, $array_res['SUM_31_45_GT_75.5']);

		// Сумма всех выпавших номеров от 31 до 45 Больше 76.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 31 && $num <= 45;
				}
			);
			return array_sum($filtered_list) > 76.5;
		}, $numbers, $numbers_prev, $array_res['SUM_31_45_GT_76.5']);

		// Сумма всех выпавших номеров от 41 до 45 Больше 43.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 41 && $num <= 45;
				}
			);
			return array_sum($filtered_list) > 43.5;
		}, $numbers, $numbers_prev, $array_res['SUM_41_45_GT_43.5']);

		// чтоб оно не считало по 10000 раз одно и тоже 
		$filtered_even = array_filter($numbers, function ($num) {
			return $num % 2 == 0;
		});
		$filtered_even_prev = array_filter($numbers_prev, function ($num) {
			return $num % 2 == 0;
		});

		$filtered_even_sum = array_sum($filtered_even);
		$filtered_even_prev_sum = array_sum($filtered_even_prev);

		// Сумма всех выпавших четных номеров Больше $i + 0.5
		for ($i = 28; $i <= 107; $i++) {
			calculate_case(function ($sum) use ($i) {
				return $sum > ($i + 0.5);
			}, $filtered_even_sum, $filtered_even_prev_sum, $array_res['SUM_EVEN_GT_' . $i . '.5']);
		}

		$filtered_odd = array_filter($numbers, function ($num) {
			return $num % 2 != 0;
		});
		$filtered_odd_prev = array_filter($numbers_prev, function ($num) {
			return $num % 2 != 0;
		});

		$filtered_odd_sum = array_sum($filtered_odd);
		$filtered_odd_prev_sum = array_sum($filtered_odd_prev);

		for ($i = 30; $i <= 111; $i++) {
			// Сумма всех выпавших НЕчетных номеров Больше $i + 0.5
			calculate_case(function ($sum) use ($i) {
				return $sum > ($i + 0.5);
			}, $filtered_odd_sum, $filtered_odd_prev_sum, $array_res['SUM_ODD_GT_' . $i . '.5']);
		}

		$min_numbers = min($numbers);
		$max_numbers = max($numbers);

		$min_numbers_prev = min($numbers_prev);
		$max_numbers_prev = max($numbers_prev);

		$sum_max_min = ($max_numbers + $min_numbers);
		$sum_max_min_prev = ($max_numbers_prev + $min_numbers_prev);

		for ($i = 37; $i <= 54; $i++) {
			// Сумма наименьшего и наибольшего из выпавших номеров Больше $i + 0.5;
			calculate_case(function ($sum_max_min) use ($i) {
				return $sum_max_min > ($i + 0.5);
			}, $sum_max_min, $sum_max_min_prev, $array_res['SUM_MIN_MAX_GT_' . $i . '.5']);
		}

		for ($i = 1; $i <= 6; $i++) {
			// $i-й номер больше 22.5
			calculate_case(function ($nums) use ($i) {
				return $nums[$i - 1] > 22.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i . '_GT_22.5']);

			// $i-й номер больше 23.5
			calculate_case(function ($nums) use ($i) {
				return $nums[$i - 1] > 23.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i . '_GT_23.5']);

			// $i-й номер больше 30.5
			calculate_case(function ($nums) use ($i) {
				return $nums[$i - 1] > 30.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i . '_GT_30.5']);

			// $i-й номер Чет
			calculate_case(function ($nums) use ($i) {
				return $nums[$i - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i . 'EVEN']);
		}

		for ($i = 102; $i <= 173; $i++) {
			// Сумма всех выпавших номеров Больше $i + 0.5
			calculate_case(function ($nums) use ($i) {
				return array_sum($nums) > ($i + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_GT_' . $i . '.5']);
		}

		$min_numbers;
		$min_numbers_prev;
		$max_numbers;
		$max_numbers_prev;

		for ($i = 1; $i <= 12; $i++) {
			// Наименьший выпавший номер Больше ($i +0.5)
			calculate_case(function ($nums) use ($i) {
				return min($nums) > ($i + 0.5);
			}, $numbers, $numbers_prev, $array_res['MIN_GT_' . $i . '.5']);
		}

		for ($i = 33; $i <= 44; $i++) {
			// Наибольший выпавший номер Больше 33.5
			calculate_case(function ($nums) use ($i) {
				return max($nums) > ($i + 0.5);
			}, $numbers, $numbers_prev, $array_res['MAX_GT_' . $i . '.5']);
		}


		// Количество выпавших НЕчетных номеров Ровно 0
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd == 0;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_0']);

		// Количество выпавших НЕчетных номеров Ровно 1
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd == 1;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_1']);

		// Количество выпавших НЕчетных номеров Ровно 2
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd == 2;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_2']);

		// Количество выпавших НЕчетных номеров Ровно 4
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd == 4;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_4']);

		// Количество выпавших НЕчетных номеров Ровно 5
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd == 5;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_5']);

		// Количество выпавших НЕчетных номеров Ровно 6
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd == 6;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_6']);

		// Количество выпавших Четных номеров Ровно 0
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even == 0;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_0']);

		// Количество выпавших Четных номеров Ровно 1
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even == 1;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_1']);

		// Количество выпавших Четных номеров Ровно 2
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even == 2;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_2']);

		// Количество выпавших Четных номеров Ровно 4
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even == 4;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_4']);

		// Количество выпавших Четных номеров Ровно 5
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even == 5;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_5']);

		// Количество выпавших Четных номеров Ровно 6
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even == 6;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_6']);

		// Количество выпавших Четных номеров Меньше 3
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even < 3;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_3']);

		// Количество выпавших Четных номеров Ровно 3
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even == 3;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_3']);

		// Количество выпавших Четных номеров Больше 3
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even > 3;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_3']);

		// Количество выпавших НЕчетных номеров Меньше 3
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd < 3;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_3']);

		// Количество выпавших НЕчетных номеров Ровно 3
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd == 3;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_3']);

		// Количество выпавших НЕчетных номеров Больше 3
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd > 3;
		}, $numbers, $numbers_prev, $array_res['ODD_GT_3']);

		// Количество выпавших номеров от 1 до 10 включительно Ровно 1
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 10;
					}
				)
			);
			return $filtered < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_EQ_1']);

		// Количество выпавших номеров от 1 до 15 включительно Меньше 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 15;
					}
				)
			);
			return $filtered < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_15_LT_2']);

		// Количество выпавших номеров от 1 до 15 включительно Ровно 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 15;
					}
				)
			);
			return $filtered == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_15_EQ_2']);

		// Количество выпавших номеров от 1 до 15 включительно Больше 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 15;
					}
				)
			);
			return $filtered > 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_15_GT_2']);

		// Количество выпавших номеров от 1 до 20 включительно Ровно 3
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 20;
					}
				)
			);
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_20_EQ_3']);

		// Количество выпавших номеров от 1 до 22 включительно Меньше 3
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 22;
					}
				)
			);
			return $filtered < 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_22_LT_3']);

		// Количество выпавших номеров от 1 до 22 включительно Ровно 3
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 22;
					}
				)
			);
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_22_EQ_3']);

		// Количество выпавших номеров от 1 до 22 включительно Больше 3
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 22;
					}
				)
			);
			return $filtered > 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_22_GT_3']);

		// Количество выпавших номеров от 11 до 20 включительно Ровно 1
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 11 && $num <= 20;
					}
				)
			);
			return $filtered == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_EQ_1']);

		// Количество выпавших номеров от 16 до 30 включительно Меньше 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 16 && $num <= 30;
					}
				)
			);
			return $filtered < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_16_30_LT_2']);

		// Количество выпавших номеров от 16 до 30 включительно Ровно 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 16 && $num <= 30;
					}
				)
			);
			return $filtered == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_16_30_EQ_2']);

		// Количество выпавших номеров от 16 до 30 включительно Больше 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 16 && $num <= 30;
					}
				)
			);
			return $filtered > 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_16_30_GT_2']);

		// Количество выпавших номеров от 21 до 30 включительно Ровно 1
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 21 && $num <= 30;
					}
				)
			);
			return $filtered == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_30_EQ_1']);

		// Количество выпавших номеров от 21 до 45 включительно Ровно 3
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 21 && $num <= 45;
					}
				)
			);
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_45_EQ_3']);

		// Количество выпавших номеров от 23 до 45 включительно Меньше 3
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 23 && $num <= 45;
					}
				)
			);
			return $filtered < 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_23_45_LT_3']);

		// Количество выпавших номеров от 23 до 45 включительно Ровно 3
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 23 && $num <= 45;
					}
				)
			);
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_23_45_EQ_3']);

		// Количество выпавших номеров от 23 до 45 включительно Больше 3
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 23 && $num <= 45;
					}
				)
			);
			return $filtered > 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_23_45_GT_3']);

		// Количество выпавших номеров от 31 до 40 включительно Ровно 1
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 31 && $num <= 40;
					}
				)
			);
			return $filtered == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_40_EQ_1']);

		// Количество выпавших номеров от 31 до 45 включительно Меньше 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 31 && $num <= 45;
					}
				)
			);
			return $filtered < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_45_LT_2']);

		// Количество выпавших номеров от 31 до 45 включительно Ровно 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 31 && $num <= 45;
					}
				)
			);
			return $filtered == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_45_EQ_2']);

		// Количество выпавших номеров от 31 до 45 включительно Больше 2
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 31 && $num <= 45;
					}
				)
			);
			return $filtered > 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_45_GT_2']);

		// Количество выпавших номеров от 41 до 45 включительно Ровно 1
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 41 && $num <= 45;
					}
				)
			);
			return $filtered == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_41_45_EQ_1']);


		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='6_45_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}


add_action('wp_ajax_insert_5_36_for_gamers', 'insert_from_table_5_36_for_gamers');
add_action('wp_ajax_nopriv_insert_5_36_for_gamers', 'insert_from_table_5_36_for_gamers');
function insert_from_table_5_36_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_5_36_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_5_36_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_5_36_for_gamers', 'get_5_36_for_gamers');
add_action('wp_ajax_nopriv_get_5_36_for_gamers', 'get_5_36_for_gamers');
function get_5_36_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_5_36_for_gamers`');
	$keys = array(
		'ANY_DIV_4',
		'ANY_DIV_5',
		'ANY_DIV_6',
		'ANY_DIV_7',
		'ANY_DIV_8',
		'ANY_DIV_9',
		'ANY_DIV_10',
		'ANY_DIV_11',
		'ANY_DIV_12',
		'ANY_DIV_13',
		'ANY_DIV_14',
		'ANY_DIV_15',
		'ANY_DIV_16',
		'ANY_DIV_17',
		'ANY_DIV_18',
		'ANY_DIV_19',
		'ANY_DIV_20',
		'ANY_DIV_21',
		'ANY_DIV_22',
		'ANY_DIV_23',
		'ANY_DIV_24',
		'ANY_DIV_25',
		'ANY_DIV_26',
		'ANY_DIV_27',
		'ANY_DIV_28',
		'ANY_DIV_29',
		'ANY_DIV_30',
		'ANY_DIV_31',
		'ANY_DIV_33',
		'ANY_DIV_34',
		'ANY_DIV_35',
		'ANY_DIV_36',
		'MIN_EVEN',
		'MAX_EVEN',
		'SUM_MIN_MAX_EVEN',
		'ADJACENT_NUMBERS',
		'COUNT_EVEN_GT_COUNT_ODD',
		'SUM_EVEN_GT_SUM_ODD',
		'SUM_EVEN',
		'SUM_1_10_GT_5.5',
		'SUM_1_12_GT_10.5',
		'SUM_1_12_GT_13.5',
		'SUM_1_18_GT_19.5',
		'SUM_1_18_GT_23.5',
		'SUM_11_20_GT_15.5',
		'SUM_13_24_GT_31.5',
		'SUM_13_24_GT_37.5',
		'SUM_19_36_GT_55.5',
		'SUM_19_36_GT_67.5',
		'SUM_21_30_GT_25.5',
		'SUM_25_36_GT_55.5',
		'SUM_25_36_GT_61.5',
		'SUM_31_36_GT_33.5',
		'SUM_EVEN_GT_33.5',
		'SUM_EVEN_GT_39.5',
		'SUM_EVEN_GT_42.5',
		'SUM_EVEN_GT_45.5',
		'SUM_EVEN_GT_46.5',
		'SUM_EVEN_GT_50.5',
		'SUM_EVEN_GT_53.5',
		'SUM_EVEN_GT_61.5',
		'SUM_ODD_GT_30.5',
		'SUM_ODD_GT_36.5',
		'SUM_ODD_GT_39.5',
		'SUM_ODD_GT_43.5',
		'SUM_ODD_GT_47.5',
		'SUM_ODD_GT_50.5',
		'SUM_ODD_GT_57.5',
		'SUM_MIN_MAX_GT_33.5',
		'SUM_MIN_MAX_GT_35.5',
		'SUM_MIN_MAX_GT_36.5',
		'SUM_MIN_MAX_GT_37.5',
		'SUM_MIN_MAX_GT_38.5',
		'SUM_MIN_MAX_GT_40.5',
		'SUM_MAX_MIN_GT_EVEN',
		'NUM_1_GT_18.5',
		'NUM_2_GT_18.5',
		'NUM_3_GT_18.5',
		'NUM_4_GT_18.5',
		'NUM_5_GT_18.5',
		'NUM_1_GT_25.5',
		'NUM_2_GT_25.5',
		'NUM_3_GT_25.5',
		'NUM_4_GT_25.5',
		'NUM_5_GT_25.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'DIFF_MAX_MIN_EVEN',
		'DIFF_MAX_MIN_GT_24.5',
		'DIFF_MAX_MIN_GT_25.5',
		'SUM_GT_80.5',
		'SUM_GT_82.5',
		'SUM_GT_86.5',
		'SUM_GT_92.5',
		'SUM_GT_98.5',
		'SUM_GT_102.5',
		'SUM_GT_104.5',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'MIN_GT_4.5',
		'MIN_GT_5.5',
		'MIN_GT_6.5',
		'MIN_GT_7.5',
		'MIN_GT_8.5',
		'MIN_GT_9.5',
		'MIN_GT_10.5',
		'MIN_GT_11.5',
		'MAX_GT_25.5',
		'MAX_GT_26.5',
		'MAX_GT_27.5',
		'MAX_GT_28.5',
		'MAX_GT_29.5',
		'MAX_GT_30.5',
		'MAX_GT_31.5',
		'MAX_GT_32.5',
		'MAX_GT_33.5',
		'MAX_GT_34.5',
		'MAX_GT_35.5',
		'ODD_LT_2',
		'ODD_EQ_2',
		'ODD_GT_2',
		'EVEN_LT_2',
		'EVEN_EQ_2',
		'EVEN_GT_2',
		'COUNT_1_12_LT_2',
		'COUNT_1_12_EQ_2',
		'COUNT_1_12_GT_2',
		'COUNT_1_18_LT_2',
		'COUNT_1_18_EQ_2',
		'COUNT_1_18_GT_2',
		'COUNT_13_24_LT_2',
		'COUNT_13_24_EQ_2',
		'COUNT_13_24_GT_2',
		'COUNT_19_36_LT_2',
		'COUNT_19_36_EQ_2',
		'COUNT_19_36_GT_2',
		'COUNT_25_36_LT_2',
		'COUNT_25_36_EQ_2',
		'COUNT_25_36_GT_2',
		'ODD_EQ_0',
		'ODD_EQ_1',
		'ODD_EQ_2',
		'ODD_EQ_3',
		'ODD_EQ_4',
		'ODD_EQ_5',
		'EVEN_EQ_0',
		'EVEN_EQ_1',
		'EVEN_EQ_2',
		'EVEN_EQ_3',
		'EVEN_EQ_4',
		'EVEN_EQ_5',
		'COUNT_1_10_EQ_1',
		'COUNT_1_12_EQ_2',
		'COUNT_1_18_EQ_2',
		'COUNT_11_20_EQ_1',
		'COUNT_13_24_EQ_2',
		'COUNT_19_36_EQ_2',
		'COUNT_21_30_EQ_1',
		'COUNT_25_36_EQ_2',
		'COUNT_31_36_EQ_1'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6);

		for ($number = 4; $$number <= 36; $number++) {
			// Любой из выпавших номеров кратен 4 (0 не кратное)
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] % $number == 0) {
						return true;
					}
				}return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $number]);
		}

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Выпадут соседние номера
		calculate_case(function ($nums) {
			for ($i = 0; $i < count($nums); $i++) {
				if ($i > 0) {
					if (($nums[$i] == $nums[$i - 1] - 1 || $nums[$i] == $nums[$i + 1] - 1) && ($nums[$i] == $nums[$i - 1] + 1 || $nums[$i + 1] + 1)) {
						return true;
					}
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ADJACENT_NUMBERS']);

		// Четных номеров выпадет больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		// Сумма выпавших четных номеров больше, чем сумма выпавших НЕчетных номеров
		calculate_case(function ($nums) {
			$even = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			$odd = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_SUM_ODD']);

		// Сумма всех выпавших номеров ЧЕТ
		calculate_case(function ($nums) {
			return array_sum($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN']);

		// Сумма всех выпавших номеров от 1 до 10 Больше 5.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			});
			return array_sum($filtered_list) > 5.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_10_GT_5.5']);

		// Сумма всех выпавших номеров от 1 до 12 Больше 10.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 12;
			});
			return array_sum($filtered_list) > 10.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_12_GT_10.5']);

		// Сумма всех выпавших номеров от 1 до 12 Больше 13.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 12;
			});
			return array_sum($filtered_list) > 13.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_12_GT_13.5']);

		// Сумма всех выпавших номеров от 1 до 18 Больше 19.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 18;
			});
			return array_sum($filtered_list) > 19.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_18_GT_19.5']);

		// Сумма всех выпавших номеров от 1 до 18 Больше 23.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 18;
			});
			return array_sum($filtered_list) > 23.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_18_GT_23.5']);

		// Сумма всех выпавших номеров от 11 до 20 Больше 15.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			});
			return array_sum($filtered_list) > 15.5;
		}, $numbers, $numbers_prev, $array_res['SUM_11_20_GT_15.5']);

		// Сумма всех выпавших номеров от 13 до 24 Больше 31.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 13 && $num <= 24;
			});
			return array_sum($filtered_list) > 31.5;
		}, $numbers, $numbers_prev, $array_res['SUM_13_24_GT_31.5']);

		// Сумма всех выпавших номеров от 13 до 24 Больше 37.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 13 && $num <= 24;
			});
			return array_sum($filtered_list) > 37.5;
		}, $numbers, $numbers_prev, $array_res['SUM_13_24_GT_37.5']);

		// Сумма всех выпавших номеров от 19 до 36 Больше 55.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 19 && $num <= 36;
			});
			return array_sum($filtered_list) > 55.5;
		}, $numbers, $numbers_prev, $array_res['SUM_19_36_GT_55.5']);

		// Сумма всех выпавших номеров от 19 до 36 Больше 67.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 19 && $num <= 36;
			});
			return array_sum($filtered_list) > 67.5;
		}, $numbers, $numbers_prev, $array_res['SUM_19_36_GT_67.5']);

		// Сумма всех выпавших номеров от 21 до 30 Больше 25.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 21 && $num <= 30;
			});
			return array_sum($filtered_list) > 25.5;
		}, $numbers, $numbers_prev, $array_res['SUM_21_30_GT_25.5']);

		// Сумма всех выпавших номеров от 25 до 36 Больше 55.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 25 && $num <= 36;
			});
			return array_sum($filtered_list) > 55.5;
		}, $numbers, $numbers_prev, $array_res['SUM_25_36_GT_55.5']);

		// Сумма всех выпавших номеров от 25 до 36 Больше 61.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 25 && $num <= 36;
			});
			return array_sum($filtered_list) > 61.5;
		}, $numbers, $numbers_prev, $array_res['SUM_25_36_GT_61.5']);

		// Сумма всех выпавших номеров от 31 до 36 Больше 33.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 31 && $num <= 36;
			});
			return array_sum($filtered_list) > 33.5;
		}, $numbers, $numbers_prev, $array_res['SUM_31_36_GT_33.5']);



		// Сумма всех выпавших Четных номеров Больше 33.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 33.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_33.5']);

		// Сумма всех выпавших Четных номеров Больше 39.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 39.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_39.5']);

		// Сумма всех выпавших Четных номеров Больше 42.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 42.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_42.5']);

		// Сумма всех выпавших Четных номеров Больше 45.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 45.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_45.5']);

		// Сумма всех выпавших Четных номеров Больше 46.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 46.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_46.5']);

		// Сумма всех выпавших Четных номеров Больше 50.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 50.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_50.5']);

		// Сумма всех выпавших Четных номеров Больше 53.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 53.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_53.5']);

		// Сумма всех выпавших Четных номеров Больше 61.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 61.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_61.5']);


		// Сумма всех выпавших НЕчетных номеров Больше 30.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 30.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_30.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 36.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 36.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_36.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 39.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 39.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_39.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 43.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 43.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_43.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 47.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 47.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_47.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 50.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 50.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_50.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 57.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 57.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_57.5']);


		// Сумма наименьшего и наибольшего из выпавших номеров Больше 33.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 33.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_33.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров Больше 35.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 35.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_35.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров Больше 36.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 36.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_36.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров Больше 37.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 37.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_37.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров Больше 38.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 38.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_38.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров Больше 40.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 40.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_40.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров ЧЕТ
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MAX_MIN_GT_EVEN']);

		for ($i = 1; $i <= 5; $i++) {
			// $i-й номер Больше 18.5
			calculate_case(function ($nums) use ($i) {
				return $nums[$i - 1] > 18.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . ($i + 1) . '_GT_18.5']);

			// $i-й номер Больше 25.5
			calculate_case(function ($nums) use ($i) {
				return $nums[$i - 1] > 25.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . ($i + 1) . '_GT_25.5']);

			// $i-й номер Чет
			calculate_case(function ($nums) use ($i) {
				return $nums[$i - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . ($i + 1) . '_EVEN']);
		}

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		// Разность наибольшего и наименьшего из выпавших номеров Больше 24.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 24.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_24.5']);

		// Разность наибольшего и наименьшего из выпавших номеров Больше 25.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 25.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_25.5']);

		// Сумма всех выпавших номеров Больше 80.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 80.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_80.5']);

		// Сумма всех выпавших номеров Больше 82.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 82.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_82.5']);

		// Сумма всех выпавших номеров Больше 86.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 86.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_86.5']);

		// Сумма всех выпавших номеров Больше 92.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 92.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_92.5']);

		// Сумма всех выпавших номеров Больше 98.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 98.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_98.5']);

		// Сумма всех выпавших номеров Больше 102.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 102.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_102.5']);

		// Сумма всех выпавших номеров Больше 104.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 104.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_104.5']);

		$min_numbers = min($numbers);
		$min_numbers_prev = min($numbers_prev);

		for ($i = 1; $i <= 11; $i++) {
			// Наименьший выпавший номер Больше $i + 0.5
			calculate_case(function ($min) use ($i) {
				return $min > ($i + 0.5);
			}, $min_numbers, $min_numbers_prev, $array_res['MIN_GT_' . $i . '.5']);
		}
		$max_numbers = max($numbers);
		$max_numbers_prev = max($numbers_prev);
		for ($i = 25; $i <= 35; $i++) {
			// Наибольший выпавший номер Больше $i + 0.5
			calculate_case(function ($max) use ($i) {
				return $max > ($i + 0.5);
			}, $max_numbers, $max_numbers_prev, $array_res['MAX_GT_' . $i . '.5']);
		}

		// Количество выпавших НЕчетных номеров Меньше 2
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd < 2;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_2']);

		// Количество выпавших НЕчетных номеров Ровно 2
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd == 2;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_2']);

		// Количество выпавших НЕчетных номеров Больше 2
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd > 2;
		}, $numbers, $numbers_prev, $array_res['ODD_GT_2']);

		// Количество выпавших Четных номеров Меньше 2
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even < 2;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_2']);

		// Количество выпавших Четных номеров Ровно 2
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even == 2;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_2']);

		// Количество выпавших Четных номеров Больше 2
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even > 2;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_2']);

		$ranges = [[1, 12], [1, 18], [13, 24], [19, 36], [25, 36]];

		foreach ($ranges as $range) {
			$filtered = array_filter($numbers, function ($num) use ($range) {
				return $num >= $range[0] && $num <= $range[1];
			});
			$filtered_prev = array_filter($numbers_prev, function ($num) use ($range) {
				return $num >= $range[0] && $num <= $range[1];
			});

			// Количество выпавших номеров от $range[0] до $range[1] Меньше 2
			calculate_case(function ($filtered) {
				return count($filtered) < 2;
			}, $filtered, $filtered_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_LT_2']);

			// Количество выпавших номеров от $range[0] до $range[1] Ровно 2
			calculate_case(function ($filtered) {
				return count($filtered) == 2;
			}, $filtered, $filtered_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_EQ_2']);

			// Количество выпавших номеров от $range[0] до $range[1] Больше 2
			calculate_case(function ($filtered) {
				return count($filtered) > 2;
			}, $filtered, $filtered_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_GT_2']);
		}


		for ($number = 0; $number <= 5; $number++) {
			// Количество выпавших НЕчетных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$odd = count(array_filter($nums, function ($num) {
					return $num % 2 != 0;
				}));
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);

			// Количество выпавших Четных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$even = count(array_filter($nums, function ($num) {
					return $num % 2 == 0;
				}));
				return $even == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);
		}


		// Количество выпавших номеров от 1 до 10 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			});
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_EQ_1']);

		// Количество выпавших номеров от 1 до 12 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 12;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_12_EQ_2']);

		// Количество выпавших номеров от 1 до 18 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 18;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_18_EQ_2']);

		// Количество выпавших номеров от 11 до 20 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			});
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_EQ_1']);

		// Количество выпавших номеров от 13 до 24 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 13 && $num <= 24;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_13_24_EQ_2']);

		// Количество выпавших номеров от 19 до 36 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 19 && $num <= 36;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_19_36_EQ_2']);

		// Количество выпавших номеров от 21 до 30 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 21 && $num <= 30;
			});
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_30_EQ_1']);

		// Количество выпавших номеров от 25 до 36 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 25 && $num <= 36;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_25_36_EQ_2']);

		// Количество выпавших номеров от 31 до 36 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 31 && $num <= 36;
			});
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_36_EQ_1']);

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='5_36_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}


add_action('wp_ajax_insert_4_20_for_gamers', 'insert_from_table_4_20_for_gamers');
add_action('wp_ajax_nopriv_insert_4_20_for_gamers', 'insert_from_table_4_20_for_gamers');
function insert_from_table_4_20_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_4_20_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_4_20_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_4_20_for_gamers', 'get_4_20_for_gamers');
add_action('wp_ajax_nopriv_get_4_20_for_gamers', 'get_4_20_for_gamers');
function get_4_20_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_4_20_for_gamers`');
	$keys = array(
		'ANY_DIV_7',
		'ANY_DIV_8',
		'ANY_DIV_9',
		'ANY_DIV_10',
		'ANY_DIV_11',
		'MIN_EVEN',
		'MAX_EVEN',
		'SUM_MIN_MAX_EVEN',
		'DIFF_MAX_MIN_EVEN',
		'SAME_NUMBERS',
		'COUNT_EVEN_GT_COUNT_ODD',
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'SUM_ODD_GT_37.5',
		'SUM_ODD_GT_38.5',
		'SUM_ODD_GT_39.5',
		'SUM_ODD_GT_40.5',
		'SUM_ODD_GT_41.5',
		'SUM_EVEN_GT_40.5',
		'SUM_EVEN_GT_42.5',
		'SUM_EVEN_GT_43.5',
		'SUM_EVEN_GT_44.5',
		'SUM_EVEN_GT_45.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'NUM_8_EVEN',
		'DIFF_MAX_MIN_GT_12.5',
		'DIFF_MAX_MIN_GT_13.5',
		'DIFF_MAX_MIN_GT_14.5',
		'DIFF_MAX_MIN_GT_15.5',
		'DIFF_MAX_MIN_GT_16.5',
		'DIFF_MAX_MIN_GT_17.5',
		'DIFF_MAX_MIN_GT_18.5',
		'SUM_GT_82.5',
		'SUM_GT_83.5',
		'SUM_GT_84.5',
		'SUM_GT_85.5',
		'SUM_GT_86.5',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'MAX_GT_17.5',
		'MAX_GT_18.5',
		'MAX_GT_19.5',
		'ODD_EQ_2',
		'ODD_EQ_3',
		'ODD_EQ_4',
		'ODD_EQ_5',
		'ODD_EQ_6',
		'EVEN_EQ_2',
		'EVEN_EQ_3',
		'EVEN_EQ_4',
		'EVEN_EQ_5',
		'EVEN_EQ_6'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);

		for ($number = 7; $number <= 11; $i++) {
			// Любой из выпавших номеров кратен 7 (0 не кратное)
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] != 0 && ($nums[$i] % $number) == 0) {
						return true;
					}
				}return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $i]);
		}

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		// Выпадут совпадающие номера в разных полях
		calculate_case(function ($nums) {
			return count(array_count_values($nums)) > 0;
		}, $numbers, $numbers_prev, $array_res['SAME_NUMBERS']);

		// Четных номеров выпадет больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		for ($i = 1; $i <= 20; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $i]);
		}

		for ($number = 37; $number <= 41; $i++) {
			// Сумма всех выпавших НЕчетных номеров Больше 37.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 != 0;
				});

				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_' . $number . '.5']);
		}

		// Сумма всех выпавших четных номеров Больше 40.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 40.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_40.5']);


		for ($number = 42; $number <= 45; $number++) {
			// Сумма всех выпавших четных номеров Больше 40.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 == 0;
				});
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_' . $number . '.5']);
		}

		for ($number = 1; $number <= 8; $number++) {
			// $number-й номер Чет
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);
		}

		for ($number = 12; $number <= 18; $number++) {
			// Разность наибольшего и наименьшего из выпавших номеров Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				return (max($nums) - min($nums)) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_' . $number . '.5']);
		}

		for ($number = 82; $number <= 86; $number++) {
			// Сумма всех выпавших номеров Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				return array_sum($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_GT_' . $number . '.5']);
		}

		for ($number = 1; $number <= 3; $number++) {
			// Наименьший выпавший номер Больше 1.5
			calculate_case(function ($nums) use ($number) {
				return min($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MIN_GT_' . $number . '.5']);
		}

		for ($number = 17; $number <= 19; $number++) {
			// Наибольший выпавший номер Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				return max($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MAX_GT_' . $number . '.5']);
		}

		for ($number = 2; $number <= 6; $number++) {
			// Количество выпавших НЕчетных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$odd = count(array_filter($nums, function ($num) {
					return $num % 2 != 0;
				}));
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);

			// Количество выпавших Четных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$even = count(array_filter($nums, function ($num) {
					return $num % 2 == 0;
				}));
				return $even == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='4_20_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_rapido_2_for_gamers', 'insert_from_table_rapido_2_for_gamers');
add_action('wp_ajax_nopriv_insert_rapido_2_for_gamers', 'insert_from_table_rapido_2_for_gamers');


function insert_from_table_rapido_2_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_rapido_2_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_rapido_2_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}

add_action('wp_ajax_get_rapido_2_for_gamers', 'get_rapido_2_for_gamers');
add_action('wp_ajax_nopriv_get_rapido_2_for_gamers', 'get_rapido_2_for_gamers');

function get_rapido_2_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_rapido_2_for_gamers`');
	$keys = array(
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'DOP_NUM_1',
		'DOP_NUM_2',
		'DOP_NUM_3',
		'DOP_NUM_4',
		'DOP_NUM_GT_2.5',
		'DOP_NUM_EVEN',
		'SUM_GT_77.5',
		'SUM_GT_81.5',
		'SUM_GT_84.5',
		'SUM_GT_88.5',
		'SUM_GT_91.5',
		'SUM_EVEN',
		'SUM_ODD_GT_35.5',
		'SUM_ODD_GT_39.5',
		'SUM_ODD_GT_43.5',
		'SUM_EVEN_GT_38.5',
		'SUM_EVEN_GT_42.5',
		'SUM_EVEN_GT_46.5',
		'SUM_1_7_GT_11.5',
		'SUM_1_10_GT_21.5',
		'SUM_8_14_GT_31.5',
		'SUM_11_20_GT_62.5',
		'SUM_15_20_GT_37.5',
		'MIN_GT_2.5',
		'MAX_GT_18.5',
		'MIN_EVEN',
		'MAX_EVEN',
		'SUM_MIN_MAX_GT_20.5',
		'SUM_MIN_MAX_EVEN',
		'DIFF_MAX_MIN_GT_16.5',
		'DIFF_MAX_MIN_EVEN',
		'NUM_1_GT_10.5',
		'NUM_2_GT_10.5',
		'NUM_3_GT_10.5',
		'NUM_4_GT_10.5',
		'NUM_5_GT_10.5',
		'NUM_6_GT_10.5',
		'NUM_7_GT_10.5',
		'NUM_8_GT_10.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'NUM_8_EVEN',
		'ANY_DIV_10',
		'COUNT_EVEN_GT_COUNT_ODD',
		'SUM_EVEN_GT_SUM_ODD',
		'ODD_LT_4',
		'ODD_EQ_4',
		'ODD_GT_4',
		'EVEN_LT_4',
		'EVEN_EQ_4',
		'EVEN_GT_4',
		'COUNT_1_7_LT_3',
		'COUNT_1_7_EQ_3',
		'COUNT_1_7_GT_3',
		'COUNT_1_10_LT_4',
		'COUNT_1_10_EQ_4',
		'COUNT_1_10_GT_4',
		'COUNT_8_14_LT_3',
		'COUNT_8_14_EQ_3',
		'COUNT_8_14_GT_3',
		'COUNT_11_20_LT_4',
		'COUNT_11_20_EQ_4',
		'COUNT_11_20_GT_4',
		'COUNT_15_20_LT_2',
		'COUNT_15_20_EQ_2',
		'COUNT_15_20_GT_2',
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9);

		for ($number = 1; $number <= 20; $number++) {
			// Выпадет номер 1
			calculate_case(function ($nums) use ($number) {
				return in_array($number, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number]);
		}

		for ($number = 1; $number <= 4; $number++) {
			// Бонусный (Дополнительный) номер $number
			calculate_case(function ($nums) use ($number) {
				return end($nums) == $number;
			}, $numbers, $numbers_prev, $array_res['DOP_NUM_' . $number]);
		}

		// Бонусный (Дополнительный) номер Больше 2.5
		calculate_case(function ($nums) {
			return end($nums) > 2.5;
		}, $numbers, $numbers_prev, $array_res['DOP_NUM_GT_2.5']);

		// Бонусный (Дополнительный) номер Чет
		calculate_case(function ($nums) {
			return end($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DOP_NUM_EVEN']);

		// Сумма всех выпавших номеров Больше 77.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 77.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_77.5']);

		// Сумма всех выпавших номеров Больше 81.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 81.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_81.5']);

		// Сумма всех выпавших номеров Больше 84.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 84.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_84.5']);

		// Сумма всех выпавших номеров Больше 88.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 88.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_88.5']);

		// Сумма всех выпавших номеров Больше 91.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 91.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_91.5']);

		// Сумма всех выпавших номеров Чет
		calculate_case(function ($nums) {
			return (array_sum($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN']);

		// Сумма всех выпавших НЕчетных номеров Больше 35.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 35.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_35.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 39.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 39.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_39.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 43.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 43.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_43.5']);

		// Сумма всех выпавших четных номеров Больше 38.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 38.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_38.5']);

		// Сумма всех выпавших четных номеров Больше 42.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 42.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_42.5']);

		// Сумма всех выпавших четных номеров Больше 46.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 46.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_46.5']);

		// Сумма всех выпавших номеров от 1 до 7 Больше 11.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 7;
			});
			return array_sum($filtered_list) > 11.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_7_GT_11.5']);

		// Сумма всех выпавших номеров от 1 до 10 Больше 21.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			});
			return array_sum($filtered_list) > 21.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_10_GT_21.5']);

		// Сумма всех выпавших номеров от 8 до 14 Больше 31.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 8 && $num <= 14;
			});
			return array_sum($filtered_list) > 31.5;
		}, $numbers, $numbers_prev, $array_res['SUM_8_14_GT_31.5']);

		// Сумма всех выпавших номеров от 11 до 20 Больше 62.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			});
			return array_sum($filtered_list) > 62.5;
		}, $numbers, $numbers_prev, $array_res['SUM_11_20_GT_62.5']);

		// Сумма всех выпавших номеров от 15 до 20 Больше 37.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 15 && $num <= 20;
			});
			return array_sum($filtered_list) > 37.5;
		}, $numbers, $numbers_prev, $array_res['SUM_15_20_GT_37.5']);

		// Наименьший выпавший номер Больше 2.5
		calculate_case(function ($nums) {
			return min($nums) > 2.5;
		}, $numbers, $numbers_prev, $array_res['MIN_GT_2.5']);

		// Наибольший выпавший номер Больше 18.5
		calculate_case(function ($nums) {
			return max($nums) > 18.5;
		}, $numbers, $numbers_prev, $array_res['MAX_GT_18.5']);

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Больше 20.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 20.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_20.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Разность наибольшего и наименьшего из выпавших номеров Больше 16.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 16.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_16.5']);

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		for ($number = 1; $number <= 8; $number++) {
			// $number-й номер больше 10.5
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] > 10.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_GT_10.5']);

			// $number-й номер Чет
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);
		}

		// Любой из выпавших номеров кратен 10
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 10 == 0) {
					return true;
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ANY_DIV_10']);

		// Четных номеров выпадет больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		// Сумма выпавших Четных номеров Больше, чем сумма выпавших НЕчетных номеров
		calculate_case(function ($nums) {
			$even = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			$odd = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_SUM_ODD']);

		// Количество выпавших НЕчетных номеров Меньше 4
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd < 4;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_4']);

		// Количество выпавших НЕчетных номеров Ровно 4
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd == 4;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_4']);

		// Количество выпавших НЕчетных номеров Больше 4
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd > 4;
		}, $numbers, $numbers_prev, $array_res['ODD_GT_4']);

		// Количество выпавших Четных номеров Меньше 4
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even < 4;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_4']);

		// Количество выпавших Четных номеров Ровно 4
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even == 4;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_4']);

		// Количество выпавших Четных номеров Больше 4
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even > 4;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_4']);

		// Количество выпавших номеров от 1 до 7 включительно Меньше 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 7;
			}));
			return $filtered < 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_7_LT_3']);

		// Количество выпавших номеров от 1 до 7 включительно Ровно 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 7;
			}));
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_7_EQ_3']);

		// Количество выпавших номеров от 1 до 7 включительно Больше 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 7;
			}));
			return $filtered > 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_7_GT_3']);

		// Количество выпавших номеров от 1 до 10 включительно Меньше 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			}));
			return $filtered < 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_LT_4']);

		// Количество выпавших номеров от 1 до 10 включительно Ровно 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			}));
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_EQ_4']);

		// Количество выпавших номеров от 1 до 10 включительно Больше 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			}));
			return $filtered > 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_GT_4']);

		// Количество выпавших номеров от 8 до 14 включительно Меньше 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 8 && $num <= 14;
			}));
			return $filtered < 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_8_14_LT_3']);

		// Количество выпавших номеров от 8 до 14 включительно Ровно 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 8 && $num <= 14;
			}));
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_8_14_EQ_3']);

		// Количество выпавших номеров от 8 до 14 включительно Больше 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 8 && $num <= 14;
			}));
			return $filtered > 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_8_14_GT_3']);

		// Количество выпавших номеров от 11 до 20 включительно Меньше 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			}));
			return $filtered < 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_LT_4']);

		// Количество выпавших номеров от 11 до 20 включительно Ровно 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			}));
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_EQ_4']);

		// Количество выпавших номеров от 11 до 20 включительно Больше 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			}));
			return $filtered > 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_GT_4']);

		// Количество выпавших номеров от 15 до 20 включительно Меньше 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 15 && $num <= 20;
			}));
			return $filtered < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_15_20_LT_2']);

		// Количество выпавших номеров от 15 до 20 включительно Ровно 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 15 && $num <= 20;
			}));
			return $filtered == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_15_20_EQ_2']);

		// Количество выпавших номеров от 15 до 20 включительно Больше 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num >= 15 && $num <= 20;
			}));
			return $filtered > 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_15_20_GT_2']);


		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='rapido_2_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}
add_action('wp_ajax_insert_24_12_for_gamers', 'insert_from_table_24_12_for_gamers');
add_action('wp_ajax_nopriv_insert_24_12_for_gamers', 'insert_from_table_24_12_for_gamers');

function insert_from_table_24_12_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_24_12_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_24_12_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9,NUMBER10,NUMBER11,NUMBER12) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ',' . $item->numbers[9] . ',' . $item->numbers[10] . ',' . $item->numbers[11] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}

add_action('wp_ajax_get_24_12_for_gamers', 'get_24_12_for_gamers');
add_action('wp_ajax_nopriv_get_24_12_for_gamers', 'get_24_12_for_gamers');

function get_24_12_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_24_12_for_gamers`');
	$keys = array(
		'MIN_GT_2.5',
		'MIN_EVEN',
		'MAX_GT_22.5',
		'MAX_EVEN',
		'DIFF_MAX_MIN_GT_21.5',
		'DIFF_MAX_MIN_EVEN',
		'SUM_MAX_MIN_GT_25.5',
		'SUM_MAX_MIN_EVEN',
		'SUM_GT_140.5',
		'SUM_GT_145.5',
		'SUM_GT_150.5',
		'SUM_GT_155.5',
		'SUM_GT_160.5',
		'SUM_EVEN',
		'COUNT_ODD_GT_67.5',
		'COUNT_ODD_GT_71.5',
		'COUNT_ODD_GT_75.5',
		'SUM_EVEN_GT_68.5',
		'SUM_EVEN_GT_72.5',
		'SUM_EVEN_GT_76.5',
		'SUM_1_8_GT_18.5',
		'SUM_1_12_GT_39.5',
		'SUM_9_16_GT_50.5',
		'SUM_13_24_GT_111.5',
		'SUM_17_24_GT_82.5',
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'NUM_21',
		'NUM_22',
		'NUM_23',
		'NUM_24',
		'NUM_1_GT_12.5',
		'NUM_2_GT_12.5',
		'NUM_3_GT_12.5',
		'NUM_4_GT_12.5',
		'NUM_5_GT_12.5',
		'NUM_6_GT_12.5',
		'NUM_7_GT_12.5',
		'NUM_8_GT_12.5',
		'NUM_9_GT_12.5',
		'NUM_10_GT_12.5',
		'NUM_11_GT_12.5',
		'NUM_12_GT_12.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'NUM_8_EVEN',
		'NUM_9_EVEN',
		'NUM_10_EVEN',
		'NUM_11_EVEN',
		'NUM_12_EVEN',
		'ANY_DIV_10',
		'FIRST_GT_LAST',
		'COUNT_EVEN_GT_COUNT_ODD',
		'SUM_EVEN_GT_SUM_ODD',
		'ODD_LT_6',
		'ODD_EQ_6',
		'ODD_GT_6',
		'EVEN_LT_6',
		'EVEN_EQ_6',
		'EVEN_GT_6',
		'COUNT_1_8_LT_4',
		'COUNT_1_8_EQ_4',
		'COUNT_1_8_GT_4',
		'COUNT_1_12_LT_6',
		'COUNT_1_12_EQ_6',
		'COUNT_1_12_GT_6',
		'COUNT_9_16_LT_4',
		'COUNT_9_16_EQ_4',
		'COUNT_9_16_GT_4',
		'COUNT_13_24_LT_6',
		'COUNT_13_24_EQ_6',
		'COUNT_13_24_GT_6',
		'COUNT_17_24_LT_4',
		'COUNT_17_24_EQ_4',
		'COUNT_17_24_GT_4'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9, $row->NUMBER10, $row->NUMBER11, $row->NUMBER12);

		// Наименьший выпавший номер Больше 2.5
		calculate_case(function ($nums) {
			return min($nums) > 2.5;
		}, $numbers, $numbers_prev, $array_res['MIN_GT_2.5']);

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Больше 22.5
		calculate_case(function ($nums) {
			return max($nums) > 22.5;
		}, $numbers, $numbers_prev, $array_res['MAX_GT_22.5']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Разность наибольшего и наименьшего номеров Больше 21.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 21.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_21.5']);

		// Разность наибольшего и наименьшего номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		// Сумма наименьшего и наибольшего номеров Больше 25.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 25.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MAX_MIN_GT_25.5']);

		// Сумма наименьшего и наибольшего номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MAX_MIN_EVEN']);

		// Сумма всех выпавших номеров Больше 140.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 140.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_140.5']);

		// Сумма всех выпавших номеров Больше 145.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 145.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_145.5']);

		// Сумма всех выпавших номеров Больше 150.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 150.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_150.5']);

		// Сумма всех выпавших номеров Больше 155.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 155.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_155.5']);

		// Сумма всех выпавших номеров Больше 160.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 160.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_160.5']);

		// Сумма всех выпавших номеров Чет
		calculate_case(function ($nums) {
			return (array_sum($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN']);

		// Сумма всех выпавших Нечетных номеров Больше 67.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return count($filtered) > 67.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_GT_67.5']);

		// Сумма всех выпавших Нечетных номеров Больше 71.5
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num % 2 != 0;
				}
			);
			return count($filtered) > 71.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_GT_71.5']);

		// Сумма всех выпавших Нечетных номеров Больше 75.5
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num % 2 != 0;
				}
			);
			return count($filtered) > 75.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_GT_75.5']);

		// Сумма всех выпавших Четных номеров Больше 68.5
		calculate_case(function ($nums) {
			$filtered = array_sum(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);

			return $filtered > 68.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_68.5']);

		// Сумма всех выпавших Четных номеров Больше 72.5
		calculate_case(function ($nums) {
			$filtered = array_sum(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);

			return $filtered > 72.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_72.5']);

		// Сумма всех выпавших Четных номеров Больше 76.5
		calculate_case(function ($nums) {
			$filtered = array_sum(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);

			return $filtered > 76.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_76.5']);

		// Сумма всех выпавших номеров от 1 до 8 Больше 18.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 1 && $num <= 8;
				}
			);
			return array_sum($filtered_list) > 18.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_8_GT_18.5']);

		// Сумма всех выпавших номеров от 1 до 12 Больше 39.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 1 && $num <= 12;
				}
			);
			return array_sum($filtered_list) > 39.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_12_GT_39.5']);

		// Сумма всех выпавших номеров от 9 до 16 Больше 50.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 9 && $num <= 16;
				}
			);
			return array_sum($filtered_list) > 50.5;
		}, $numbers, $numbers_prev, $array_res['SUM_9_16_GT_50.5']);

		// Сумма всех выпавших номеров от 13 до 24 Больше 111.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 13 && $num <= 24;
				}
			);
			return array_sum($filtered_list) > 111.5;
		}, $numbers, $numbers_prev, $array_res['SUM_13_24_GT_111.5']);

		// Сумма всех выпавших номеров от 17 до 24 Больше 82.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter(
				$nums,
				function ($num) {
					return $num >= 17 && $num <= 24;
				}
			);
			return array_sum($filtered_list) > 82.5;
		}, $numbers, $numbers_prev, $array_res['SUM_17_24_GT_82.5']);

		for ($number = 1; $number <= 24; $number++) {
			// Выпадет номер $number
			calculate_case(function ($nums) use ($number) {
				return in_array($number, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number]);
		}

		for ($number = 1; $number <= 12; $number++) {
			// $number - й номер больше 12.5
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] > 12.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_GT_12.5']);
		}

		for ($number = 1; $number <= 12; $number++) {
			// $number - й номер Чет
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);
		}

		// Любой из выпавших номеров кратен 10
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 10 == 0) {
					return true;
				}
			}
			return false;
		}, $numbers, $numbers_prev, $array_res['ANY_DIV_10']);

		// Первый больше последнего
		calculate_case(function ($nums) {
			return $nums[0] > end($nums);
		}, $numbers, $numbers_prev, $array_res['FIRST_GT_LAST']);


		// Четных больше, чем нечетных
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		// Сумма выпавших четных номеров больше, чем сумма выпавших НЕчетных номеров
		calculate_case(function ($nums) {
			$even = array_filter(
				$nums,
				function ($num) {
					return $num % 2 == 0;
				}
			);
			$odd = array_filter(
				$nums,
				function ($num) {
					return $num % 2 != 0;
				}
			);
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_SUM_ODD']);

		// Количество выпавших НЕчетных номеров Меньше 6
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd < 6;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_6']);

		// Количество выпавших НЕчетных номеров Ровно 6
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd == 6;
		}, $numbers, $numbers_prev, $array_res['ODD_EQ_6']);

		// Количество выпавших НЕчетных номеров Больше 6
		calculate_case(function ($nums) {
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $odd > 6;
		}, $numbers, $numbers_prev, $array_res['ODD_GT_6']);

		// Количество выпавших Четных номеров Меньше 6
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even < 6;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_6']);

		// Количество выпавших Четных номеров Ровно 6
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even == 6;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_6']);

		// Количество выпавших Четных номеров Больше 6
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			return $even > 6;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_6']);

		// Количество выпавших номеров от 1 до 8 включительно Меньше 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 8;
					}
				)
			);
			return $filtered < 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_8_LT_4']);

		// Количество выпавших номеров от 1 до 8 включительно Ровно 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 8;
					}
				)
			);
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_8_EQ_4']);

		// Количество выпавших номеров от 1 до 8 включительно Больше 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 8;
					}
				)
			);
			return $filtered > 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_8_GT_4']);

		// Количество выпавших номеров от 1 до 12 включительно Меньше 6
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 12;
					}
				)
			);
			return $filtered < 6;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_12_LT_6']);

		// Количество выпавших номеров от 1 до 12 включительно Ровно 6
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 12;
					}
				)
			);
			return $filtered == 6;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_12_EQ_6']);

		// Количество выпавших номеров от 1 до 12 включительно Больше 6
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 1 && $num <= 12;
					}
				)
			);
			return $filtered > 6;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_12_GT_6']);

		// Количество выпавших номеров от 9 до 16 включительно Меньше 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 9 && $num <= 16;
					}
				)
			);
			return $filtered < 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_9_16_LT_4']);

		// Количество выпавших номеров от 9 до 16 включительно Ровно 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 9 && $num <= 16;
					}
				)
			);
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_9_16_EQ_4']);

		// Количество выпавших номеров от 9 до 16 включительно Больше 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 9 && $num <= 16;
					}
				)
			);
			return $filtered > 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_9_16_GT_4']);

		// Количество выпавших номеров от 13 до 24 включительно Меньше 6
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 13 && $num <= 24;
					}
				)
			);
			return $filtered < 6;
		}, $numbers, $numbers_prev, $array_res['COUNT_13_24_LT_6']);

		// Количество выпавших номеров от 13 до 24 включительно Ровно 6
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 13 && $num <= 24;
					}
				)
			);
			return $filtered == 6;
		}, $numbers, $numbers_prev, $array_res['COUNT_13_24_EQ_6']);

		// Количество выпавших номеров от 13 до 24 включительно Больше 6
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 13 && $num <= 24;
					}
				)
			);
			return $filtered > 6;
		}, $numbers, $numbers_prev, $array_res['COUNT_13_24_GT_6']);

		// Количество выпавших номеров от 17 до 24 включительно Меньше 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 17 && $num <= 24;
					}
				)
			);
			return $filtered < 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_17_24_LT_4']);

		// Количество выпавших номеров от 17 до 24 включительно Ровно 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 17 && $num <= 24;
					}
				)
			);
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_17_24_EQ_4']);

		// Количество выпавших номеров от 17 до 24 включительно Больше 4
		calculate_case(function ($nums) {
			$filtered = count(
				array_filter(
					$nums,
					function ($num) {
						return $num >= 17 && $num <= 24;
					}
				)
			);
			return $filtered > 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_17_24_GT_4']);


		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='24_12_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_duel_for_gamers', 'insert_from_table_duel_for_gamers');
add_action('wp_ajax_nopriv_insert_duel_for_gamers', 'insert_from_table_duel_for_gamers');

function insert_from_table_duel_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_duel_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_duel_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}

add_action('wp_ajax_get_duel_for_gamers', 'get_duel_for_gamers');
add_action('wp_ajax_nopriv_get_duel_for_gamers', 'get_duel_for_gamers');

function get_duel_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_duel_for_gamers`');
	$keys = array(
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'NUM_21',
		'NUM_22',
		'NUM_23',
		'NUM_24',
		'NUM_25',
		'NUM_26',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'MIN_GT_4.5',
		'MIN_GT_5.5',
		'MIN_GT_6.5',
		'MAX_GT_20.5',
		'MAX_GT_21.5',
		'MAX_GT_22.5',
		'MAX_GT_23.5',
		'MAX_GT_24.5',
		'DIFF_MAX_MIN_GT_9.5',
		'DIFF_MAX_MIN_GT_10.5',
		'DIFF_MAX_MIN_GT_11.5',
		'DIFF_MAX_MIN_GT_12.5',
		'DIFF_MAX_MIN_GT_13.5',
		'DIFF_MAX_MIN_GT_14.5',
		'DIFF_MAX_MIN_GT_15.5',
		'DIFF_MAX_MIN_GT_16.5',
		'DIFF_MAX_MIN_GT_17.5',
		'DIFF_MAX_MIN_GT_18.5',
		'DIFF_MAX_MIN_GT_19.5',
		'DIFF_MAX_MIN_GT_20.5',
		'DIFF_MAX_MIN_GT_21.5',
		'DIFF_MAX_MIN_EVEN',
		'SUM_MAX_MIN_EVEN',
		'SUM_MAX_MIN_GT_26.5',
		'SUM_GT_45.5',
		'SUM_GT_50.5',
		'SUM_GT_52.5',
		'SUM_GT_53.5',
		'SUM_GT_54.5',
		'SUM_GT_55.5',
		'SUM_GT_56.5',
		'SUM_GT_60.5',
		'SUM_GT_65.5',
		'SUM_EVEN',
		'SUM_EVEN_GT_22.5',
		'SUM_EVEN_GT_24.5',
		'SUM_EVEN_GT_25.5',
		'SUM_EVEN_GT_26.5',
		'SUM_EVEN_GT_27.5',
		'SUM_EVEN_GT_28.5',
		'SUM_EVEN_GT_32.5',
		'SUM_ODD_GT_21.5',
		'SUM_ODD_GT_22.5',
		'SUM_ODD_GT_23.5',
		'SUM_ODD_GT_24.5',
		'SUM_ODD_GT_25.5',
		'SUM_ODD_GT_26.5',
		'SUM_ODD_GT_29.5',
		'SUM_1_9_GT_6.5',
		'SUM_1_13_GT_13.5',
		'SUM_10_18_GT_16.5',
		'SUM_14_26_GT_40.5',
		'SUM_19_26_GT_23.5',
		'NUM_DIV_5',
		'MIN_EVEN',
		'MAX_EVEN',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_1_GT_13.5',
		'NUM_2_GT_13.5',
		'NUM_3_GT_13.5',
		'NUM_4_GT_13.5',
		'FIRST_GT_LAST',
		'ADJACENT_NUMBERS',
		'SAME_NUMBERS',
		'COUNT_EVEN_GT_COUNT_ODD',
		'NUM_DIV_4',
		'NUM_DIV_5',
		'NUM_DIV_6',
		'NUM_DIV_7',
		'NUM_DIV_8',
		'COUNT_EVEN_LT_2',
		'COUNT_ODD_EQ_2',
		'COUNT_ODD_GT_2',
		'COUNT_EVEN_LT_2',
		'COUNT_EVEN_EQ_2',
		'COUNT_EVEN_GT_2',
		'COUNT_1_9_LT_1',
		'COUNT_1_9_EQ_1',
		'COUNT_1_9_GT_1',
		'COUNT_1_13_LT_2',
		'COUNT_1_13_EQ_2',
		'COUNT_1_13_GT_2',
		'COUNT_10_18_LT_1',
		'COUNT_10_18_EQ_1',
		'COUNT_10_18_GT_1',
		'COUNT_14_26_LT_2',
		'COUNT_14_26_EQ_2',
		'COUNT_14_26_GT_2',
		'COUNT_19_26_LT_1',
		'COUNT_19_26_EQ_1',
		'COUNT_19_26_GT_1',
		'COUNT_ODD_EQ_0',
		'COUNT_ODD_EQ_1',
		'COUNT_ODD_EQ_2',
		'COUNT_ODD_EQ_3',
		'COUNT_ODD_EQ_4',
		'COUNT_EVEN_EQ_0',
		'COUNT_EVEN_EQ_1',
		'COUNT_EVEN_EQ_3',
		'COUNT_EVEN_EQ_4'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4);

		for ($i = 1; $i <= 26; $i++) {
			//Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res["NUM_" . $i]);
		}

		for ($number = 2; $number <= 6; $number++) {
			// Наименьший выпавший номер Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				return min($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MIN_GT_' . $number . '.5']);
		}

		for ($number = 20; $number <= 24; $number++) {
			// Наибольший выпавший номер Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				return max($nums) > $number + 0.5;
			}, $numbers, $numbers_prev, $array_res['MAX_GT_' . $number . '.5']);
		}

		for ($number = 9; $number <= 21; $number++) {
			// Разность наибольшего и наименьшего номеров Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				return (max($nums) - min($nums)) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_' . $number . '.5']);
		}


		// Разность наибольшего и наименьшего номеров ЧЕТ
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		// Сумма наибольшего и наименьшего номеров ЧЕТ
		calculate_case(function ($nums) {
			return (min($nums) + max($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MAX_MIN_EVEN']);

		// Сумма наибольшего и наименьшего номеров Больше 26.5
		calculate_case(function ($nums) {
			return (min($nums) + max($nums)) > 26.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MAX_MIN_GT_26.5']);

		// Сумма всех выпавших номеров Больше 45.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 45.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_45.5']);

		// Сумма всех выпавших номеров Больше 50.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 50.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_50.5']);

		// Сумма всех выпавших номеров Больше 52.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 52.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_52.5']);

		// Сумма всех выпавших номеров Больше 53.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 53.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_53.5']);

		// Сумма всех выпавших номеров Больше 54.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 54.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_54.5']);

		// Сумма всех выпавших номеров Больше 55.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 55.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_55.5']);

		// Сумма всех выпавших номеров Больше 56.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 56.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_56.5']);

		// Сумма всех выпавших номеров Больше 60.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 60.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_60.5']);

		// Сумма всех выпавших номеров Больше 65.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 65.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_65.5']);

		// Сумма всех выпавших номеров ЧЕТ
		calculate_case(function ($nums) {
			return array_sum($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN']);

		// Сумма всех выпавших Четных номеров Больше 22.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 22.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_22.5']);

		// Сумма всех выпавших Четных номеров Больше 24.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 24.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_24.5']);

		// Сумма всех выпавших Четных номеров Больше 25.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 25.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_25.5']);

		// Сумма всех выпавших Четных номеров Больше 26.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 26.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_26.5']);

		// Сумма всех выпавших Четных номеров Больше 27.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 27.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_27.5']);

		// Сумма всех выпавших Четных номеров Больше 28.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 28.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_28.5']);

		// Сумма всех выпавших Четных номеров Больше 32.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 32.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_32.5']);

		// Сумма всех выпавших НЕчетных номеров Больше 21.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 21.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_21.5']);

		for ($number = 22; $number <= 26; $number++) {
			// Сумма всех выпавших НЕчетных номеров Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 != 0;
				});
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_' . $number . '.5']);
		}

		// Сумма всех выпавших НЕчетных номеров Больше 29.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 29.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_29.5']);


		// Сумма всех выпавших номеров от 1 до 9 Больше 6.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 9;
			});
			return array_sum($filtered_list) > 6.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_9_GT_6.5']);

		// Сумма всех выпавших номеров от 1 до 13 Больше 13.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 13;
			});
			return array_sum($filtered_list) > 13.5;
		}, $numbers, $numbers_prev, $array_res['SUM_1_13_GT_13.5']);

		// Сумма всех выпавших номеров от 10 до 18  Больше 16.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 10 && $num <= 18;
			});
			return array_sum($filtered) > 16.5;
		}, $numbers, $numbers_prev, $array_res['SUM_10_18_GT_16.5']);

		// Сумма всех выпавших номеров от 14 до 26 Больше 40.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 14 && $num <= 26;
			});
			return array_sum($filtered_list) > 40.5;
		}, $numbers, $numbers_prev, $array_res['SUM_14_26_GT_40.5']);

		// Сумма всех выпавших номеров от 19 до 26 Больше 23.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 19 && $num <= 26;
			});
			return array_sum($filtered_list) > 23.5;
		}, $numbers, $numbers_prev, $array_res['SUM_19_26_GT_23.5']);

		// Выпадет ли номер кратный 5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 5 == 0;
			});
			return count($filtered) > 0;
		}, $numbers, $numbers_prev, $array_res['NUM_DIV_5']);

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		for ($number = 1; $number <= 4; $number++) {
			// $number-й номер ЧЕТ
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);

			// $number-й номер Больше 13.5
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] > 13.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_GT_13.5']);
		}

		// Выпадут соседние номера
		calculate_case(function ($nums) {
			for ($i = 0; $i < count($nums); $i++) {
				if ($i > 0) {
					if (($nums[$i] == $nums[$i - 1] - 1 || $nums[$i] == $nums[$i + 1] - 1) && ($nums[$i] == $nums[$i - 1] + 1 || $nums[$i + 1] + 1)) {
						return true;
					}
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ADJACENT_NUMBERS']);

		// Первый номер больше последнего
		calculate_case(function ($nums) {
			return $nums[0] > end($nums);
		}, $numbers, $numbers_prev, $array_res['FIRST_GT_LAST']);


		// Выпадут совпадающие номера в разных полях
		calculate_case(function ($nums) {
			$half_one = array($nums[0], $nums[1]);
			$half_two = array($nums[2], $nums[3]);

			if (in_array($nums[0], $half_two) || in_array($nums[1], $half_two) || in_array($nums[2], $half_one) || in_array($nums[3], $half_one)) {
				return true;
			}
			return false;
		}, $numbers, $numbers_prev, $array_res['SAME_NUMBERS']);


		// Четных номеров больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		for ($number = 4; $number <= 8; $number++) {
			// Любой из выпавших номеров кратен $number (0-не кратное)
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] != 0 && $nums[$i] % $number == 0) {
						return true;
					}
				}

				return false;
			}, $numbers, $numbers_prev, $array_res['NUM_DIV_' . $number]);
		}

		// Количество выпавших Нечетных номеров меньше 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_LT_2']);

		// Количество выпавших Нечетных номеров ровно 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_LT_2']);

		// Количество выпавших Нечетных номеров больше 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered > 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_GT_2']);


		// Количество выпавших Четных номеров меньше 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_LT_2']);

		// Количество выпавших Четных номеров ровно 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_EQ_2']);

		// Количество выпавших Четных номеров больше 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered > 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_2']);

		// Количество выпавших номеров от 1 до 9 меньше 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 9;
			}));
			return $count < 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_9_LT_1']);

		// Количество выпавших номеров от 1 до 9 ровно 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 9;
			}));
			return $count == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_9_EQ_1']);

		// Количество выпавших номеров от 1 до 9 больше 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 9;
			}));
			return $count > 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_9_EQ_1']);

		// Количество выпавших номеров от 1 до 13 меньше 2
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 13;
			}));
			return $count < 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_13_LT_2']);

		// Количество выпавших номеров от 1 до 13 ровно 2
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 13;
			}));
			return $count == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_13_EQ_2']);

		// Количество выпавших номеров от 1 до 13 больше 2
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 13;
			}));
			return $count > 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_13_EQ_2']);

		// Количество выпавших номеров от 10 до 18 меньше 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 10 && $num <= 18;
			}));
			return $count < 10;
		}, $numbers, $numbers_prev, $array_res['COUNT_10_18_LT_1']);

		// Количество выпавших номеров от 10 до 18 ровно 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 10 && $num <= 18;
			}));
			return $count == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_10_18_EQ_1']);

		// Количество выпавших номеров от 10 до 18 больше 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 10 && $num <= 18;
			}));
			return $count > 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_10_18_GT_1']);

		// Количество выпавших номеров от 14 до 26 меньше 2
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 14 && $num <= 26;
			}));
			return $count < 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_14_26_LT_2']);

		// Количество выпавших номеров от 14 до 26 ровно 2
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 14 && $num <= 26;
			}));
			return $count == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_14_26_EQ_2']);

		// Количество выпавших номеров от 14 до 26 больше 2
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 14 && $num <= 26;
			}));
			return $count > 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_14_26_GT_2']);

		// Количество выпавших номеров от 19 до 26 меньше 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 19 && $num <= 26;
			}));
			return $count < 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_19_26_LT_1']);

		// Количество выпавших номеров от 19 до 26 ровно 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 19 && $num <= 26;
			}));
			return $count == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_19_26_EQ_1']);

		// Количество выпавших номеров от 19 до 26 больше 1
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 19 && $num <= 26;
			}));
			return $count > 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_19_26_GT_1']);

		// Количество выпавших Нечетных номеров ровно 0
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered == 0;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_EQ_0']);

		// Количество выпавших Нечетных номеров ровно 1
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_EQ_1']);

		// Количество выпавших Нечетных номеров ровно 2
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_EQ_2']);

		// Количество выпавших Нечетных номеров ровно 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_EQ_3']);

		// Количество выпавших Нечетных номеров ровно 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_EQ_4']);

		// Количество выпавших Четных номеров ровно 0
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered == 0;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_EQ_0']);

		// Количество выпавших Четных номеров ровно 1
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_EQ_1']);

		// Количество выпавших Четных номеров ровно 3
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_EQ_3']);

		// Количество выпавших Четных номеров ровно 4
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_EQ_4']);


		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='duel_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_top_3_for_gamers', 'insert_from_table_top_3_for_gamers');
add_action('wp_ajax_nopriv_insert_top_3_for_gamers', 'insert_from_table_top_3_for_gamers');

function insert_from_table_top_3_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_top_3_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_top_3_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}

add_action('wp_ajax_get_top_3_for_gamers', 'get_top_3_for_gamers');
add_action('wp_ajax_nopriv_get_top_3_for_gamers', 'get_top_3_for_gamers');

function get_top_3_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_top_3_for_gamers`');
	$keys = array(
		'MIN_GT_1.5',
		'MAX_GT_7.5',
		'DIFF_MAX_MIN_GT_4.5',
		'SUM_GT_10.5',
		'SUM_GT_12.5',
		'SUM_GT_13.5',
		'SUM_GT_14.5',
		'SUM_GT_16.5',
		'SUM_EVEN_GT_SUM_ODD',
		'SUM_0_3_GT_1.5',
		'SUM_0_4_GT_2.5',
		'SUM_0_4_GT_4.5',
		'SUM_4_6_GT_4.5',
		'SUM_4_6_GT_5.5',
		'SUM_5_9_GT_10.5',
		'SUM_5_9_GT_14.5',
		'SUM_7_9_GT_7.5',
		'SUM_7_9_GT_8.5',
		'FIRST_GT_4.5',
		'SECOND_GT_4.5',
		'THIRD_GT_4.5',
		'FIRST_EVEN',
		'SECOND_EVEN',
		'THIRD_EVEN',
		'SAME_NUMBERS',
		'FIRST_GT_LAST',
		'ADJACENT_NUMBERS',
		'EVEN_GT_ODD',
		'MIN_EVEN',
		'MAX_EVEN',
		'SUM_ODD_GT_7.5',
		'SUM_EVEN_GT_4.5',
		'DIFF_MAX_MIN_GT_5.5',
		'DIFF_MAX_MIN_EVEN',
		'SUM_MIN_MAX_GT_9.5',
		'SUM_MIN_MAX_EVEN',
		'NUM_DIV_3',
		'NUM_DIV_4',
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_0',
		'COUNT_ODD_LT_1',
		'COUNT_ODD_EQ_1',
		'COUNT_ODD_GT_1',
		'COUNT_EVEN_LT_1',
		'COUNT_EVEN_EQ_1',
		'COUNT_EVEN_GT_1',
		'COUNT_0_3_LT_1',
		'COUNT_0_3_EQ_1',
		'COUNT_0_3_GT_1',
		'COUNT_0_4_LT_1',
		'COUNT_0_4_EQ_1',
		'COUNT_0_4_GT_1',
		'COUNT_4_6_LT_1',
		'COUNT_4_6_EQ_1',
		'COUNT_4_6_GT_1',
		'COUNT_5_9_LT_1',
		'COUNT_5_9_EQ_1',
		'COUNT_5_9_GT_1',
		'COUNT_7_9_LT_1',
		'COUNT_7_9_EQ_1',
		'COUNT_7_9_GT_1'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3);

		// Наименьший выпавший номер Больше 1.5
		calculate_case(function ($nums) {
			return min($nums) > 1.5;
		}, $numbers, $numbers_prev, $array_res['MIN_GT_1.5']);

		// Наибольший выпавший номер Больше 7.5
		calculate_case(function ($nums) {
			return max($nums) > 7.5;
		}, $numbers, $numbers_prev, $array_res['MAX_GT_7.5']);

		// Разность наибольшего и наименьшего из выпавших номеров Больше 4.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 4.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_4.5']);

		// Сумма всех выпавших номеров Больше 10.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 10.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_10.5']);

		for ($number = 12; $number <= 16; $number++) {
			// Сумма всех выпавших номеров Больше 12.5
			calculate_case(function ($nums) use ($number) {
				return array_sum($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_GT_' . $number . '.5']);
		}

		// Сумма выпавших Четных номеров Больше, чем сумма выпавших НЕчетных номеров
		calculate_case(function ($nums) {
			$even = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			$odd = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_SUM_ODD']);

		// Сумма всех выпавших номеров от 0 до 3 Больше 1.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 0 && $num <= 3;
			});
			return array_sum($filtered_list) > 1.5;
		}, $numbers, $numbers_prev, $array_res['SUM_0_3_GT_1.5']);

		// Сумма всех выпавших номеров от 0 до 4 Больше 2.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 0 && $num <= 4;
			});
			return array_sum($filtered_list) > 2.5;
		}, $numbers, $numbers_prev, $array_res['SUM_0_4_GT_2.5']);

		// Сумма всех выпавших номеров от 0 до 4 Больше 4.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 0 && $num <= 4;
			});
			return array_sum($filtered_list) > 4.5;
		}, $numbers, $numbers_prev, $array_res['SUM_0_4_GT_4.5']);


		// Сумма всех выпавших номеров от 4 до 6 Больше 4.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 4 && $num <= 6;
			});
			return array_sum($filtered_list) > 4.5;
		}, $numbers, $numbers_prev, $array_res['SUM_4_6_GT_4.5']);

		// Сумма всех выпавших номеров от 4 до 6 Больше 5.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 4 && $num <= 6;
			});
			return array_sum($filtered_list) > 5.5;
		}, $numbers, $numbers_prev, $array_res['SUM_4_6_GT_5.5']);

		// Сумма всех выпавших номеров от 5 до 9 Больше 10.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 5 && $num <= 9;
			});
			return array_sum($filtered_list) > 10.5;
		}, $numbers, $numbers_prev, $array_res['SUM_5_9_GT_10.5']);

		// Сумма всех выпавших номеров от 5 до 9 Больше 14.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 5 && $num <= 9;
			});
			return array_sum($filtered_list) > 14.5;
		}, $numbers, $numbers_prev, $array_res['SUM_5_9_GT_14.5']);

		// Сумма всех выпавших номеров от 7 до 9 Больше 7.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 7 && $num <= 9;
			});
			return array_sum($filtered_list) > 7.5;
		}, $numbers, $numbers_prev, $array_res['SUM_7_9_GT_7.5']);

		// Сумма всех выпавших номеров от 7 до 9 Больше 8.5
		calculate_case(function ($nums) {
			$filtered_list = array_filter($nums, function ($num) {
				return $num >= 7 && $num <= 9;
			});
			return array_sum($filtered_list) > 8.5;
		}, $numbers, $numbers_prev, $array_res['SUM_7_9_GT_8.5']);


		// Первый выпавший номер Больше 4.5
		calculate_case(function ($nums) {
			return $nums[0] > 4.5;
		}, $numbers, $numbers_prev, $array_res['FIRST_GT_4.5']);

		// Второй выпавший номер Больше 4.5
		calculate_case(function ($nums) {
			return $nums[1] > 4.5;
		}, $numbers, $numbers_prev, $array_res['SECOND_GT_4.5']);

		// Третий выпавший номер Больше 4.5
		calculate_case(function ($nums) {
			return $nums[2] > 4.5;
		}, $numbers, $numbers_prev, $array_res['THIRD_GT_4.5']);

		// Первый выпавший номер Чет
		calculate_case(function ($nums) {
			return $nums[0] % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['FIRST_EVEN']);

		// Второй выпавший номер Чет
		calculate_case(function ($nums) {
			return $nums[1] % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SECOND_EVEN']);

		// Третий выпавший номер Чет
		calculate_case(function ($nums) {
			return $nums[2] % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['THIRD_EVEN']);

		// Выпадут ли одинаковые (совпадающие) номера
		calculate_case(function ($nums) {
			$count_nums = count($nums);
			$count_values = array_count_values($nums);
			return count(array_values($count_values)) < $count_nums;
		}, $numbers, $numbers_prev, $array_res['SAME_NUMBERS']);

		// Первый номер Больше последнего
		calculate_case(function ($nums) {
			return $nums[0] > end($nums);
		}, $numbers, $numbers_prev, $array_res['FIRST_GT_LAST']);

		// Выпадут соседние номера
		calculate_case(function ($nums) {
			for ($i = 0; $i < count($nums); $i++) {
				if ($i > 0) {
					if (($nums[$i] == $nums[$i - 1] - 1 || $nums[$i] == $nums[$i + 1] - 1) && ($nums[$i] == $nums[$i - 1] + 1 || $nums[$i + 1] + 1)) {
						return true;
					}
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ADJACENT_NUMBERS']);

		// Четных больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_ODD']);

		// Наименьший выпавший номер ЧЕТ
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер ЧЕТ
		calculate_case(function ($nums) {
			return (max($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Сумма всех выпавших НЕчетных номеров Больше 7.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return array_sum($filtered) > 7.5;
		}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_7.5']);

		// Сумма всех выпавших четных номеров Больше 4.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 4.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_4.5']);

		// Разность наибольшего и наименьшего из выпавших номеров Больше 5.5
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) > 5.5;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_5.5']);

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Больше 9.5
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) > 9.5;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_GT_9.5']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Любой из выпавших номеров кратен 3 (0-не кратное)
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 3 == 0) {
					return true;
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['NUM_DIV_3']);

		// Любой из выпавших номеров кратен 4 (0-не кратное)
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 4 == 0) {
					return true;
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['NUM_DIV_4']);

		for ($number = 1; $number <= 9; $number++) {
			// Выпадет номер 1
			calculate_case(function ($nums) use ($number) {
				return in_array($number, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number]);
		}

		// Выпадет номер 0
		calculate_case(function ($nums) {
			return in_array(0, $nums);
		}, $numbers, $numbers_prev, $array_res['NUM_0']);


		// Количество выпавших нечетных номеров Меньше 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return count($filtered) < 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_LT_1']);

		// Количество выпавших нечетных номеров Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_EQ_1']);

		// Количество выпавших нечетных номеров Больше 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 != 0;
			});
			return count($filtered) > 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_GT_1']);


		// Количество выпавших четных номеров Меньше 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return count($filtered) < 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_LT_1']);

		// Количество выпавших четных номеров Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_EQ_1']);

		// Количество выпавших четных номеров Больше 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return count($filtered) > 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_1']);

		$ranges = [[0, 3], [0, 4], [4, 6], [5, 9], [7, 9]];

		foreach ($ranges as $range) {
			$filtered = array_filter($numbers, function ($num) use ($range) {
				return $num >= $range[0] && $num <= $range[1];
			});

			$filtered_prev = array_filter($numbers_prev, function ($num) use ($range) {
				return $num >= $range[0] && $num <= $range[1];
			});

			// Количество выпавших номеров от $range[0] до $range[1] Меньше 1
			calculate_case(function ($filtered) {
				return count($filtered) < 1;
			}, $filtered, $filtered_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_LT_1']);
			// Количество выпавших номеров от $range[0] до $range[1] Ровно 1
			calculate_case(function ($filtered) {
				return count($filtered) == 1;
			}, $filtered, $filtered_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_EQ_1']);
			// Количество выпавших номеров от $range[0] до $range[1] Больше 1
			calculate_case(function ($filtered) {
				return count($filtered) > 1;
			}, $filtered, $filtered_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_GT_1']);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='top_3_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}



add_action('wp_ajax_insert_keno_for_gamers', 'insert_from_table_keno_for_gamers');
add_action('wp_ajax_nopriv_insert_keno_for_gamers', 'insert_from_table_keno_for_gamers');
function insert_from_table_keno_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_keno_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_keno_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9,NUMBER10,NUMBER11,NUMBER12,NUMBER13,NUMBER14,NUMBER15,NUMBER16,NUMBER17,NUMBER18,NUMBER19,NUMBER20,NUMBER21,NUMBER22) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ',' . $item->numbers[9] . ',' . $item->numbers[10] . ',' . $item->numbers[11] . ',' . $item->numbers[12] . ',' . $item->numbers[13] . ',' . $item->numbers[14] . ',' . $item->numbers[15] . ',' . $item->numbers[16] . ',' . $item->numbers[17] . ',' . $item->numbers[18] . ',' . $item->numbers[19] . ',' . $item->numbers[20] . ',' . $item->numbers[21] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_keno_for_gamers', 'get_keno_for_gamers');
add_action('wp_ajax_nopriv_get_keno_for_gamers', 'get_keno_for_gamers');
function get_keno_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_keno_for_gamers`');
	$keys = array(
		'ANY_DIV_11',
		'ANY_DIV_12',
		'ANY_DIV_13',
		'ANY_DIV_14',
		'ANY_DIV_15',
		'ANY_DIV_16',
		'ANY_DIV_17',
		'ANY_DIV_18',
		'ANY_DIV_19',
		'ANY_DIV_20',
		'ANY_DIV_21',
		'ANY_DIV_22',
		'ANY_DIV_23',
		'ANY_DIV_24',
		'ANY_DIV_25',
		'ANY_DIV_26',
		'ANY_DIV_27',
		'ANY_DIV_28',
		'ANY_DIV_29',
		'ANY_DIV_30',
		'ANY_DIV_31',
		'ANY_DIV_32',
		'ANY_DIV_33',
		'ANY_DIV_34',
		'ANY_DIV_35',
		'ANY_DIV_36',
		'ANY_DIV_37',
		'ANY_DIV_38',
		'ANY_DIV_39',
		'ANY_DIV_40',
		'ANY_DIV_41',
		'ANY_DIV_42',
		'ANY_DIV_43',
		'ANY_DIV_44',
		'ANY_DIV_45',
		'ANY_DIV_46',
		'ANY_DIV_47',
		'ANY_DIV_48',
		'ANY_DIV_49',
		'ANY_DIV_50',
		'ANY_DIV_51',
		'ANY_DIV_52',
		'ANY_DIV_53',
		'ANY_DIV_54',
		'ANY_DIV_55',
		'ANY_DIV_56',
		'ANY_DIV_57',
		'ANY_DIV_58',
		'ANY_DIV_59',
		'ANY_DIV_60',
		'ANY_DIV_61',
		'ANY_DIV_62',
		'ANY_DIV_63',
		'ANY_DIV_64',
		'ANY_DIV_65',
		'ANY_DIV_66',
		'ANY_DIV_67',
		'ANY_DIV_68',
		'ANY_DIV_69',
		'ANY_DIV_70',
		'ANY_DIV_71',
		'ANY_DIV_72',
		'ANY_DIV_73',
		'ANY_DIV_74',
		'ANY_DIV_75',
		'ANY_DIV_76',
		'ANY_DIV_77',
		'ANY_DIV_78',
		'ANY_DIV_79',
		'ANY_DIV_80',
		'MIN_EVEN',
		'MAX_EVEN',
		'SUM_MIN_MAX_EVEN',
		'SUM_GT_762.5',
		'SUM_GT_786.5',
		'SUM_GT_809.5',
		'SUM_GT_832.5',
		'SUM_GT_857.5',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'MIN_GT_4.5',
		'MIN_GT_5.5',
		'MIN_GT_6.5',
		'MIN_GT_7.5',
		'MAX_GT_73.5',
		'MAX_GT_74.5',
		'MAX_GT_75.5',
		'MAX_GT_76.5',
		'MAX_GT_77.5',
		'MAX_GT_78.5',
		'MAX_GT_79.5',
		'ANY_DIV_15',
		'ANY_DIV_20',
		'COUNT_1_20_GT_4.5',
		'COUNT_21_40_GT_4.5',
		'COUNT_41_60_GT_4.5',
		'COUNT_61_80_GT_4.5',
		'ODD_LT_10',
		'COUNT_ODD_EQ_10',
		'ODD_GT_10',
		'EVEN_LT_10',
		'EVEN_EQ_10',
		'EVEN_GT_10',
		'COUNT_1_10_LT_3',
		'COUNT_1_10_EQ_3',
		'COUNT_1_10_GT_3',
		'COUNT_1_40_LT_10',
		'COUNT_1_40_EQ_10',
		'COUNT_1_40_GT_10',
		'COUNT_11_20_LT_3',
		'COUNT_11_20_EQ_3',
		'COUNT_11_20_GT_3',
		'COUNT_21_30_LT_3',
		'COUNT_21_30_EQ_3',
		'COUNT_21_30_GT_3',
		'COUNT_31_40_LT_3',
		'COUNT_31_40_EQ_3',
		'COUNT_31_40_GT_3',
		'COUNT_41_50_LT_3',
		'COUNT_41_50_EQ_3',
		'COUNT_41_50_GT_3',
		'COUNT_41_80_LT_10',
		'COUNT_41_80_EQ_10',
		'COUNT_41_80_GT_10',
		'COUNT_51_60_LT_3',
		'COUNT_51_60_EQ_3',
		'COUNT_51_60_GT_3',
		'COUNT_61_70_LT_3',
		'COUNT_61_70_EQ_3',
		'COUNT_61_70_GT_3',
		'COUNT_71_80_LT_3',
		'COUNT_71_80_EQ_3',
		'COUNT_71_80_GT_3',
		'ODD_EQ_5',
		'ODD_EQ_6',
		'ODD_EQ_7',
		'ODD_EQ_8',
		'ODD_EQ_9',
		'ODD_EQ_10',
		'ODD_EQ_11',
		'ODD_EQ_12',
		'ODD_EQ_13',
		'ODD_EQ_14',
		'ODD_EQ_15',
		'EVEN_EQ_5',
		'EVEN_EQ_6',
		'EVEN_EQ_7',
		'EVEN_EQ_8',
		'EVEN_EQ_9',
		'EVEN_EQ_10',
		'EVEN_EQ_11',
		'EVEN_EQ_12',
		'EVEN_EQ_13',
		'EVEN_EQ_14',
		'EVEN_EQ_15',
		'COUNT_1_10_EQ_2',
		'COUNT_1_20_EQ_5',
		'COUNT_1_26_EQ_6',
		'COUNT_1_40_EQ_10',
		'COUNT_11_20_EQ_2',
		'COUNT_21_30_EQ_2',
		'COUNT_21_40_EQ_5',
		'COUNT_27_53_EQ_7',
		'COUNT_31_40_EQ_2',
		'COUNT_41_50_EQ_2',
		'COUNT_41_60_EQ_5',
		'COUNT_41_80_EQ_10',
		'COUNT_51_60_EQ_2',
		'COUNT_54_80_EQ_7',
		'COUNT_61_70_EQ_2',
		'COUNT_61_80_EQ_5',
		'COUNT_71_80_EQ_2'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9, $row->NUMBER10, $row->NUMBER11, $row->NUMBER12, $row->NUMBER13, $row->NUMBER14, $row->NUMBER15, $row->NUMBER16, $row->NUMBER17, $row->NUMBER18, $row->NUMBER19, $row->NUMBER20);
		for ($number = 11; $number <= 80; $number++) {
			// Любой из выпавших номеров кратен $number
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] % $number == 0) {
						return true;
					}
				}return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $number]);
		}

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Сумма всех выпавших номеров Больше 762.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 762.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_762.5']);

		// Сумма всех выпавших номеров Больше 786.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 786.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_786.5']);

		// Сумма всех выпавших номеров Больше 809.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 809.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_809.5']);

		// Сумма всех выпавших номеров Больше 832.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 832.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_832.5']);

		// Сумма всех выпавших номеров Больше 857.5
		calculate_case(function ($nums) {
			return array_sum($nums) > 857.5;
		}, $numbers, $numbers_prev, $array_res['SUM_GT_857.5']);

		for ($number = 1; $number <= 7; $number++) {
			// Наименьший выпавший номер Больше 1.5
			calculate_case(function ($nums) use ($number) {
				return min($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MIN_GT_' . $number . '.5']);
		}

		for ($number = 73; $number <= 79; $number++) {
			// Наибольший выпавший номер Больше 73.5
			calculate_case(function ($nums) use ($number) {
				return max($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MAX_GT_' . $number . '.5']);
		}

		// Любой из выпавших номеров кратен 15
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 15 == 0) {
					return true;
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ANY_DIV_15']);

		// Любой из выпавших номеров кратен 20
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 20 == 0) {
					return true;
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ANY_DIV_20']);

		// Количество выпавших номеров от 1 до 20 Больше 4.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 20;
			});
			return count($filtered) > 4.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_20_GT_4.5']);

		// Количество выпавших номеров от 21 до 40 Больше 4.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 21 && $num <= 40;
			});
			return count($filtered) > 4.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_40_GT_4.5']);

		// Количество выпавших номеров от 41 до 60 Больше 4.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 41 && $num <= 60;
			});
			return count($filtered) > 4.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_41_60_GT_4.5']);

		// Количество выпавших номеров от 61 до 80 Больше 4.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 61 && $num <= 80;
			});
			return count($filtered) > 4.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_61_80_GT_4.5']);

		// Количество выпавших НЕчетных номеров Меньше 10
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd < 10;
		}, $numbers, $numbers_prev, $array_res['ODD_LT_10']);

		// Количествово выпавших НЕчетных номеров Ровно 10
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered == 10;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_EQ_10']);

		// Количество выпавших НЕчетных номеров Больше 10
		calculate_case(function ($nums) {
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $odd > 10;
		}, $numbers, $numbers_prev, $array_res['ODD_GT_10']);


		// Количество выпавших Четных номеров Меньше 10
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even < 10;
		}, $numbers, $numbers_prev, $array_res['EVEN_LT_10']);

		// Количество выпавших Четных номеров Ровно 10
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even == 10;
		}, $numbers, $numbers_prev, $array_res['EVEN_EQ_10']);

		// Количество выпавших Четных номеров Больше 10
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $even > 10;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_10']);

		// эти числа подставятся в нужные позиции
		$ranges = [[1, 10, 3], [1, 40, 10], [11, 20, 3], [21, 30, 3], [31, 40, 3], [41, 50, 3], [41, 80, 10], [51, 60, 3], [61, 70, 3], [71, 80, 3],];

		foreach ($ranges as $range) {
			$filtered = array_filter($numbers, function ($num) use ($range) {
				return $num >= $range[0] && $num <= $range[1];
			});
			$filtered_prev = array_filter($numbers, function ($num) use ($range) {
				return $num >= $range[0] && $num <= $range[1];
			});

			// Количество выпавших номеров от $range[0] до $range[1] Меньше $range[2]
			calculate_case(function ($filtered) use ($range) {
				return count($filtered) < $range[2];
			}, $filtered, $filtered_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_LT_' . $range[2]]);

			// Количество выпавших номеров от $range[0] до $range[1] Ровно $range[2]
			calculate_case(function ($filtered) use ($range) {
				return count($filtered) == $range[2];
			}, $filtered, $filtered_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_EQ_' . $range[2]]);

			// Количество выпавших номеров от $range[0] до $range[1] Больше $range[2]
			calculate_case(function ($filtered) use ($range) {
				return count($filtered) > $range[2];
			}, $numbers, $numbers_prev, $array_res['COUNT_' . $range[0] . '_' . $range[1] . '_GT_' . $range[2]]);
		}

		for ($number = 5; $number <= 15; $number++) {
			// Количество выпавших НЕчетных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$odd = count(array_filter($nums, function ($num) {
					return $num % 2 != 0;
				}));
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);
		}

		for ($number = 5; $number <= 15; $number++) {
			// Количество выпавших Четных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$even = count(array_filter($nums, function ($num) {
					return $num % 2 == 0;
				}));
				return $even == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);
		}


		// Количество выпавших номеров от 1 до 10 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_EQ_2']);

		// Количество выпавших номеров от 1 до 20 Ровно 5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 20;
			});
			return count($filtered) == 5;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_20_EQ_5']);

		// Количество выпавших номеров от 1 до 26 Ровно 6
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 26;
			});
			return count($filtered) == 6;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_26_EQ_6']);

		// Количество выпавших номеров от 1 до 40 Ровно 10
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 40;
			});
			return count($filtered) == 10;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_40_EQ_10']);

		// Количество выпавших номеров от 11 до 20 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_EQ_2']);

		// Количество выпавших номеров от 21 до 30 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 21 && $num <= 30;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_30_EQ_2']);

		// Количество выпавших номеров от 21 до 40 Ровно 5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 21 && $num <= 40;
			});
			return count($filtered) == 5;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_40_EQ_5']);

		// Количество выпавших номеров от 27 до 53 Ровно 7
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 27 && $num <= 53;
			});
			return count($filtered) == 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_27_53_EQ_7']);

		// Количество выпавших номеров от 31 до 40 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 31 && $num <= 40;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_40_EQ_2']);

		// Количество выпавших номеров от 41 до 50 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 41 && $num <= 50;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_41_50_EQ_2']);

		// Количество выпавших номеров от 41 до 60 Ровно 5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 41 && $num <= 60;
			});
			return count($filtered) == 5;
		}, $numbers, $numbers_prev, $array_res['COUNT_41_60_EQ_5']);

		// Количество выпавших номеров от 41 до 80 Ровно 10
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 41 && $num <= 80;
			});
			return count($filtered) == 10;
		}, $numbers, $numbers_prev, $array_res['COUNT_41_80_EQ_10']);

		// Количество выпавших номеров от 51 до 60 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 51 && $num <= 60;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_51_60_EQ_2']);

		// Количество выпавших номеров от 54 до 80 Ровно 7
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 54 && $num <= 80;
			});
			return count($filtered) == 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_54_80_EQ_7']);

		// Количество выпавших номеров от 61 до 70 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 61 && $num <= 70;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_61_70_EQ_2']);

		// Количество выпавших номеров от 61 до 80 Ровно 5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 61 && $num <= 80;
			});
			return count($filtered) == 5;
		}, $numbers, $numbers_prev, $array_res['COUNT_61_80_EQ_5']);

		// Количество выпавших номеров от 71 до 80 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 71 && $num <= 80;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_71_80_EQ_2']);


		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='keno_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}




add_action('wp_ajax_insert_6_36_for_gamers', 'insert_from_table_6_36_for_gamers');
add_action('wp_ajax_nopriv_insert_6_36_for_gamers', 'insert_from_table_6_36_for_gamers');
function insert_from_table_6_36_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_6_36_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_6_36_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_6_36_for_gamers', 'get_6_36_for_gamers');
add_action('wp_ajax_nopriv_get_6_36_for_gamers', 'get_6_36_for_gamers');
function get_6_36_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_6_36_for_gamers`');
	$keys = array(
		'ANY_DIV_4',
		'ANY_DIV_5',
		'ANY_DIV_6',
		'ANY_DIV_7',
		'ANY_DIV_8',
		'ANY_DIV_9',
		'ANY_DIV_10',
		'ANY_DIV_11',
		'ANY_DIV_12',
		'ANY_DIV_13',
		'ANY_DIV_14',
		'ANY_DIV_15',
		'ANY_DIV_16',
		'ANY_DIV_17',
		'ANY_DIV_18',
		'ANY_DIV_19',
		'ANY_DIV_20',
		'ANY_DIV_21',
		'ANY_DIV_22',
		'ANY_DIV_23',
		'ANY_DIV_24',
		'ANY_DIV_25',
		'ANY_DIV_26',
		'ANY_DIV_27',
		'ANY_DIV_28',
		'ANY_DIV_29',
		'ANY_DIV_30',
		'ANY_DIV_31',
		'ANY_DIV_33',
		'ANY_DIV_34',
		'ANY_DIV_35',
		'ANY_DIV_36',
		'MIN_EVEN',
		'MAX_EVEN',
		'FIRST_GT_LAST',
		'SUM_MIN_MAX_EVEN',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_1_GT_18.5',
		'NUM_2_GT_18.5',
		'NUM_3_GT_18.5',
		'NUM_4_GT_18.5',
		'NUM_5_GT_18.5',
		'NUM_6_GT_18.5',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'MIN_GT_4.5',
		'MIN_GT_5.5',
		'MIN_GT_6.5',
		'MIN_GT_7.5',
		'MIN_GT_8.5',
		'MIN_GT_9.5',
		'MAX_GT_27.5',
		'MAX_GT_28.5',
		'MAX_GT_29.5',
		'MAX_GT_30.5',
		'MAX_GT_31.5',
		'MAX_GT_32.5',
		'MAX_GT_33.5',
		'MAX_GT_34.5',
		'MAX_GT_35.5',
		'ODD_EQ_0',
		'ODD_EQ_1',
		'ODD_EQ_2',
		'ODD_EQ_3',
		'ODD_EQ_4',
		'ODD_EQ_5',
		'ODD_EQ_6',
		'EVEN_EQ_0',
		'EVEN_EQ_1',
		'EVEN_EQ_2',
		'EVEN_EQ_3',
		'EVEN_EQ_4',
		'EVEN_EQ_5',
		'EVEN_EQ_6',
		'COUNT_1_10_EQ_2',
		'COUNT_1_12_EQ_2',
		'COUNT_1_18_EQ_3',
		'COUNT_11_20_EQ_2',
		'COUNT_13_24_EQ_2',
		'COUNT_19_36_EQ_3',
		'COUNT_21_30_EQ_2',
		'COUNT_25_36_EQ_2',
		'COUNT_31_36_EQ_1'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6);

		for ($number = 4; $number <= 36; $number++) {
			// Любой из выпавших номеров кратен 4 (0 не кратное)
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] != 0 && $nums[$i] % $number == 0) {
						return true;
					}
				}return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $number]);
		}

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Первый номер Больше последнего
		calculate_case(function ($nums) {
			return $nums[0] > end($nums);
		}, $numbers, $numbers_prev, $array_res['FIRST_GT_LAST']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		for ($number = 1; $number <= 6; $number++) {
			// $number-й номер Чет
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);
		}

		for ($number = 1; $number <= 6; $number++) {
			// $number-й номер больше 18.5
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] > 18.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_GT_18.5']);
		}

		for ($number = 1; $number <= 9; $number++) {
			// Наименьший выпавший номер Больше ($number + 0.5)
			calculate_case(function ($nums) use ($number) {
				return min($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MIN_GT_' . $number . '.5']);
		}

		for ($number = 27; $number <= 35; $number++) {
			// Наибольший выпавший номер Больше ($number + 0.5)
			calculate_case(function ($nums) use ($number) {
				return max($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MAX_GT_' . $number . '.5']);
		}

		for ($number = 0; $number <= 6; $number++) {
			// Количество выпавших НЕчетных номеров Ровно 0
			calculate_case(function ($nums) use ($number) {
				$odd = count(array_filter($nums, function ($num) {
					return $num % 2 != 0;
				}));
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);
		}


		for ($number = 0; $number <= 6; $number++) {
			// Количество выпавших НЕчетных номеров Ровно 0
			calculate_case(function ($nums) use ($number) {
				$odd = count(array_filter($nums, function ($num) {
					return $num % 2 == 0;
				}));
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);
		}

		// Количество выпавших номеров от 1 до 10 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 10;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_EQ_2']);

		// Количество выпавших номеров от 1 до 12 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 12;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_12_EQ_2']);

		// Количество выпавших номеров от 1 до 18 Ровно 3
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 18;
			});
			return count($filtered) == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_18_EQ_3']);

		// Количество выпавших номеров от 11 до 20 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 11 && $num <= 20;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_EQ_2']);

		// Количество выпавших номеров от 13 до 24 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 13 && $num <= 24;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_13_24_EQ_2']);

		// Количество выпавших номеров от 19 до 36 Ровно 3
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 19 && $num <= 36;
			});
			return count($filtered) == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_19_36_EQ_3']);

		// Количество выпавших номеров от 21 до 30 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 21 && $num <= 30;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_30_EQ_2']);

		// Количество выпавших номеров от 25 до 36 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 25 && $num <= 36;
			});
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_25_36_EQ_2']);

		// Количество выпавших номеров от 31 до 36 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num >= 31 && $num <= 36;
			});
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_36_EQ_1']);


		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='6_36_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}



add_action('wp_ajax_insert_rocketbingo_for_gamers', 'insert_from_table_rocketbingo_for_gamers');
add_action('wp_ajax_nopriv_insert_rocketbingo_for_gamers', 'insert_from_table_rocketbingo_for_gamers');
function insert_from_table_rocketbingo_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_rocketbingo_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_rocketbingo_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9,NUMBER10,NUMBER11,NUMBER12,NUMBER13,NUMBER14,NUMBER15,NUMBER16,NUMBER17,NUMBER18,NUMBER19,NUMBER20,NUMBER21,NUMBER22,NUMBER23,NUMBER24,NUMBER25,NUMBER26,NUMBER27,NUMBER28,NUMBER29,NUMBER30,NUMBER31,NUMBER32,NUMBER33,NUMBER34,NUMBER35) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ',' . $item->numbers[9] . ',' . $item->numbers[10] . ',' . $item->numbers[11] . ',' . $item->numbers[12] . ',' . $item->numbers[13] . ',' . $item->numbers[14] . ',' . $item->numbers[15] . ',' . $item->numbers[16] . ',' . $item->numbers[17] . ',' . $item->numbers[18] . ',' . $item->numbers[19] . ',' . $item->numbers[20] . ',' . $item->numbers[21] . ',' . $item->numbers[22] . ',' . $item->numbers[23] . ',' . $item->numbers[24] . ',' . $item->numbers[25] . ',' . $item->numbers[26] . ',' . $item->numbers[27] . ',' . $item->numbers[28] . ',' . $item->numbers[29] . ',' . $item->numbers[30] . ',' . $item->numbers[31] . ',' . $item->numbers[32] . ',' . $item->numbers[33] . ',' . $item->numbers[34] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_rocketbingo_for_gamers', 'get_rocketbingo_for_gamers');
add_action('wp_ajax_nopriv_get_rocketbingo_for_gamers', 'get_rocketbingo_for_gamers');
function get_rocketbingo_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_rocketbingo_for_gamers`');
	$keys = array(
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'NUM_21',
		'NUM_22',
		'NUM_23',
		'NUM_24',
		'NUM_25',
		'NUM_26',
		'NUM_27',
		'NUM_28',
		'NUM_29',
		'NUM_30',
		'NUM_31',
		'NUM_32',
		'NUM_33',
		'NUM_34',
		'NUM_35',
		'NUM_1_GT_37.5',
		'NUM_2_GT_37.5',
		'NUM_3_GT_37.5',
		'NUM_4_GT_37.5',
		'NUM_5_GT_37.5',
		'NUM_6_GT_37.5',
		'NUM_7_GT_37.5',
		'NUM_8_GT_37.5',
		'NUM_9_GT_37.5',
		'NUM_10_GT_37.5',
		'NUM_11_GT_37.5',
		'NUM_12_GT_37.5',
		'NUM_13_GT_37.5',
		'NUM_14_GT_37.5',
		'NUM_15_GT_37.5',
		'NUM_16_GT_37.5',
		'NUM_17_GT_37.5',
		'NUM_18_GT_37.5',
		'NUM_19_GT_37.5',
		'NUM_20_GT_37.5',
		'NUM_21_GT_37.5',
		'NUM_22_GT_37.5',
		'NUM_23_GT_37.5',
		'NUM_24_GT_37.5',
		'NUM_25_GT_37.5',
		'NUM_26_GT_37.5',
		'NUM_27_GT_37.5',
		'NUM_28_GT_37.5',
		'NUM_29_GT_37.5',
		'NUM_30_GT_37.5',
		'NUM_31_GT_37.5',
		'NUM_32_GT_37.5',
		'NUM_33_GT_37.5',
		'NUM_34_GT_37.5',
		'NUM_35_GT_37.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'NUM_8_EVEN',
		'NUM_9_EVEN',
		'NUM_10_EVEN',
		'NUM_11_EVEN',
		'NUM_12_EVEN',
		'NUM_13_EVEN',
		'NUM_14_EVEN',
		'NUM_15_EVEN',
		'NUM_16_EVEN',
		'NUM_17_EVEN',
		'NUM_18_EVEN',
		'NUM_19_EVEN',
		'NUM_20_EVEN',
		'NUM_21_EVEN',
		'NUM_22_EVEN',
		'NUM_23_EVEN',
		'NUM_24_EVEN',
		'NUM_25_EVEN',
		'NUM_26_EVEN',
		'NUM_27_EVEN',
		'NUM_28_EVEN',
		'NUM_29_EVEN',
		'NUM_30_EVEN',
		'NUM_31_EVEN',
		'NUM_32_EVEN',
		'NUM_33_EVEN',
		'NUM_34_EVEN',
		'NUM_35_EVEN',
		'SUM_GT_1326.5',
		'SUM_GT_1327.5',
		'SUM_GT_1328.5',
		'SUM_GT_1329.5',
		'SUM_GT_1330.5',
		'SUM_EVEN',
		'SUM_EVEN_GT_648.5',
		'SUM_EVEN_GT_650.5',
		'SUM_EVEN_GT_651.5',
		'SUM_EVEN_GT_652.5',
		'SUM_EVEN_GT_653.5',
		'COUNT_ODD_GT_672.5',
		'COUNT_ODD_GT_673.5',
		'COUNT_ODD_GT_674.5',
		'COUNT_ODD_GT_675.5',
		'COUNT_ODD_GT_676.5',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_EVEN',
		'MAX_GT_73.5',
		'MAX_GT_74.5',
		'MAX_EVEN',
		'FIRST_EVEN',
		'LAST_EVEN',
		'EVEN_GT_ODD',
		'SUM_MIN_MAX_EVEN',
		'DIFF_MAX_MIN_EVEN',
		'DIFF_MAX_MIN_GT_69.5',
		'DIFF_MAX_MIN_GT_70.5',
		'DIFF_MAX_MIN_GT_71.5',
		'DIFF_MAX_MIN_GT_72.5',
		'DIFF_MAX_MIN_GT_73.5',
		'ANY_DIV_25',
		'ANY_DIV_30',
		'FIRST_GT_LAST',
		'COUNT_1_25_GT_11.5',
		'COUNT_26_50_GT_11.5',
		'COUNT_51_75_GT_11.5',
		'COUNT_ODD_LT_18',
		'COUNT_ODD_EQ_18',
		'COUNT_ODD_GT_18',
		'COUNT_EVEN_LT_18',
		'COUNT_EVEN_EQ_18',
		'COUNT_EVEN_GT_18',
		'COUNT_1_15_LT_7',
		'COUNT_1_15_EQ_7',
		'COUNT_1_15_GT_7',
		'COUNT_1_38_LT_17',
		'COUNT_1_38_EQ_17',
		'COUNT_1_38_GT_17',
		'COUNT_16_30_LT_7',
		'COUNT_16_30_EQ_7',
		'COUNT_16_30_GT_7',
		'COUNT_31_45_LT_7',
		'COUNT_31_45_EQ_7',
		'COUNT_31_45_GT_7',
		'COUNT_39_75_LT_17',
		'COUNT_39_75_EQ_17',
		'COUNT_39_75_GT_17',
		'COUNT_46_60_LT_7',
		'COUNT_46_60_EQ_7',
		'COUNT_46_60_GT_7',
		'COUNT_61_75_LT_7',
		'COUNT_61_75_EQ_7',
		'COUNT_61_75_GT_7',
		'ODD_EQ_14',
		'ODD_EQ_15',
		'ODD_EQ_16',
		'ODD_EQ_17',
		'ODD_EQ_18',
		'ODD_EQ_19',
		'ODD_EQ_20',
		'ODD_EQ_21',
		'EVEN_EQ_14',
		'EVEN_EQ_15',
		'EVEN_EQ_16',
		'EVEN_EQ_17',
		'EVEN_EQ_18',
		'EVEN_EQ_19',
		'EVEN_EQ_20',
		'EVEN_EQ_21'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8, $row->NUMBER9, $row->NUMBER10, $row->NUMBER11, $row->NUMBER12, $row->NUMBER13, $row->NUMBER14, $row->NUMBER15, $row->NUMBER16, $row->NUMBER17, $row->NUMBER18, $row->NUMBER19, $row->NUMBER20, $row->NUMBER21, $row->NUMBER22, $row->NUMBER23, $row->NUMBER24, $row->NUMBER25, $row->NUMBER26, $row->NUMBER27, $row->NUMBER28, $row->NUMBER29, $row->NUMBER30, $row->NUMBER31, $row->NUMBER32, $row->NUMBER33, $row->NUMBER34, $row->NUMBER35);
		for ($i = 1; $i <= 35; $i++) {
			// Выпадет номер $i
			calculate_case(function ($nums) use ($i) {
				return in_array($i, $nums);
			}, $numbers, $numbers_prev, $array_res["NUM_$i"]);
		}

		for ($i = 1; $i <= 35; $i++) {
			// $i-й номер больше 37.5
			calculate_case(function ($nums) use ($i) {
				return $nums[$i - 1] > 37.5;
			}, $numbers, $numbers_prev, $array_res["NUM_" . $i . "_GT_37.5"]);
		}

		for ($i = 1; $i <= 35; $i++) {
			// $i-й номер Чет
			calculate_case(function ($nums) {
				return $nums[1 - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res["NUM_" . $i . "_EVEN"]);
		}

		for ($number = 1326; $number <= 1330; $number++) {
			// Сумма всех выпавших номеров Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				return array_sum($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_GT_' . $number . '.5']);
		}

		// Сумма всех выпавших номеров Чет

		calculate_case(function ($nums) {
			return (array_sum($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN']);

		// Сумма всех выпавших Четных номеров Больше 648.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 648.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_648.5']);
		for ($number = 650; $number <= 653; $number++) {
			// Сумма всех выпавших Четных номеров Больше 650.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 == 0;
				});
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_' . $number . '.5']);
		}

		for ($number = 672; $number <= 676; $number++) {
			// Сумма всех выпавших Нечетных номеров Больше $number+0.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 != 0;
				});
				return count($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['COUNT_ODD_GT_' . $number . '.5']);
		}

		// Наименьший выпавший номер Больше 1.5
		calculate_case(function ($nums) {
			return min($nums) > 1.5;
		}, $numbers, $numbers_prev, $array_res['MIN_GT_1.5']);

		// Наименьший выпавший номер Больше 2.5
		calculate_case(function ($nums) {
			return min($nums) > 2.5;
		}, $numbers, $numbers_prev, $array_res['MIN_GT_2.5']);

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Больше 73.5
		calculate_case(function ($nums) {
			return max($nums) > 73.5;
		}, $numbers, $numbers_prev, $array_res['MAX_GT_73.5']);

		// Наибольший выпавший номер Больше 74.5
		calculate_case(function ($nums) {
			return max($nums) > 74.5;
		}, $numbers, $numbers_prev, $array_res['MAX_GT_74.5']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Первый выпавший номер Чет
		calculate_case(function ($nums) {
			return $nums[0] % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['FIRST_EVEN']);

		// Последний выпавший номер Чет
		calculate_case(function ($nums) {
			return end($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['LAST_EVEN']);

		// Четных номеров выпадет больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['EVEN_GT_ODD']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		for ($number = 69; $number <= 73; $number++) {
			// Разность наибольшего и наименьшего из выпавших номеров Больше 69.5
			calculate_case(function ($nums) use ($number) {
				return (max($nums) - min($nums)) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_' . $number . '.5']);
		}

		// Любой из выпавших номеров кратен 25
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 25 == 0) {
					return true;
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ANY_DIV_25']);

		// Любой из выпавших номеров кратен 30
		calculate_case(function ($nums) {
			$count = count($nums);
			for ($i = 0; $i < $count; $i++) {
				if ($nums[$i] % 30 == 0) {
					return true;
				}
			}return false;
		}, $numbers, $numbers_prev, $array_res['ANY_DIV_30']);

		// Первый номер Больше последнего
		calculate_case(function ($nums) {
			return $nums[0] > end($nums);
		}, $numbers, $numbers_prev, $array_res['FIRST_GT_LAST']);

		// Количество выпавших номеров от 1 до 25 больше 11.5
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 25;
			}));
			return $count > 11.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_25_GT_11.5']);

		// Количество выпавших номеров от 26 до 50 больше 11.5
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 26 && $num <= 50;
			}));
			return $count > 11.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_26_50_GT_11.5']);

		// Количество выпавших номеров от 51 до 75 больше 11.5
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 51 && $num <= 75;
			}));
			return $count > 11.5;
		}, $numbers, $numbers_prev, $array_res['COUNT_51_75_GT_11.5']);

		// Количество выпавших Нечетных номеров меньше 18
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered < 18;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_LT_18']);

		// Количество выпавших Нечетных номеров ровно 18
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered == 18;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_EQ_18']);

		// Количество выпавших Нечетных номеров больше 18
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $filtered > 18;
		}, $numbers, $numbers_prev, $array_res['COUNT_ODD_GT_18']);

		// Количество выпавших Четных номеров меньше 18
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered < 18;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_LT_18']);

		// Количество выпавших Четных номеров ровно 18
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered == 18;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_EQ_18']);

		// Количество выпавших Четных номеров больше 18
		calculate_case(function ($nums) {
			$filtered = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			return $filtered > 18;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_18']);

		// Количество выпавших номеров от 1 до 15 меньше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 15;
			}));
			return $count < 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_15_LT_7']);

		// Количество выпавших номеров от 1 до 15 ровно 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 15;
			}));
			return $count == 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_15_EQ_7']);

		// Количество выпавших номеров от 1 до 15 больше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 15;
			}));
			return $count > 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_15_GT_7']);

		// Количество выпавших номеров от 1 до 38 меньше 17
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 38;
			}));
			return $count < 17;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_38_LT_17']);

		// Количество выпавших номеров от 1 до 38 ровно 17
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 38;
			}));
			return $count == 17;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_38_EQ_17']);

		// Количество выпавших номеров от 1 до 38 больше 17
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 1 && $num <= 38;
			}));
			return $count > 17;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_38_GT_17']);

		// Количество выпавших номеров от 16 до 30 меньше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 16 && $num <= 30;
			}));
			return $count < 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_16_30_LT_7']);

		// Количество выпавших номеров от 16 до 30 ровно 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 16 && $num <= 30;
			}));
			return $count == 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_16_30_EQ_7']);

		// Количество выпавших номеров от 16 до 30 больше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 16 && $num <= 30;
			}));
			return $count > 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_16_30_GT_7']);

		// Количество выпавших номеров от 31 до 45 меньше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 31 && $num <= 45;
			}));
			return $count < 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_45_LT_7']);

		// Количество выпавших номеров от 31 до 45 ровно 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 31 && $num <= 45;
			}));
			return $count == 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_45_EQ_7']);

		// Количество выпавших номеров от 31 до 45 больше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 31 && $num <= 45;
			}));
			return $count > 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_45_GT_7']);

		// Количество выпавших номеров от 39 до 75 меньше 17
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 39 && $num <= 75;
			}));
			return $count < 17;
		}, $numbers, $numbers_prev, $array_res['COUNT_39_75_LT_17']);

		// Количество выпавших номеров от 39 до 75 ровно 17
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 39 && $num <= 75;
			}));
			return $count == 17;
		}, $numbers, $numbers_prev, $array_res['COUNT_39_75_EQ_17']);

		// Количество выпавших номеров от 39 до 75 больше 17
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 39 && $num <= 75;
			}));
			return $count > 17;
		}, $numbers, $numbers_prev, $array_res['COUNT_39_75_GT_17']);

		// Количество выпавших номеров от 46 до 60 меньше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 46 && $num <= 60;
			}));
			return $count < 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_46_60_LT_7']);

		// Количество выпавших номеров от 46 до 60 ровно 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 46 && $num <= 60;
			}));
			return $count == 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_46_60_EQ_7']);

		// Количество выпавших номеров от 46 до 60 больше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 46 && $num <= 60;
			}));
			return $count > 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_46_60_GT_7']);

		// Количество выпавших номеров от 61 до 75 меньше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 61 && $num <= 75;
			}));
			return $count < 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_61_75_LT_7']);

		// Количество выпавших номеров от 61 до 75 ровно 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 61 && $num <= 75;
			}));
			return $count == 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_61_75_EQ_7']);

		// Количество выпавших номеров от 61 до 75 больше 7
		calculate_case(function ($nums) {
			$count = count(array_filter($nums, function ($num) {
				return $num >= 61 && $num <= 75;
			}));
			return $count > 7;
		}, $numbers, $numbers_prev, $array_res['COUNT_61_75_GT_7']);

		for ($number = 14; $number <= 21; $number++) {
			// Количество выпавших НЕчетных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$odd = count(array_filter($nums, function ($num) {
					return $num % 2 != 0;
				}));
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);
		}

		for ($number = 14; $number <= 21; $number++) {
			// Количество выпавших Четных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$even = count(array_filter($nums, function ($num) {
					return $num % 2 == 0;
				}));
				return $even == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='rocketbingo_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}


add_action('wp_ajax_insert_7_49_for_gamers', 'insert_from_table_7_49_for_gamers');
add_action('wp_ajax_nopriv_insert_7_49_for_gamers', 'insert_from_table_7_49_for_gamers');
function insert_from_table_7_49_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_7_49_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_7_49_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_7_49_for_gamers', 'get_7_49_for_gamers');
add_action('wp_ajax_nopriv_get_7_49_for_gamers', 'get_7_49_for_gamers');
function get_7_49_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_7_49_for_gamers`');
	$keys = array(
		'ANY_DIV_4',
		'ANY_DIV_5',
		'ANY_DIV_6',
		'ANY_DIV_7',
		'ANY_DIV_8',
		'ANY_DIV_9',
		'ANY_DIV_10',
		'ANY_DIV_11',
		'ANY_DIV_12',
		'ANY_DIV_13',
		'ANY_DIV_14',
		'ANY_DIV_15',
		'ANY_DIV_16',
		'ANY_DIV_17',
		'ANY_DIV_18',
		'ANY_DIV_19',
		'ANY_DIV_20',
		'ANY_DIV_21',
		'ANY_DIV_22',
		'ANY_DIV_23',
		'ANY_DIV_24',
		'ANY_DIV_25',
		'ANY_DIV_26',
		'ANY_DIV_27',
		'ANY_DIV_28',
		'ANY_DIV_29',
		'ANY_DIV_30',
		'ANY_DIV_31',
		'ANY_DIV_32',
		'ANY_DIV_33',
		'ANY_DIV_34',
		'ANY_DIV_35',
		'ANY_DIV_36',
		'ANY_DIV_37',
		'ANY_DIV_38',
		'ANY_DIV_39',
		'ANY_DIV_40',
		'ANY_DIV_41',
		'ANY_DIV_42',
		'ANY_DIV_43',
		'ANY_DIV_44',
		'ANY_DIV_45',
		'ANY_DIV_46',
		'ANY_DIV_47',
		'ANY_DIV_48',
		'ANY_DIV_49',
		'MIN_EVEN',
		'MAX_EVEN',
		'FIRST_GT_LAST',
		'SUM_MIN_MAX_EVEN',
		'NUM_1_GT_25.5',
		'NUM_2_GT_25.5',
		'NUM_3_GT_25.5',
		'NUM_4_GT_25.5',
		'NUM_5_GT_25.5',
		'NUM_6_GT_25.5',
		'NUM_7_GT_25.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'MIN_GT_4.5',
		'MIN_GT_5.5',
		'MIN_GT_6.5',
		'MIN_GT_7.5',
		'MIN_GT_8.5',
		'MIN_GT_9.5',
		'MIN_GT_10.5',
		'MIN_GT_11.5',
		'MIN_GT_12.5',
		'MAX_GT_37.5',
		'MAX_GT_38.5',
		'MAX_GT_39.5',
		'MAX_GT_40.5',
		'MAX_GT_41.5',
		'MAX_GT_42.5',
		'MAX_GT_43.5',
		'MAX_GT_44.5',
		'MAX_GT_45.5',
		'MAX_GT_46.5',
		'MAX_GT_47.5',
		'MAX_GT_48.5',
		'COUNT_1_10_EQ_1',
		'COUNT_1_17_EQ_2',
		'COUNT_1_20_EQ_3',
		'COUNT_1_24_EQ_3',
		'COUNT_11_20_EQ_1',
		'COUNT_18_33_EQ_2',
		'COUNT_21_30_EQ_1',
		'COUNT_21_49_EQ_4',
		'COUNT_25_49_EQ_4',
		'COUNT_31_40_EQ_1',
		'COUNT_34_49_EQ_2',
		'COUNT_41_49_EQ_1',
		'ODD_EQ_0',
		'ODD_EQ_1',
		'ODD_EQ_2',
		'ODD_EQ_3',
		'ODD_EQ_4',
		'ODD_EQ_5',
		'ODD_EQ_6',
		'ODD_EQ_7',
		'EVEN_EQ_0',
		'EVEN_EQ_1',
		'EVEN_EQ_2',
		'EVEN_EQ_3',
		'EVEN_EQ_4',
		'EVEN_EQ_5',
		'EVEN_EQ_6',
		'EVEN_EQ_7'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7);

		for ($number = 4; $number <= 49; $number++) {
			// Любой из выпавших номеров кратен $number (0 не кратное)
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] != 0 && ($nums[$i] % $number) == 0) {
						return true;
					}
				}
				return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $number]);
		}


		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Первый номер Больше последнего
		calculate_case(function ($nums) {
			return $nums[0] > end($nums);
		}, $numbers, $numbers_prev, $array_res['FIRST_GT_LAST']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		for ($number = 1; $number <= 7; $number++) {
			// $number-й номер больше 25.5
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] > 25.5;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_GT_25.5']);
		}

		for ($number = 1; $number <= 7; $number++) {
			// $number-й номер Чет
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);
		}

		for ($number = 1; $number <= 12; $number++) {
			// Наименьший выпавший номер Больше $number+0.5
			calculate_case(function ($nums) use ($number) {
				return min($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MIN_GT_' . $number . '.5']);
		}

		for ($number = 37; $number <= 48; $number++) {
			// Наибольший выпавший номер Больше 37.5
			calculate_case(function ($nums) use ($number) {
				return max($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MAX_GT_' . $number . '.5']);
		}

		// Количество выпавших номеров от 1 до 10 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 1 && $num <= 10;
				}
			);
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_10_EQ_1']);

		// Количество выпавших номеров от 1 до 17 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 1 && $num <= 17;
				}
			);
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_17_EQ_2']);

		// Количество выпавших номеров от 1 до 20 Ровно 3
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 1 && $num <= 20;
				}
			);
			return count($filtered) == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_20_EQ_3']);

		// Количество выпавших номеров от 1 до 24 Ровно 3
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 1 && $num <= 24;
				}
			);
			return count($filtered) == 3;
		}, $numbers, $numbers_prev, $array_res['COUNT_1_24_EQ_3']);

		// Количество выпавших номеров от 11 до 20 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 11 && $num <= 20;
				}
			);
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_11_20_EQ_1']);

		// Количество выпавших номеров от 18 до 33 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 18 && $num <= 33;
				}
			);
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_18_33_EQ_2']);

		// Количество выпавших номеров от 21 до 30 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 21 && $num <= 30;
				}
			);
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_30_EQ_1']);

		// Количество выпавших номеров от 21 до 49 Ровно 4
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 21 && $num <= 49;
				}
			);
			return count($filtered) == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_21_49_EQ_4']);

		// Количество выпавших номеров от 25 до 49 Ровно 4
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 25 && $num <= 49;
				}
			);
			return count($filtered) == 4;
		}, $numbers, $numbers_prev, $array_res['COUNT_25_49_EQ_4']);

		// Количество выпавших номеров от 31 до 40 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 31 && $num <= 40;
				}
			);
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_31_40_EQ_1']);

		// Количество выпавших номеров от 34 до 49 Ровно 2
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 34 && $num <= 49;
				}
			);
			return count($filtered) == 2;
		}, $numbers, $numbers_prev, $array_res['COUNT_34_49_EQ_2']);

		// Количество выпавших номеров от 41 до 49 Ровно 1
		calculate_case(function ($nums) {
			$filtered = array_filter(
				$nums,
				function ($num) {
					return $num >= 41 && $num <= 49;
				}
			);
			return count($filtered) == 1;
		}, $numbers, $numbers_prev, $array_res['COUNT_41_49_EQ_1']);

		for ($number = 0; $number <= 7; $number++) {
			// Количество выпавших НЕчетных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$odd = count(
					array_filter(
						$nums,
						function ($num) {
							return $num % 2 != 0;
						}
					)
				);
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);
		}

		for ($number = 0; $number <= 7; $number++) {
			// Количество выпавших Четных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$even = count(
					array_filter(
						$nums,
						function ($num) {
							return $num % 2 == 0;
						}
					)
				);
				return $even == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);

		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='7_49_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_bingo_75_for_gamers', 'insert_from_table_bingo_75_for_gamers');
add_action('wp_ajax_nopriv_insert_bingo_75_for_gamers', 'insert_from_table_bingo_75_for_gamers');
function insert_from_table_bingo_75_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_bingo_75_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_bingo_75_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}

add_action('wp_ajax_get_bingo_75_for_gamers', 'get_bingo_75_for_gamers');
add_action('wp_ajax_nopriv_get_bingo_75_for_gamers', 'get_bingo_75_for_gamers');
function get_bingo_75_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_bingo_75_for_gamers`');
	$keys = array(
		'ANY_DIV_8',
		'ANY_DIV_9',
		'ANY_DIV_10',
		'ANY_DIV_11',
		'ANY_DIV_12',
		'MIN_EVEN',
		'MAX_EVEN',
		'SUM_MIN_MAX_EVEN',
		'COUNT_EVEN_GT_COUNT_ODD',
		'DIFF_MAX_MIN_EVEN',
		'DIFF_MAX_MIN_GT_67.5',
		'DIFF_MAX_MIN_GT_68.5',
		'DIFF_MAX_MIN_GT_69.5',
		'DIFF_MAX_MIN_GT_70.5',
		'DIFF_MAX_MIN_GT_71.5',
		'DIFF_MAX_MIN_GT_72.5',
		'DIFF_MAX_MIN_GT_73.5',
		'SUM_GT_1062.5',
		'SUM_GT_1063.5',
		'SUM_GT_1064.5',
		'SUM_GT_1065.5',
		'SUM_GT_1066.5',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'SUM_EVEN_GT_522.5',
		'SUM_EVEN_GT_523.5',
		'SUM_EVEN_GT_524.5',
		'SUM_EVEN_GT_525.5',
		'SUM_EVEN_GT_526.5',
		'SUM_ODD_GT_536.5',
		'SUM_ODD_GT_537.5',
		'SUM_ODD_GT_538.5',
		'SUM_ODD_GT_539.5',
		'SUM_ODD_GT_540.5',
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'NUM_21',
		'NUM_22',
		'NUM_23',
		'NUM_24',
		'NUM_25',
		'NUM_26',
		'NUM_27',
		'NUM_28',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'NUM_8_EVEN',
		'NUM_9_EVEN',
		'NUM_10_EVEN',
		'NUM_11_EVEN',
		'NUM_12_EVEN',
		'NUM_13_EVEN',
		'NUM_14_EVEN',
		'NUM_15_EVEN',
		'NUM_16_EVEN',
		'NUM_17_EVEN',
		'NUM_18_EVEN',
		'NUM_19_EVEN',
		'NUM_20_EVEN',
		'NUM_21_EVEN',
		'NUM_22_EVEN',
		'NUM_23_EVEN',
		'NUM_24_EVEN',
		'NUM_25_EVEN',
		'NUM_26_EVEN',
		'NUM_27_EVEN',
		'NUM_28_EVEN',
		'ODD_EQ_11',
		'ODD_EQ_12',
		'ODD_EQ_13',
		'ODD_EQ_14',
		'ODD_EQ_15',
		'ODD_EQ_16',
		'ODD_EQ_17',
		'ODD_EQ_18',
		'EVEN_EQ_10',
		'EVEN_EQ_11',
		'EVEN_EQ_12',
		'EVEN_EQ_13',
		'EVEN_EQ_14',
		'EVEN_EQ_15',
		'EVEN_EQ_16',
		'EVEN_EQ_17'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$array = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3);
		$numbers = filter_numbers(range(1, 75), $array);


		for ($number = 8; $number <= 12; $number++) {
			// Любой из выпавших номеров кратен 8 (0 не кратное)
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] != 0 && ($nums[$i] % $number) == 0) {
						return true;
					}
				}
				return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $number]);
		}

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Четных номеров выпадет больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				)
			);
			$odd = count(
				array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				)
			);
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		for ($number = 67; $number <= 73; $number++) {
			// Разность наибольшего и наименьшего из выпавших номеров Больше ($number+0.5)
			calculate_case(function ($nums) use ($number) {
				return (max($nums) - min($nums)) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_' . $number . '.5']);
		}
		for ($number = 1062; $number <= 1066; $number++) {
			// Сумма всех выпавших номеров Больше ($number+0.5)
			calculate_case(function ($nums) use ($number) {
				return array_sum($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_GT_' . $number . '.5']);

		}

		for ($number = 1; $number <= 3; $number++) {
			// Наименьший выпавший номер Больше 1.5
			calculate_case(function ($nums) use ($number) {
				return min($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MIN_GT_' . $number . '.5']);
		}

		for ($number = 522; $number <= 526; $number++) {
			// Сумма всех выпавших четных номеров Больше ($number + 0.5)
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter(
					$nums,
					function ($num) {
						return $num % 2 == 0;
					}
				);
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_' . $number . '.5']);
		}

		for ($number = 536; $number <= 540; $number++) {
			// Сумма всех выпавших НЕчетных номеров Больше ($number+0.5)
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter(
					$nums,
					function ($num) {
						return $num % 2 != 0;
					}
				);
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_' . $number . '.5']);
		}

		for ($number = 1; $number <= 28; $number++) {
			// Выпадет номер $number
			calculate_case(function ($nums) use ($number) {
				return in_array($number, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number]);
		}

		for ($number = 1; $number <= 28; $number++) {
			// $number-й номер Чет
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);
		}

		for ($number = 11; $number <= 18; $number++) {
			// Количество выпавших НЕчетных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$odd = count(
					array_filter(
						$nums,
						function ($num) {
							return $num % 2 != 0;
						}
					)
				);
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);
		}

		for ($number = 10; $number <= 17; $number++) {
			// Количество выпавших Четных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$even = count(
					array_filter(
						$nums,
						function ($num) {
							return $num % 2 == 0;
						}
					)
				);
				return $even == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='bingo_75_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

add_action('wp_ajax_insert_velikolepnaya_8_for_gamers', 'insert_from_table_velikolepnaya_8_for_gamers');
add_action('wp_ajax_nopriv_insert_velikolepnaya_8_for_gamers', 'insert_from_table_velikolepnaya_8_for_gamers');
function insert_from_table_velikolepnaya_8_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_velikolepnaya_8_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_velikolepnaya_8_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ',' . $item->numbers[8] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_velikolepnaya_8_for_gamers', 'get_velikolepnaya_8_for_gamers');
add_action('wp_ajax_nopriv_get_velikolepnaya_8_for_gamers', 'get_velikolepnaya_8_for_gamers');
function get_velikolepnaya_8_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_velikolepnaya_8_for_gamers`');
	$keys = array(
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'MIN_EVEN',
		'MAX_EVEN',
		'ANY_DIV_7',
		'ANY_DIV_8',
		'ANY_DIV_9',
		'ANY_DIV_11',
		'ANY_DIV_12',
		'COUNT_EVEN_GT_COUNT_ODD',
		'SUM_MIN_MAX_EVEN',
		'SUM_EVEN_GT_42.5',
		'SUM_EVEN_GT_44.5',
		'SUM_EVEN_GT_45.5',
		'SUM_EVEN_GT_46.5',
		'SUM_EVEN_GT_47.5',
		'SUM_ODD_GT_38.5',
		'SUM_ODD_GT_39.5',
		'SUM_ODD_GT_40.5',
		'SUM_ODD_GT_41.5',
		'SUM_ODD_GT_42.5',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'NUM_8_EVEN',
		'DIFF_MAX_MIN_EVEN',
		'DIFF_MAX_MIN_GT_15.5',
		'DIFF_MAX_MIN_GT_16.5',
		'DIFF_MAX_MIN_GT_17.5',
		'DIFF_MAX_MIN_GT_18.5',
		'SUM_GT_84.5',
		'SUM_GT_85.5',
		'SUM_GT_86.5',
		'SUM_GT_87.5',
		'SUM_GT_88.5',
		'MIN_GT_1.5',
		'MAX_GT_18.5',
		'MAX_GT_19.5',
		'ODD_EQ_2',
		'ODD_EQ_3',
		'ODD_EQ_4',
		'ODD_EQ_5',
		'ODD_EQ_6',
		'ODD_EQ_7',
		'EVEN_EQ_2',
		'EVEN_EQ_3',
		'EVEN_EQ_4',
		'EVEN_EQ_5',
		'EVEN_EQ_6',
		'EVEN_EQ_7'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$special_prev = 0;
	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);
		$special = $row->NUMBER9;

		for ($number = 1; $number <= 20; $number++) {
			// Выпадет номер $number
			calculate_case(function ($nums) use ($number) {
				return in_array($number, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number]);
		}

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);


		for ($number = 7; $number <= 12; $number++) {
			if ($number == 10)
				continue;
			// Любой из выпавших номеров кратен $number (0 не кратное)
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] != 0 && $nums[$i] % $number == 0) {
						return true;
					}
				}return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $number]);
		}

		// Четных номеров выпадет больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);


		for ($number = 42; $number <= 47; $number++) {
			if ($number == 43)
				continue;
			// Сумма всех выпавших Четных номеров Больше 42.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 == 0;
				});
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_' . $number . '.5']);
		}

		for ($number = 38; $number <= 42; $number++) {
			// Сумма всех выпавших НЕчетных номеров Больше 38.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 != 0;
				});
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_' . $number . '.5']);
		}

		for ($number = 1; $number <= 8; $number++) {
			// $number-й номер ЧЕТ
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);
		}

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		for ($number = 15; $number <= 18; $number++) {
			// Разность наибольшего и наименьшего номеров Больше 15.5
			calculate_case(function ($nums) use ($number) {
				return (max($nums) - min($nums)) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_' . $number . '.5']);
		}
		for ($number = 84; $number <= 88; $number++) {
			// Сумма всех выпавших номеров Больше $number+0.5
			calculate_case(function ($nums) use ($number) {
				return array_sum($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_GT_' . $number . '.5']);
		}

		// Наименьший выпавший номер Больше 1.5
		calculate_case(function ($nums) {
			return min($nums) > 1.5;
		}, $numbers, $numbers_prev, $array_res['MIN_GT_1.5']);

		// Наибольший выпавший номер Больше 18.5
		calculate_case(function ($nums) {
			return max($nums) > 18.5;
		}, $numbers, $numbers_prev, $array_res['MAX_GT_18.5']);

		// Наибольший выпавший номер Больше 19.5
		calculate_case(function ($nums) {
			return max($nums) > 19.5;
		}, $numbers, $numbers_prev, $array_res['MAX_GT_19.5']);

		for ($number = 2; $number <= 7; $number++) {
			// Количество выпавших НЕчетных номеров Ровно 2
			calculate_case(function ($nums) use ($number) {
				$odd = count(array_filter($nums, function ($num) {
					return $num % 2 != 0;
				}));
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);
		}

		for ($number = 2; $number <= 7; $number++) {
			// Количество выпавших Четных номеров Ровно 2
			calculate_case(function ($nums) use ($number) {
				$even = count(array_filter($nums, function ($num) {
					return $num % 2 == 0;
				}));
				return $even == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='velikolepnaya_8_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}


add_action('wp_ajax_insert_lavina_prizov_for_gamers', 'insert_from_table_lavina_prizov_for_gamers');
add_action('wp_ajax_nopriv_insert_lavina_prizov_for_gamers', 'insert_from_table_lavina_prizov_for_gamers');

function insert_from_table_lavina_prizov_for_gamers()
{
	global $wpdb;
	$rows = [];
	if (isset($_REQUEST['rows'])) {
		$rows = json_decode(stripslashes($_REQUEST['rows']));
	} else
		wp_die();

	$wpdb->query('DELETE FROM `wp_lottery_lavina_prizov_for_gamers`');
	var_dump($rows);
	foreach ($rows as $item) {
		$sql_request_about_insert = "INSERT INTO `wp_lottery_lavina_prizov_for_gamers` (`CURRENT`,NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8) VALUES(" . $item->number . ',' . $item->numbers[0] . ',' . $item->numbers[1] . ',' . $item->numbers[2] . ',' . $item->numbers[3] . ',' . $item->numbers[4] . ',' . $item->numbers[5] . ',' . $item->numbers[6] . ',' . $item->numbers[7] . ")";
		$wpdb->query($sql_request_about_insert);
	}

	wp_die();
}


add_action('wp_ajax_get_lavina_prizov_for_gamers', 'get_lavina_prizov_for_gamers');
add_action('wp_ajax_nopriv_get_lavina_prizov_for_gamers', 'get_lavina_prizov_for_gamers');
function get_lavina_prizov_for_gamers()
{
	global $wpdb;
	$rows = $wpdb->get_results('SELECT * FROM `wp_lottery_lavina_prizov_for_gamers`');
	$keys = array(
		'NUM_1',
		'NUM_2',
		'NUM_3',
		'NUM_4',
		'NUM_5',
		'NUM_6',
		'NUM_7',
		'NUM_8',
		'NUM_9',
		'NUM_10',
		'NUM_11',
		'NUM_12',
		'NUM_13',
		'NUM_14',
		'NUM_15',
		'NUM_16',
		'NUM_17',
		'NUM_18',
		'NUM_19',
		'NUM_20',
		'MIN_EVEN',
		'MAX_EVEN',
		'ANY_DIV_7',
		'ANY_DIV_8',
		'ANY_DIV_9',
		'ANY_DIV_10',
		'ANY_DIV_11',
		'ADJACENT_NUMBERS_DIFF_FIELDS',
		'NUM_1_EVEN',
		'NUM_2_EVEN',
		'NUM_3_EVEN',
		'NUM_4_EVEN',
		'NUM_5_EVEN',
		'NUM_6_EVEN',
		'NUM_7_EVEN',
		'NUM_8_EVEN',
		'COUNT_EVEN_GT_COUNT_ODD',
		'DIFF_MAX_MIN_EVEN',
		'SUM_MIN_MAX_EVEN',
		'SUM_EVEN_GT_40.5',
		'SUM_EVEN_GT_42.5',
		'SUM_EVEN_GT_43.5',
		'SUM_EVEN_GT_44.5',
		'SUM_EVEN_GT_45.5',
		'SUM_ODD_GT_37.5',
		'SUM_ODD_GT_38.5',
		'SUM_ODD_GT_39.5',
		'SUM_ODD_GT_40.5',
		'SUM_ODD_GT_41.5',
		'MIN_GT_1.5',
		'MIN_GT_2.5',
		'MIN_GT_3.5',
		'MAX_GT_17.5',
		'MAX_GT_18.5',
		'MAX_GT_19.5',
		'DIFF_MAX_MIN_GT_12.5',
		'DIFF_MAX_MIN_GT_13.5',
		'DIFF_MAX_MIN_GT_14.5',
		'DIFF_MAX_MIN_GT_15.5',
		'DIFF_MAX_MIN_GT_16.5',
		'DIFF_MAX_MIN_GT_17.5',
		'DIFF_MAX_MIN_GT_18.5',
		'SUM_GT_82.5',
		'SUM_GT_83.5',
		'SUM_GT_84.5',
		'SUM_GT_85.5',
		'SUM_GT_86.5',
		'ODD_EQ_2',
		'ODD_EQ_3',
		'ODD_EQ_4',
		'ODD_EQ_5',
		'ODD_EQ_6',
		'EVEN_EQ_2',
		'EVEN_EQ_3',
		'EVEN_EQ_4',
		'EVEN_EQ_5',
		'EVEN_EQ_6'
	);

	$array_res = array();

	foreach ($keys as $key)
		$array_res[$key] = prepare_table_values();

	$numbers_prev = array(); foreach ($rows as $row) {
		$numbers = array($row->NUMBER1, $row->NUMBER2, $row->NUMBER3, $row->NUMBER4, $row->NUMBER5, $row->NUMBER6, $row->NUMBER7, $row->NUMBER8);

		for ($number = 1; $number <= 20; $number++) {
			// Выпадет номер $number
			calculate_case(function ($nums) use ($number) {
				return in_array($number, $nums);
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number]);
		}

		// Наименьший выпавший номер Чет
		calculate_case(function ($nums) {
			return (min($nums) % 2) == 0;
		}, $numbers, $numbers_prev, $array_res['MIN_EVEN']);

		// Наибольший выпавший номер Чет
		calculate_case(function ($nums) {
			return max($nums) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['MAX_EVEN']);

		for ($number = 7; $number <= 11; $number++) {
			// Любой из выпавших номеров кратен 7 (0 не кратное)
			calculate_case(function ($nums) use ($number) {
				$count = count($nums);
				for ($i = 0; $i < $count; $i++) {
					if ($nums[$i] != 0 && $nums[$i] % $number == 0) {
						return true;
					}
				}return false;
			}, $numbers, $numbers_prev, $array_res['ANY_DIV_' . $number]);
		}

		// Выпадут совпадающие номера на разных полях
		calculate_case(function ($nums) {
			$field1 = array_slice($nums, 0, 4);
			$field2 = array_slice($nums, 4);

			for ($i = 0; $i < count($field1); $i++) {
				for ($j = 0; $j < count($field2); $j++) {
					if ($field1[$i] == $field2[$j]) {
						return true;
					}
				}
			}

			return false;
		}, $numbers, $numbers_prev, $array_res['ADJACENT_NUMBERS_DIFF_FIELDS']);

		for ($number = 1; $number <= 8; $number++) {
			// $number-й номер ЧЕТ
			calculate_case(function ($nums) use ($number) {
				return $nums[$number - 1] % 2 == 0;
			}, $numbers, $numbers_prev, $array_res['NUM_' . $number . '_EVEN']);
		}

		// Четных номеров выпадет больше, чем НЕчетных
		calculate_case(function ($nums) {
			$even = count(array_filter($nums, function ($num) {
				return $num % 2 == 0;
			}));
			$odd = count(array_filter($nums, function ($num) {
				return $num % 2 != 0;
			}));
			return $even > $odd;
		}, $numbers, $numbers_prev, $array_res['COUNT_EVEN_GT_COUNT_ODD']);

		// Разность наибольшего и наименьшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) - min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_EVEN']);

		// Сумма наименьшего и наибольшего из выпавших номеров Чет
		calculate_case(function ($nums) {
			return (max($nums) + min($nums)) % 2 == 0;
		}, $numbers, $numbers_prev, $array_res['SUM_MIN_MAX_EVEN']);

		// Сумма всех выпавших Четных номеров Больше 40.5
		calculate_case(function ($nums) {
			$filtered = array_filter($nums, function ($num) {
				return $num % 2 == 0;
			});
			return array_sum($filtered) > 40.5;
		}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_40.5']);

		for ($number = 42; $number <= 45; $number++) {
			// Сумма всех выпавших Четных номеров Больше 42.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 == 0;
				});
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_EVEN_GT_' . $number . '.5']);
		}

		for ($number = 37; $number <= 41; $number++) {
			// Сумма всех выпавших Четных номеров Больше $number+0.5
			calculate_case(function ($nums) use ($number) {
				$filtered = array_filter($nums, function ($num) {
					return $num % 2 != 0;
				});
				return array_sum($filtered) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_ODD_GT_' . $number . '.5']);
		}

		for ($number = 1; $number <= 3; $number++) {
			// Наименьший выпавший номер Больше ($number+0.5)
			calculate_case(function ($nums) use ($number) {
				return min($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MIN_GT_' . $number . '.5']);
		}

		for ($number = 17; $number <= 19; $number++) {
			// Наибольший выпавший номер Больше $number+0.5
			calculate_case(function ($nums) use ($number) {
				return max($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['MAX_GT_' . $number . '.5']);
		}

		for ($number = 12; $number <= 18; $number++) {
			// Разность наибольшего и наименьшего номеров Больше $number + 0.5
			calculate_case(function ($nums) use ($number) {
				return (max($nums) - min($nums)) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['DIFF_MAX_MIN_GT_' . $number . '.5']);
		}

		for ($number = 82; $number <= 86; $number++) {
			// Сумма всех выпавших номеров Больше $number+0.5
			calculate_case(function ($nums) use ($number) {
				return array_sum($nums) > ($number + 0.5);
			}, $numbers, $numbers_prev, $array_res['SUM_GT_' . $number . '.5']);
		}

		for ($number = 2; $number <= 6; $number++) {
			// Количество выпавших НЕчетных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$odd = count(array_filter($nums, function ($num) {
					return $num % 2 != 0;
				}));
				return $odd == $number;
			}, $numbers, $numbers_prev, $array_res['ODD_EQ_' . $number]);
		}

		for ($number = 2; $number <= 6; $number++) {
			// Количество выпавших Четных номеров Ровно $number
			calculate_case(function ($nums) use ($number) {
				$even = count(array_filter($nums, function ($num) {
					return $num % 2 == 0;
				}));
				return $even == $number;
			}, $numbers, $numbers_prev, $array_res['EVEN_EQ_' . $number]);
		}

		$numbers_prev = $numbers;
	}

	foreach ($array_res as $key => $res) {
		$wpdb->query("UPDATE `wp_lottery_results` SET VALUE_YES=" . $res['VALUE_YES_MAX'] . ", VALUE_NO=" . $res['VALUE_NO_MAX'] . ", VALUE_YES_NOW=" . $res['VALUE_YES_NOW'] . ", VALUE_NO_NOW=" . $res['VALUE_NO_NOW'] . " where LOTO_TYPE='lavina_prizov_for_gamers' and VALUE_ID='" . $key . "'");
	}

	echo json_encode(array("results" => $array_res, "update" => date('Y:m:d H:i:s')));
	wp_die();
}

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function lot_widgets_init()
{
	register_sidebar(
		array(
			'name' => esc_html__('Sidebar', 'lot'),
			'id' => 'sidebar-1',
			'description' => esc_html__('Add widgets here.', 'lot'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		)
	);
}
add_action('widgets_init', 'lot_widgets_init');

add_action('wp_ajax_nopriv_register', 'register');
add_action('wp_ajax_register', 'register');

function register()
{
	$first_name = '';
	$second_name = '';
	$last_name = '';
	$gender = '';
	$phone = '';
	$birth = '';
	$years_of_playing = '';

	if (isset($_POST['first_name']))
		$first_name = $_POST['first_name'];

	if (isset($_POST['second_name']))
		$second_name = $_POST['second_name'];

	if (isset($_POST['last_name']))
		$last_name = $_POST['last_name'];

	if (isset($_POST['gender']))
		$gender = $_POST['gender'];

	if (isset($_POST['phone']))
		$phone = $_POST['phone'];

	if (isset($_POST['email']))
		$email = $_POST['email'];

	if (isset($_POST['birth']))
		$birth = $_POST['birth'];

	if (isset($_POST['years_of_playing']))
		$years_of_playing = $_POST['years_of_playing'];

	$user_id = wp_insert_user(
		array(
			'user_login' => '@user' . random_bytes(30),
			'user_pass' => random_bytes(30),
			'nice_name' => $first_name . ' ' . $last_name,
			'user_email' => $email,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'meta_input' => array(
				'birth' => $birth,
				'second_name' => $second_name,
				'years_of_playing' => $years_of_playing,
				'phone' => $phone,
				'gender' => $gender,
			)
		)
	);

	if (!is_wp_error($user_id)) {
		echo json_encode(array('success' => 'true'));
	} else {
		echo json_encode(array('success' => 'false', 'error' => $user_id->get_error_message()));
	}

	wp_die();
}


add_action('wp_ajax_logout', 'logout');
add_action('wp_ajax_nopriv_logout', 'logout');
function logout()
{
	wp_logout();
}

add_action('wp_ajax_nopriv_login', 'login');
add_action('wp_ajax_login', 'login');
function login()
{
	$email = '';
	$first_name = '';
	$second_name = '';
	$last_name = '';

	if (isset($_POST['email']))
		$email = $_POST['email'];
	if (isset($_POST['first_name']))
		$first_name = $_POST['first_name'];
	if (isset($_POST['second_name']))
		$second_name = $_POST['second_name'];
	if (isset($_POST['last_name']))
		$last_name = $_POST['last_name'];

	if ($email == '') {
		echo json_encode(array('success' => 'false', 'error' => 'Email пустой!'));
		wp_die();
	}

	if ($email != '') {
		$user = get_user_by('email', $email); //  берём пользователя по почте.
		if (!isset($user)) {
			echo json_encode(array('success' => 'false', 'error' => 'Пользователь не найден.'));
			wp_die();
		}
		$user_id = $user->ID;

		wp_set_current_user($user_id, $user->user_login);
		wp_set_auth_cookie($user_id);
		do_action('wp_login', $user->user_login);
		echo json_encode(array('success' => 'true'));
	}

	wp_die();
}


add_action('wp_ajax_update-user', 'update_user');
add_action('wp_ajax_nopriv_update-user', 'update_user');

function update_user()
{
	$first_name = '';
	$second_name = '';
	$last_name = '';
	$gender = '';
	$phone = '';
	$birth = '';
	$years_of_playing = '';
	$user_id = -1;

	if (isset($_POST['user_id']))
		$user_id = $_POST['user_id'];

	if (isset($_POST['first_name']))
		$first_name = $_POST['first_name'];

	if (isset($_POST['second_name']))
		$second_name = $_POST['second_name'];

	if (isset($_POST['last_name']))
		$last_name = $_POST['last_name'];

	if (isset($_POST['gender']))
		$gender = $_POST['gender'];

	if (isset($_POST['phone']))
		$phone = $_POST['phone'];

	if (isset($_POST['email']))
		$email = $_POST['email'];

	if (isset($_POST['birth']))
		$birth = $_POST['birth'];

	if (isset($_POST['years_of_playing']))
		$years_of_playing = $_POST['years_of_playing'];

	if (!isset($user_id) || $user_id == -1) {
		echo json_encode(array('success' => 'false', 'error' => 'Не найден пользователь'));
		wp_die();
	} else {
		wp_update_user(
			array(
				'ID' => $user_id,
				'user_email' => $email,
				'first_name' => $first_name,
				'last_name' => $last_name,
			)
		);

		$metas = array(
			'birth' => $birth,
			'second_name' => $second_name,
			'years_of_playing' => $years_of_playing,
			'phone' => $phone,
			'gender' => $gender
		);

		foreach ($metas as $key => $value) {
			update_user_meta($user_id, $key, $value);
		}
		echo json_encode(array('success' => true));
		wp_die();
	}

}



add_action('after_setup_theme', 'lot_setup');

/**
 * Enqueue scripts and styles.
 */
function lot_scripts()
{
	wp_enqueue_style('lot-style', get_stylesheet_uri(), array(), _S_VERSION);
	wp_style_add_data('lot-style', 'rtl', 'replace');

	wp_enqueue_script('lot-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}

add_action('wp_enqueue_scripts', 'lot_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}