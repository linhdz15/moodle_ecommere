<?php

require_once '../../../config.php';
require_once($CFG->dirroot. '/local/th_ecommercelib/lib.php');
use core_course\external\course_summary_exporter;
global $DB, $USER, $OUTPUT, $PAGE, $COURSE, $CFG;

// Check for all required variables.
$courseid = $COURSE->id;
$page = optional_param('page', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_th_error_course', $courseid);
}

$pageurl = '/local/th_ecommercelib/search/th_search_product.php';
$title = 'Tìm kiếm khóa học';
$PAGE->set_url('/local/th_ecommercelib/search/th_search_product.php');
$PAGE->set_pagelayout('standard');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading($title);
$PAGE->set_title($SITE->fullname . ': ' . $title);

$instanceid = 'th_search_product';
$renderer = $PAGE->get_renderer('local_th_ecommercelib');

echo $OUTPUT->header();

if($search){
	
	$chelper = array(
		'sort' => array(
				'displayname' => 1
			),
		'offset' => 0,
		'paginationallowall' => 0,
		'summary' => 1,
		'coursecontacts' => 1,
		'customfields' => 1
	);
	$courses = array();
	if($arrcourses = core_course_category::search_courses(array('search' => $search), $chelper)){
		foreach ($arrcourses as $course) {
			$data = new \stdClass();
			$data->id = $course->id;
			$data->fullname = $course->fullname;
			$data->shortname = $course->shortname;
			$courseimage = course_summary_exporter::get_course_image($data);;
			if (!$courseimage) {
				$courseimage = '';
			}
			$data->courseimage = $courseimage;
			$courses[] = $data;
		}
	}


	$list_cate_product = th_ecommercelib::get_list_remote_products();

	$list_cate_product1 = [];
	foreach($courses as $course){
		$course_shortname = $course->shortname;
		if(isset($list_cate_product->$course_shortname)){
			$course->fullname = $list_cate_product->$course_shortname->name;
			$course->list_price = number_format($list_cate_product->$course_shortname->list_price, 0, ',', '.') . 'đ';
			$course->sale_product = number_format($list_cate_product->$course_shortname->sale_product, 0, ',', '.') . 'đ';
			$course->is_api = true;
		} else {
			$course->list_price = '';
			$course->sale_product = '';
			$course->is_api = false;
		}
		
		$list_cate_product1[] = $course;
	}

	// Số mục trên mỗi trang
	$itemsPerPage = 12;

	// Lấy mục dữ liệu tương ứng với trang hiện tại
	$start = $page * $itemsPerPage;
	$currentPageProducts = array_slice($list_cate_product1, $start, $itemsPerPage);

	echo '<p class="th_search_product_title">Tất cả khóa học tìm thấy: '.$search.'</p>';

	echo '<div>
	        <div class="row">';
	foreach($currentPageProducts as $cate_product){
	    
	    $roleid = $DB->get_field_sql("SELECT id FROM {role} WHERE shortname = 'editingteacher'"); 
	    $giang_vien = $DB->get_record_sql("SELECT u.* FROM {enrol} as e, {user_enrolments} as ue, {user} as u, {context} as c, {role_assignments} as ra WHERE ue.status = 0 AND e.courseid = '$cate_product->id' AND e.enrol = 'manual' AND e.id = ue.enrolid AND u.id = ue.userid AND c.instanceid = '$cate_product->id' AND c.contextlevel = '50' AND c.id = ra.contextid AND ra.userid = u.id AND ra.roleid = '$roleid'");

	    $course_image = $cate_product->courseimage;

		if(!$course_image){
			$course_image = $CFG->wwwroot . '/theme/moove/img/logo_vmc.jpg';
		}

	    if($giang_vien){
	        $user_picture = $OUTPUT->user_picture($giang_vien);
	        $teacher_fullname = $giang_vien->firstname.' '.$giang_vien->lastname;
	    } else {
	        $user_picture = '<img src =" '.$CFG->wwwroot.'/theme/moove/img/logo_vmc.jpg" loading="lazy" decoding="async">';
	        $teacher_fullname = 'VMC Việt Nam';
	    }
	    
	    $product_name = $cate_product->fullname;
	    $product_price_sale = $cate_product->sale_product;
	    $product_price_full = $cate_product->list_price;
	    if(mb_strlen($product_name) > 90) {        
	        $product_name = mb_substr($cate_product->fullname , 0 , 90, 'UTF-8') . '...';
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

		if($cate_product->is_api){
			$add_to_cart = $renderer->render_from_template('local_th_edumy_cart/add_to_cart_btn', $data);
			$buynow_btn = $renderer->render_from_template('local_th_edumy_cart/buynow_btn', $data);
		} else {
			$add_to_cart = '<a id="disabled-button" class="product_add_to_cart"><i class="fa fa-cart-plus" aria-hidden="true"></i></a>';
			$buynow_btn = '<form method="POST" action="#">
							<a id="disabled-button" class="product-buy-now" tabindex="0">
								Mua ngay
							</a>
						</form>';
		}

	    echo '<div class="col-xl-3 col-lg-4 col-md-6 col-xs-12">
	            <div class="th_search_product">
	                <div class="product_item">
	                    <div class = "product-img">
	                        <img src = "'.$course_image.'" alt = "" class = "img-fluid d-block mx-auto course_img" loading="lazy" decoding="async">
	                    </div>
	                    <div class = "product-info p-3">
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

	echo '</div>
	</div>';

	//panigation
	$baseurl = new moodle_url('/local/th_ecommercelib/search/th_search_product.php', array('search' => $search, 'page' => $page));
	echo $OUTPUT->paging_bar(count($list_cate_product1), $page, $itemsPerPage, $baseurl);

} else {
	echo "Không tìm thấy khóa học nào";
}

echo $OUTPUT->footer();
?>

<script>
	const inputElement = document.getElementById('live_search');
	document.getElementById('search_form').addEventListener('submit', function(event) {
		const inputValue = inputElement.value;
		
		if (inputValue.trim() === '') {
			alert('Vui lòng nhập nội dung tìm kiếm!');
			event.preventDefault(); // Ngăn chặn việc submit biểu mẫu
		}
	});
</script>