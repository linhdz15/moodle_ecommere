<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->libdir . '/csvlib.class.php';
require_once $CFG->dirroot . '/local/th_edumy_cart/lib.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';
require_once 'JsonMapper.php';
require_once 'JsonMapper/Exception.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';

use local_th_edumy_cart\output\cart as cart;

global $DB, $CFG, $COURSE, $USER;


$PAGE->set_url(new moodle_url('/local/th_edumy_cart/buysing_history.php'));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Lịch sử mua hàng');
$PAGE->set_title('Lịch sử mua hàng');
$editurl = new moodle_url('/local/th_edumy_cart/buysing_history.php');
$PAGE->settingsnav->add('Lịch sử mua hàng', $editurl);
$get_busing_history = th_ecommercelib::get_busing_history();
$list_cate_product = th_ecommercelib::get_list_remote_products();
if($get_busing_history['status'] != 'Error') {
    $data = new stdClass();
    $cart_data = [];

    $orders = [];
    foreach($get_busing_history as $order) {
        $product_arr = [];
        $products = $order['products'];
        foreach($products as $product) {
            $default_code = $product['default_code'];
            $reward = $product['reward'];
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
            if($reward == 1) {
                $order['discount_code'] = $product;
                
            }
        }
        
        $order['product'] =  $product_arr;
        $order['url'] = $CFG->wwwroot.'/local/th_edumy_cart/order_details.php';
        if($order['state'] == 'sale') {
            $order['status'] = get_string('sale','local_th_edumy_cart');
            $order['class_status'] = 'th-sale-order';
        }else {
            $order['status'] = get_string('draft','local_th_edumy_cart');
            $order['class_status'] = 'th-draft-order';
        }
        $orders[] = $order;   
        $data->order = $orders;
    
    }

    $renderer = $PAGE->get_renderer('local_th_edumy_cart');
    $content = $renderer->render_from_template('local_th_edumy_cart/buysing_history/buysing_history', $data);
}else {
    $content = '<div class="post-detail__contents">
        <div class="woocommerce">
            <p class="cart-empty th-woocommerce-info">Lịch sử mua hàng trống.</p>	
            <div class="d-flex justify-content-between" style="margin-top: 15px;">
                <div>
                    <a role="button" href="'.$CFG->wwwroot.'/?redirect=0" class="btn btn-secondary mb32 th-btn-go-back">
                        <span class="fa fa-chevron-left"></span>
                        <span>Quay trở lại cửa hàng</span>
                    </a>
                </div>
            </div>
        </div>
    </div>';
}



	
echo $OUTPUT->header();
$style = file_get_contents('style_busing_history.css');
echo "<style>$style</style>";
echo $content;
echo $OUTPUT->footer();
?>