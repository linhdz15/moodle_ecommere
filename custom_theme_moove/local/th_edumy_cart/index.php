<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->dirroot . '/local/th_edumy_cart/lib.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';

use core_course\external\course_summary_exporter;

global $DB, $CFG, $COURSE, $USER, $PAGE;

$id = $USER->id;

if (!$course = $DB->get_record('course', array('id' => $COURSE->id))) {
	print_error('invalidcourse', 'local_th_edumy_cart', $COURSE->id);
}

if (isloggedin() && !isguestuser()) {
	$login = true;
} else {
	$login = false;
}

$PAGE->set_url(new moodle_url('/local/th_edumy_cart/index.php'));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Giỏ hàng');
$PAGE->set_title('Giỏ hàng');
$editurl = new moodle_url('/local/th_edumy_cart/index.php');
$PAGE->settingsnav->add('Giỏ hàng', $editurl);

$action = optional_param('action', null, PARAM_TEXT);
$id = optional_param('id', -1, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

if($login){
	if ($action == "delete_product" && $id >= 0) {
		$PAGE->url->param('delete', 1);
		if ($confirm and confirm_sesskey()) {
			$DB->delete_records('local_th_edumy_cart', ['id' => $id]);
			redirect($CFG->wwwroot . '/local/th_edumy_cart/index.php', get_string('success'), null, \core\output\notification::NOTIFY_SUCCESS);
		}
		$strheading = get_string('delete');
		$PAGE->navbar->add($strheading);
		$PAGE->set_title($strheading);
		$PAGE->set_heading($COURSE->fullname);
		echo $OUTPUT->header();
		echo $OUTPUT->heading($strheading);
		$record = $DB->get_record_sql("SELECT c.* FROM {local_th_edumy_cart} as e, {course} as c WHERE e.cart_info = c.id AND e.id = $id");
		$yesurl = new moodle_url('/local/th_edumy_cart/index.php', array('id' => $id, 'action' => 'delete_product', 'confirm' => 1, 'delete' => 1, 'sesskey' => sesskey()));
		$message = "Bạn có chắc chắn muốn xóa khóa học " . html_writer::tag('b', $record->fullname);
		echo $OUTPUT->confirm($message, $yesurl, new moodle_url('/local/th_edumy_cart/index.php'));
		echo $OUTPUT->footer();
		die;
	}

	$all_cart_info = $DB->get_records('local_th_edumy_cart', ['user_id' => $USER->id]);

	$total_price = 0;

	if($all_cart_info){
		$cart = new stdClass();
		$product_arr = [];
		foreach($all_cart_info as $cart_info){
			$course_id = $cart_info->cart_info;
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

			$course->id_cart = $cart_info->id;

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

	$style = file_get_contents('style.css');
	echo "<style>$style</style>";

	echo $OUTPUT->header();
	echo $content;
	echo $OUTPUT->footer();

}
