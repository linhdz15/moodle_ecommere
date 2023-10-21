<?php
require_once '../../config.php';
require_once 'JsonMapper.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';
global $USER, $DB, $USER;

$debug = false;
/**
 * handle AJAX add_to_cart function
 */
if (($_POST && $_POST['function'] == 'add_to_cart') || $debug == 'add_to_cart') {

	if (!$_POST && $debug == 'add_to_cart') {
		$courseid = 2;
	} else {
		$courseid = $_POST['courseid'];
	}

	$response = new stdClass();

	if (!isloggedin()) {
		//Xử lý khi không đăng nhập
		return;
	}

	$response->isloggedin = true;

	$cart_info = $DB->get_record_sql("SELECT * FROM {local_th_edumy_cart} WHERE user_id = $USER->id AND cart_info = $courseid");
	
	if (!$cart_info) {
		$data = new stdClass();
		$data->user_id = $USER->id;
		$data->cart_info = $courseid;
		$data->timecreated = time();
		$DB->insert_record('local_th_edumy_cart', $data);
		$response->duplicate = false;
	} else {
		$response->duplicate = true;
	}

	$all_cart_info = $DB->get_records('local_th_edumy_cart', ['user_id' => $USER->id]);
	if($all_cart_info){
		$response->num_product = count($all_cart_info);
	} else {
		$response->num_product = 0;
	}
	
	echo json_encode($response);
	return;
}

/**
 * handle AJAX get_card_number function
 */
if (($_POST && $_POST['function'] == 'get_card_number') || $debug == 'get_card_number') {

	if (!$_POST && $debug == 'get_card_number') {

	} else {

	}

	$response = new stdClass();
	if (!isloggedin()) {
		$response->isloggedin = false;
		echo json_encode($response);
		return;
	}

	$response->isloggedin = true;

	$cart_info_all = $DB->get_records('local_th_edumy_cart', ['user_id' => $USER->id]);
	if ($cart_info_all) {
		$response->num_product = count($cart_info_all);
	} else {
		$response->num_product = 0;
	}
	echo json_encode($response);
	return;
}


if (($_POST && $_POST['function'] == 'check_method') || $debug == 'check_method') { 
	$response = new stdClass();
	$payment_method = get_config('theme_th_edumy', 'list_payment_method');
	$payment_method = explode( ',',  $payment_method);
	$method_text = [];
	foreach($payment_method as $method) {		
		if($method == 1) {
			$method_text[] = array('text' => 'Thanh toán bằng chuyển khoản ngân hàng', 'value' => 1);
		}
		if($method == 2) {
			$method_text[] = array('text' => 'Thanh toán bằng ONEPAY', 'value' => 2);
		}
	}

	$response = $method_text;
	echo json_encode($response);
}
/**
 * handle AJAX check price function
 */
if (($_POST && $_POST['function'] == 'checkprice') || $debug == 'checkprice') {

	if (!$_POST && $debug == 'checkprice') {

	} else {

	}

	$response = new stdClass();

	echo json_encode($response);
	return;
}

?>