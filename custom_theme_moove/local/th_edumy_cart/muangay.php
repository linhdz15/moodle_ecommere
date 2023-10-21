<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->libdir . '/csvlib.class.php';
require_once $CFG->dirroot . '/local/th_edumy_cart/lib.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';
require_once 'JsonMapper.php';
require_once 'JsonMapper/Exception.php';

use core_course\external\course_summary_exporter;
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

$PAGE->set_url(new moodle_url('/local/th_edumy_cart/muangay.php'));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Giỏ hàng - Mua ngay');
$PAGE->set_title('Giỏ hàng - Mua ngay');
$editurl = new moodle_url('/local/th_edumy_cart/muangay.php');
$PAGE->settingsnav->add('Giỏ hàng - Mua ngay', $editurl);

$id = optional_param('courseid', -1, PARAM_INT);

if ($login) {

	$cart_info = $DB->get_records('local_th_edumy_cart', ['user_id' => $USER->id, 'cart_info' => $id]);
	$total_price = 0;

	if($cart_info){
		$cart = new stdClass();
		$product_arr = [];
		foreach($cart_info as $cart_data){
			$course_id = $cart_data->cart_info;
			$course = $DB->get_record_sql("SELECT c.*, e.price as product_price_full, e.price_sale as product_price_sale FROM {course} as c
											LEFT JOIN {th_ecommerce_course} as e ON c.id = e.course_id
											WHERE c.id = $course_id");

			$course->img = course_summary_exporter::get_course_image($course);
			$total_price = $total_price + $course->product_price_sale;

			if($course->product_price_full){
				$course->product_price_full = number_format($course->product_price_full, 0, ',', '.') . 'đ';
			}

			if($course->product_price_sale){
				$course->product_price_sale = number_format($course->product_price_sale, 0, ',', '.') . 'đ';
			}

			$course->id_cart = $cart_data->id;

			$product_arr[] = $course;
		}

		$payment_key = 'payment_'.time();
		$cart->payment_key = $payment_key;
		$cart->products = $product_arr;
		$cart->total_price = number_format($total_price, 0, ',', '.');
		$cart->sale_price = 0;

		$renderer = $PAGE->get_renderer('local_th_edumy_cart');
		$content = $renderer->render_cart($cart);

		$SESSION->cart[$payment_key] = $cart;

	} else {
		$content = '
			<div class="post-detail__contents">
				<div class="woocommerce">
					<p class="cart-empty th-woocommerce-info">Chưa có sản phẩm nào trong giỏ hàng.</p>
					<div class="d-flex justify-content-between" style="margin-top: 15px;">
						<div>
							<a role="button" href="' . $CFG->wwwroot . '/?redirect=0" class="btn btn-secondary mb32 th-btn-go-back">
								<span class="fa fa-chevron-left"></span>
								<span>Quay trở lại cửa hàng</span>
							</a>
						</div>
					</div>
				</div>
			</div>';
	}

	$renderer = $PAGE->get_renderer('local_th_edumy_cart');
	$content = $renderer->render_cart($cart);
} else {
}

echo $OUTPUT->header();
$style = file_get_contents('style.css');
echo "<style>$style</style>";

echo $content;
echo $OUTPUT->footer();
?>