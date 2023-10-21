<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->libdir . '/csvlib.class.php';
require_once $CFG->dirroot . '/local/th_edumy_cart/lib.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';
require_once 'JsonMapper.php';
require_once 'JsonMapper/Exception.php';
use local_th_edumy_cart\output\cart as cart;

global $DB, $CFG, $COURSE, $USER;

$id = $USER->id;

$login;

if (!$course = $DB->get_record('course', array('id' => $COURSE->id))) {
	print_error('invalidcourse', 'local_th_edumy_cart', $COURSE->id);
}

if (isloggedin() && !isguestuser()) {
	$login = true;
} else {
	$login = false;
}

$PAGE->set_url(new moodle_url('/local/th_edumy_cart/confirmation.php'));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Confirmation');
$PAGE->set_title('Confirmation');
$editurl = new moodle_url('/local/th_edumy_cart/confirmation.php');
$PAGE->settingsnav->add('Confirmation', $editurl);

$content = "";
global $USER, $SESSION;

$confirm_order = $SESSION->th_edumy_cart_confirm_order;
$typecart = $SESSION->th_type_cart;
unset($SESSION->th_type_cart);
unset($SESSION->th_edumy_cart_confirm_order);

if ($confirm_order['faultCode']) {
	$PAGE->requires->js_call_amd('local_th_ecommercelib/th_cart', 'showError', array('message' => json_encode($confirm_order, JSON_PRETTY_PRINT)));
}

if ($confirm_order) {

	$order_code = $confirm_order['order_code'];

	if (!isloggedin()) {

		if ($order_code) {
			$cart = $SESSION->th_edumy_cart;
			unset($SESSION->th_type_cart);
			$renderer = $PAGE->get_renderer('local_th_edumy_cart');
			$content = $renderer->render_from_template('local_th_edumy_cart/confirmation/confirmation', $cart->export_for_template($renderer));

			$PAGE->requires->js_call_amd('local_th_ecommercelib/th_cart', 'clear_cart_in_localstorage');
			$PAGE->requires->js_call_amd('local_th_ecommercelib/th_cart', 'save_confirmation_cart_to_localstorage', array('cart' => $cart));
		} else {
			$content = "Invalid or expired Coupon or product price changed! Please Try Again!";
		}

	} else {
		if ($order_code) {

			$data = [];
			$data['order_code'] = $confirm_order['order_code'];
			$data['order_total'] = '500.000';

			if ($typecart == "type_muangay") {

				$cart_record = $DB->get_record('local_th_edumy_muangay', ['user_id' => $USER->id]);
				if ($cart_record) {
					$DB->delete_records('local_th_edumy_muangay', ['user_id' => $USER->id]);
					unset($cart_record->id);
					$confirm_record = $DB->get_record('local_th_edumy_confirmation', ['user_id' => $USER->id]);
					if ($confirm_record) {
						$confirm_record->cart_info = $cart_record->cart_info;
						$DB->update_record('local_th_edumy_confirmation', $confirm_record);
					} else {
						$DB->insert_record('local_th_edumy_confirmation', $cart_record);
					}
				} else {
					$cart_record = $DB->get_record('local_th_edumy_confirmation', ['user_id' => $USER->id]);
				}

			} else {
				$cart_record = $DB->get_record('local_th_edumy_cart', ['user_id' => $USER->id]);
				if ($cart_record) {
					$DB->delete_records('local_th_edumy_cart', ['user_id' => $USER->id]);
					unset($cart_record->id);
					$confirm_record = $DB->get_record('local_th_edumy_confirmation', ['user_id' => $USER->id]);
					if ($confirm_record) {
						$confirm_record->cart_info = $cart_record->cart_info;
						$DB->update_record('local_th_edumy_confirmation', $confirm_record);
					} else {
						$DB->insert_record('local_th_edumy_confirmation', $cart_record);
					}
				} else {
					$cart_record = $DB->get_record('local_th_edumy_confirmation', ['user_id' => $USER->id]);
				}
			}

			$json_cart = $cart_record != null ? $cart_record->cart_info : null;

			$mapper = new JsonMapper();
			$mapper->bStrictNullTypes = false;
			$cart = $mapper->map((object) json_decode($json_cart, true), cart::class);

			$renderer = $PAGE->get_renderer('local_th_edumy_cart');
			$content = $renderer->render_from_template('local_th_edumy_cart/confirmation/confirmation', $cart->export_for_template($renderer));

		} else {
			$content = "Invalid or expired Coupon or product price changed! Please Try Again!";
		}
	}

} else {

	if (!isloggedin()) {
		$content = "<div id='wrap'></div>";
		$PAGE->requires->js_call_amd('local_th_ecommercelib/th_cart', 'render_confirmation_order');

	} else {
		$cart_record = $DB->get_record('local_th_edumy_confirmation', ['user_id' => $USER->id]);
		$json_cart = $cart_record != null ? $cart_record->cart_info : null;

		if ($json_cart) {
			$mapper = new JsonMapper();
			$mapper->bStrictNullTypes = false;
			$cart = $mapper->map((object) json_decode($json_cart, true), cart::class);

			$renderer = $PAGE->get_renderer('local_th_edumy_cart');
			$content = $renderer->render_from_template('local_th_edumy_cart/confirmation/confirmation', $cart->export_for_template($renderer));
		}
	}
}

echo $OUTPUT->header();
$style = file_get_contents('style.css');
echo "<style>$style</style>";

echo $content;
echo $OUTPUT->footer();
?>