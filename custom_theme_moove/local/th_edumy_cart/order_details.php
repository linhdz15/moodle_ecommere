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

$ordername = optional_param('order', '', PARAM_TEXT);
$PAGE->set_url(new moodle_url('/local/th_edumy_cart/order_details.php?order='.$ordername.''));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Chi tiết đơn hàng');
$PAGE->set_title('Chi tiết đơn hàng');
$editurl = new moodle_url('/local/th_edumy_cart/order_details.php');
$PAGE->settingsnav->add('Chi tiết đơn hàng', $editurl);
$get_busing_history = th_ecommercelib::get_busing_history();
$list_cate_product = th_ecommercelib::get_list_remote_products();

$content = "";
$data = new stdClass();

$cart_data = [];
$order = [];
foreach($get_busing_history as $order) {
    if($order['ordername'] == $ordername) {
        $product_arr = [];
        $products = $order['products'];
        foreach($products as $product) {
            $default_code = $product['default_code'];
            if (isset($list_cate_product->$default_code)) {
                $cart = $list_cate_product->$default_code;
                if ($cart->is_combo == 1) {
                    if (isset($cart->image_base64)) {
                        $product['img'] = 'data:image/png;base64, ' . $cart->image_base64 . '';
                    }
                }else {
                    $product['img'] = $cart->image_url;
                }
                $product_price_sale = number_format($product['subtotal'], 0, ',', '.') . 'đ';
                $product_price_full = number_format($product['unit_price'], 0, ',', '.') . 'đ';
                $product['price_sale'] = $product_price_sale;
                $product['price_full'] = $product_price_full;
                $product_arr[] = $product;
            }
            $reward = $product['reward'];
            if($reward == 1) {
                $order['discount_code'] = $product; 
                $order['discount_code']['unit_price']  = number_format($product['unit_price'], 0, ',', '.') . 'đ';   
                $order['discount_code']['subtotal']  = number_format($product['subtotal'], 0, ',', '.') . 'đ';      
            }
        }

        if($order['state'] == 'sale') {
            $order['status'] = get_string('sale','local_th_edumy_cart');
            $order['class_status'] = 'th-sale-order';
        }else {
            $order['status'] = get_string('draft','local_th_edumy_cart');
            $order['class_status'] = 'th-draft-order';
        }

        if($order['payment_provider'] == 'Wire Transfer') {
            $order['payment_provider']  = get_string('payment_provider','local_th_edumy_cart');
        }

        $order['total'] = number_format($order['total'], 0, ',', '.') . 'đ';       
        $order['product'] =  $product_arr;
        $data->cart = $order;
        
    }   
    
}

$renderer = $PAGE->get_renderer('local_th_edumy_cart');
$content = $renderer->render_from_template('local_th_edumy_cart/order_details/order_details', $data);


	
echo $OUTPUT->header();
$style = file_get_contents('style_order_details.css');
echo "<style>$style</style>";
echo $content;
echo $OUTPUT->footer();
?>