<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';

use core_course\external\course_summary_exporter;

global $DB, $OUTPUT, $PAGE, $COURSE, $CFG;

// Check for all required variables.
$courseid = $COURSE->id;
$page = optional_param('page', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_th_error_course', $courseid);
}

$pageurl = '/blocks/th_cocoon_course_categories_banner/th_all_course.php';
$title = 'Tất cả khóa học';
$PAGE->set_url('/blocks/th_cocoon_course_categories_banner/th_all_course.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($title);
$PAGE->set_title($SITE->fullname . ': ' . $title);

$instanceid = 'th_all_course';
$renderer = $PAGE->get_renderer('block_th_cocoon_course_categories_banner');

echo $OUTPUT->header();

$course_all = $DB->get_records_sql("SELECT * FROM {course} WHERE visible = 1 AND NOT id = 1");

$list_cate_product1 = [];
foreach($course_all as $course){
    $course_shortname = $course->shortname;
    
    $course->list_price = 900000;
    $course->sale_product = 499000;
    $course->fullname1 = $course->fullname;
    $list_cate_product1[] = $course;
    
}

// Số mục trên mỗi trang
$itemsPerPage = 12;

// Lấy mục dữ liệu tương ứng với trang hiện tại
$start = $page * $itemsPerPage;
$currentPageProducts = array_slice($list_cate_product1, $start, $itemsPerPage);

echo '<p class="th_all_course_title">Tất cả khóa học</p>';
echo '<div>
        <div class="row">';
foreach($currentPageProducts as $cate_product){
    
    $roleid = $DB->get_field_sql("SELECT id FROM {role} WHERE shortname = 'editingteacher'"); 
    $giang_vien = $DB->get_record_sql("SELECT u.* FROM {enrol} as e, {user_enrolments} as ue, {user} as u, {context} as c, {role_assignments} as ra WHERE ue.status = 0 AND e.courseid = '$cate_product->id' AND e.enrol = 'manual' AND e.id = ue.enrolid AND u.id = ue.userid AND c.instanceid = '$cate_product->id' AND c.contextlevel = '50' AND c.id = ra.contextid AND ra.userid = u.id AND ra.roleid = '$roleid'");

    $course_image = course_summary_exporter::get_course_image($cate_product);
    
    if(!$course_image){
        $course_image = $CFG->wwwroot . '/blocks/th_cocoon_course_categories_banner/img/logo_vmc.jpg';
    }

    if($giang_vien){
        $user_picture = $OUTPUT->user_picture($giang_vien);
        $teacher_fullname = $giang_vien->firstname.' '.$giang_vien->lastname;
    } else {
        $user_picture = '<img src ="https://media.istockphoto.com/id/1307140504/vector/user-profile-icon-vector-avatar-portrait-symbol-flat-shape-person-sign-logo-black-silhouette.jpg?s=170667a&w=0&k=20&c=stO_H-1pGTJYJEieqe1KLmI0FkKlMzfWb6YJ2K_vqAI=">';
        $teacher_fullname = 'Chưa có giảng viên ';
    }
    
    $product_name = $cate_product->fullname1;
    $product_price_sale = number_format($cate_product->sale_product, 0, ',', '.') . 'đ';
    $product_price_full = number_format($cate_product->list_price, 0, ',', '.') . 'đ';
    if(mb_strlen($product_name, 'UTF-8') > 80) {        
        $product_name = mb_substr($product_name , 0 , 80, 'UTF-8') . '...';
    }

    $btn_id = "th_btn_themvaogiohang_{$instanceid}_{$cate_product->id}";
    $is_combo = false;
    $shortname = $cate_product->shortname;
    $data = [
        'btnid' => $btn_id,
        'courseid' => $cate_product->id,
        'combo' => $is_combo,
        'default_code' => $shortname,
    ];

    $add_to_cart = $renderer->render_from_template('local_th_edumy_cart/add_to_cart_btn', $data);
	$buynow_btn = $renderer->render_from_template('local_th_edumy_cart/buynow_btn', $data);

    echo '<div class="col-xl-3 col-lg-4 col-md-6 col-xs-12">
            <div class="th_all_course">
                <div class="product_item">
                    <div class = "product-img">
                        <img src = "'.$course_image.'" alt = "" class = "img-fluid d-block mx-auto course_img">
                    </div>
                    <div class = "product-info">
                        <a href = "'.$CFG->wwwroot."/course/view.php?id=$cate_product->id".'" class = "d-block text-decoration-none py-2 product-name">'.$product_name.'</a>
                        <div class="teacher-course">
                            '.$user_picture.'
                            <span>'.$teacher_fullname.'</span>
                        </div>
                        <div class="product_buy">'
                            . $buynow_btn
                            . $add_to_cart . '
                        </div>
                        <div class = "price_course">
                            <div class ="product_price">
                                <span class = "product-price-full">'.$product_price_full.'</span>   
                                <span class = "product-sale-price-new">'.$product_price_sale.'</span>
                            </div>                              
                        </div>
                    </div>      
                </div>
            </div>              
        </div>';
}

//panigation
$baseurl = new moodle_url('/blocks/th_cocoon_course_categories_banner/th_all_course.php', array('page' => $page));
echo $OUTPUT->paging_bar(count($list_cate_product1), $page, $itemsPerPage, $baseurl);

echo $OUTPUT->footer();
?>