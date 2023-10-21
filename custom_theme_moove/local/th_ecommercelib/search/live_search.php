<?php

require_once '../../../config.php';
require_once($CFG->dirroot. '/local/th_ecommercelib/lib.php');
use core_course\external\course_summary_exporter;
global $DB, $COURSE;

$debug = false;

if(isset($_POST['input']) || $debug == true){

	if($debug == true){
		$search = 'a';
	} else {
		$search  = $_POST['input'];
	}

	$chelper = array(
		'sort' => array(
				'displayname' => 1
			),

		'limit' => 10,
		'offset' => 0,
		'paginationallowall' => 0,
		'summary' => 1,
		'coursecontacts' => 1,
		'customfields' => 1
	);

	$courses = array();
	if($arrcourses = core_course_category::search_courses(array('search' => $search), $chelper)){
		foreach ($arrcourses as $course) {
			$data = new stdClass();
			$data->id = $course->id;
			$data->fullname = $course->fullname;
			$data->shortname = $course->shortname;
			$courseimage = course_summary_exporter::get_course_image($data);
			if (!$courseimage) {
				$courseimage = '';
			}
			$data->courseimage = $courseimage;
			$courses[] = $data;
		}
	}

	$list_cate_product = th_ecommercelib::get_list_remote_products();
	$return_arr = array();
	if(count($courses) > 0){
		foreach ($courses as $course){
			$course_shortname = $course->shortname;
			$course_image = $course->courseimage;
			if(!$course_image){
				$course_image = $CFG->wwwroot . '/theme/th_edumy/img/logo_vmc.jpg';
			}
			$shortname = $course->shortname;
			if(isset($list_cate_product->$course_shortname)){
				$product = $list_cate_product->$shortname;
				$sale_product = number_format($product->sale_product, 0, ',', '.') . 'đ';
				$list_price = number_format($product->list_price, 0, ',', '.') . 'đ'; 
			} else {
				$sale_product = '';
				$list_price = '';
			}

			$product_name = $course->fullname;
			if(mb_strlen($product_name, 'UTF-8') > 70) {        
				$product_name = mb_substr($product_name , 0 , 70, 'UTF-8') . '...';
			}

			$return_arr[] = array("id"=> $course->id, "image"=>$course_image, "name"=> $product_name, 'list_price'=> $list_price, 'sale_product'=> $sale_product);
		}

		echo json_encode($return_arr);
	} else {
		echo json_encode($return_arr);
	}
}

?>