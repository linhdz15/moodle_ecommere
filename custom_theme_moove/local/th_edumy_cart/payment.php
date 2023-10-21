<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->libdir . '/csvlib.class.php';
require_once $CFG->dirroot . '/local/th_edumy_cart/lib.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';

global $DB, $CFG, $COURSE, $USER;

$id = $USER->id;

if (!$course = $DB->get_record('course', array('id' => $COURSE->id))) {
	print_error('invalidcourse', 'local_th_edumy_cart', $COURSE->id);
}

$PAGE->set_url(new moodle_url('/local/th_edumy_cart/payment.php'));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Thanh toán');
$PAGE->set_title('Thanh toán');
$PAGE->settingsnav->add('Thanh toán', $editurl);

$payment_key = optional_param('key', null, PARAM_TEXT);

global $USER, $SESSION;

if ($payment_key) {

	$cart = $SESSION->cart[$payment_key];
	$renderer = $PAGE->get_renderer('local_th_edumy_cart');
	$content = $renderer->render_payment($cart);
} else {
	$content = '';
}

echo $OUTPUT->header();
$style = file_get_contents('style.css');
echo "<style>$style</style>";
echo $content;
echo $OUTPUT->footer();
?>