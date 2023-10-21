<?php
require_once $CFG->dirroot . '/config.php';
require_once $CFG->dirroot . '/local/th_ecommercelib//lib.php';

use core_course\external\course_summary_exporter;

class block_th_featured_courses_masonry extends block_base {
	public function init() {
		$this->title = 'block_th_featured_courses_masonry';
	}

	function instance_allow_multiple() {
		return true;
	}

	public function get_content() {
		$instanceid = $this->instance->id;

		global $CFG, $DB, $OUTPUT, $PAGE;
		if ($this->content !== null) {
			return $this->content;
		}
		$this->content = new stdClass;

		$categories_id = $this->config->categories;
		if (!empty($categories_id)) {
			$course_all = $DB->get_records_sql("SELECT c.*, e.price, e.price_sale FROM {course} as c
				LEFT JOIN {th_ecommerce_course} as e ON c.id = e.course_id
				WHERE c.category = $categories_id");
		} else {
			$course_all = new stdClass();
		}

		$cate = $DB->get_record('course_categories', array('id' => $categories_id));

		$renderer = $PAGE->get_renderer('block_th_featured_courses_masonry');

		$this->content->text = '
		<div class="path-block_th_featured_courses_masonry">
			<a href="' . $CFG->wwwroot . '/course/index.php?categoryid=' . $categories_id . '' . '"><h2 class="title-cate">Khóa Học ' . $cate->name . "</h2></a>
			<div class = 'row g-4 my-5 mx-auto items-slider' id='items-slider$instanceid' >";
		foreach ($course_all as $product_short_name) {

			$roleid = $DB->get_field_sql("SELECT id FROM {role} WHERE shortname = 'editingteacher'");
			$giang_vien = $DB->get_record_sql("SELECT u.* FROM {enrol} as e, {user_enrolments} as ue, {user} as u, {context} as c, {role_assignments} as ra 
				WHERE ue.status = 0 AND e.courseid = '$product_short_name->id' AND e.enrol = 'manual' AND e.id = ue.enrolid AND u.id = ue.userid 
				AND c.instanceid = '$product_short_name->id' AND c.contextlevel = '50' AND c.id = ra.contextid AND ra.userid = u.id AND ra.roleid = '$roleid'");
			$course = $DB->get_record('course', array('id' => $product_short_name->id));
			if ($course) {
				$course_image = course_summary_exporter::get_course_image($course);
			} else {
				$course_image = '<img src="' .$CFG->wwwroot.'/theme/moove/img/logo_vmc.jpg">';
			}

			if ($giang_vien) {
				$user_picture = $OUTPUT->user_picture($giang_vien);
				$teacher_fullname = $giang_vien->firstname . ' ' . $giang_vien->lastname;
			} else {
				$user_picture = '<img src="' .$CFG->wwwroot.'/theme/moove/img/logo_vmc.jpg">';
				$teacher_fullname = 'VMC Việt Nam ';
			}
			
			$product_name = $product_short_name->fullname;

			if($product_short_name->price_sale){
				$product_price_sale = number_format($product_short_name->price_sale, 0, ',', '.') . 'đ';
			} else {
				$product_price_sale = '';
			}

			if($product_short_name->price){
				$product_price_full = number_format($product_short_name->price, 0, ',', '.') . 'đ';
			} else {
				$product_price_full = '';
			}

			if (mb_strlen($product_name, 'UTF-8') > 80) {
				$product_name = mb_substr($product_name, 0, 80, 'UTF-8') . '...';
			}

			$btn_id = "th_btn_themvaogiohang_{$instanceid}_{$course->id}";
			$data = [
				'btnid' => $btn_id,
				'courseid' => $course->id
			];

			$add_to_cart = $renderer->render_from_template('local_th_edumy_cart/add_to_cart_btn', $data);
			$buynow_btn = $renderer->render_from_template('local_th_edumy_cart/buynow_btn', $data);

			$this->content->text .= '
				<div class = "col item mx-auto">
					<div class="product_item">
						<div class = "product-img">
							<img src = "' . $course_image . '" alt = "" class = "img-fluid d-block mx-auto course_img">
						</div>

						<div class = "product-info">
							<a href = "' . $CFG->wwwroot . "/course/view.php?id=$product_short_name->id" . '" class = "d-block text-decoration-none py-2 product-name">' . $product_name . '</a>
							<div class="teacher-course">
								' . $user_picture . '
								<span>' . $teacher_fullname . '</span>
							</div>
							<div class="product_buy">'
							. $buynow_btn
							. $add_to_cart . '
							</div>
							<div class = "price_course">
								<div class ="product_price">
									<span class = "product-sale-price-new">' . $product_price_sale . '</span>
									<span class = "product-price-full">' . $product_price_full . '</span>
								</div>
							</div>
						</div>
					</div>
				</div>';
			
		};

		$this->content->text .= '</div>
			</div>';

		$options = '{
			    "infinite":false,
			    "slidesToShow":4,
			    "slidesToScroll":2,
			    "prevArrow": null,
  			    "nextArrow": null,
			    "responsive":[
			      {
			         "breakpoint":1199,
			         "settings":{
			            "slidesToShow":3,
			            "slidesToScroll":1
			         }
			      },
			      {
			         "breakpoint":1024,
			         "settings":{
			            "slidesToShow":2,
			            "slidesToScroll":1
			         }
			      },
				  {
					"breakpoint":768,
					"settings":{
					   "slidesToShow":2,
					   "slidesToScroll":1
					}
				 },
			      {
			         "breakpoint":767,
			         "settings":{
			            "slidesToShow":1,
			            "slidesToScroll":1,
			            "arrows":false
			         }
			      }
			   ]
			}';

		$PAGE->requires->js_call_amd('local_th_ecommercelib/main', 'makeSlider', array("#items-slider$instanceid", $options));

		return $this->content;
	}
}
?>
