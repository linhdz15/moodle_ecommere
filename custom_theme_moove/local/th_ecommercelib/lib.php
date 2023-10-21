<?php

require_once __DIR__ . '/../../config.php';
require_once $CFG->libdir . '/adminlib.php';
use core_course\external\course_summary_exporter;

class th_ecommercelib {

	static $url = "";
	static $db = "";
	static $username = "";
	static $token = "";
	static $models = null;
	static $uid = null;
	static $list_cate_product = null;

	static $instance = null;
	// static $config = get_config('local_th_ecommercelib');

	private function __construct() {
		// The expensive process (e.g.,db connection) goes here.
	}

	public static function get_instance() {
		if (self::$instance == null) {
			self::$instance = new th_ecommercelib();

			$config = get_config('local_th_ecommercelib');
			self::$url = $config->url;
			self::$db = $config->db;
			self::$username = "$config->username";
			self::$token = $config->token;

			$db = self::$db;
			$username = self::$username;
			$url = self::$url;
			$token = self::$token;

			$common = ripcord::client("$url/xmlrpc/2/common");
			self::$uid = $common->authenticate($db, $username, $token, array());
			self::$models = ripcord::client("$url/xmlrpc/2/object");
		}

		return self::$instance;
	}

	public static function init() {

		global $DB, $OUTPUT, $CFG; 

		self::get_instance();

		$config = get_config('local_th_ecommercelib');
		$id_pricelist = $config->pricelistid;

		$db = self::$db;
		$username = self::$username;
		$url = self::$url;
		$token = self::$token;
		

		$common = ripcord::client("$url/xmlrpc/2/common");
		$uid = $common->authenticate($db, $username, $token, array());

		$models = ripcord::client("$url/xmlrpc/2/object");

		
		// $product1 = $models->execute_kw($db, $uid, $token, 'product.template', 'get_all_product', array(2), array('domain'=>array(array('categ_id', '=', 13)), 'fields'=>array(), 'limit'=>0));
		
		$products = $models->execute_kw($db, $uid, $token, 'product.template', 'get_all_product', [], array('fields' => array('id', 'name', 'list_price', 'default_code', 'combo_product_id', 'image_url', 'description', 'categ_id', 'is_combo','image_1920')));
		
		if(count($products) <= 10){
			echo 'Không đọc được dữ liệu. Vui lòng kiểm tra lại kết nối';
			exit;
		}
		
		$list_product = [];

		foreach ($products as $product) {

			$course = $DB->get_record('course', array('shortname' => $product['default_code']));

			$default_code = $product['default_code'];
			$is_combo = $product['is_combo'];

			$product['cate_name'] = $product['categ_id'][1];
			$product['categ_id'] = $product['categ_id'][0];

			if ($course) {
				$product['description'] = $course->summary;
				$product['courseid'] = $course->id;
				$product['course_link'] = $CFG->wwwroot . "/course/view.php?id=$course->id";
				$product['image_url'] = course_summary_exporter::get_course_image($course);

				$list_product[$default_code] = $product;
			} 

			if ($is_combo) {
				$product['image_base64'] = $product['image_1920'];
				$combo_product = $models->execute_kw($db, $uid, $token, 'product.template', 'get_product_in_combo_list', [], array('combo_ids' => $product['combo_product_id'], 'fields' => array('id', 'name', 'list_price', 'default_code', 'combo_product_id', 'image_url'), 'limit' => 0));
				$product['combo_product_id'] = $combo_product;

				$list_product[$default_code] = $product;
			}
		}

		$records = $DB->get_records('local_th_ecommercelib');

		self::$list_cate_product = $list_product;
		$CFG->th_list_product = $list_product;

		if (count($records) == 0) {
			$record = new stdClass();
			$record->all_remote_product = json_encode($list_product);
			$DB->insert_record('local_th_ecommercelib', $record);

		} else {
			$record = new stdClass();
			$record->id = $records[1]->id;
			$record->all_remote_product = json_encode($list_product);
			$DB->update_record('local_th_ecommercelib', $record);
		}
	}

	public function tt_giangvien($course_id) {
		global $DB, $OUTPUT;
		$roleid = $DB->get_field_sql("SELECT id FROM {role} WHERE shortname = 'editingteacher'");
		$giang_vien = $DB->get_record_sql("SELECT u.* FROM {enrol} as e, {user_enrolments} as ue, {user} as u, {context} as c, {role_assignments} as ra WHERE ue.status = 0 AND e.courseid = '$course_id' AND e.enrol = 'manual' AND e.id = ue.enrolid AND u.id = ue.userid AND c.instanceid = '$course_id' AND c.contextlevel = '50' AND c.id = ra.contextid AND ra.userid = u.id AND ra.roleid = '$roleid'");

		$gv = [];
		$gv['id'] = $giang_vien->id;
		$gv['fullname'] = $giang_vien->firstname . ' ' . $giang_vien->lastname;
		$gv['description'] = $giang_vien->description;

		$user_picture = $OUTPUT->user_picture($giang_vien, array('class' => 'userpicture'));
		$gv['image'] = $user_picture;

		return $gv;
	}

	/**
	 * [Get list of remote product and cached it in $CFG global variable]
	 * @return [object] [list of remote product]
	 */
	public static function get_list_remote_products() {

		global $CFG;

		if (!isset($CFG->th_list_products)) {
			global $DB;
			$list_cate_product = $DB->get_field_sql("SELECT all_remote_product FROM {local_th_ecommercelib} WHERE id = 1");
			$list_cate_product = json_decode($list_cate_product);

			foreach ($list_cate_product as $key => $value) {
				$sale_product = $value->sale_product;
				$list_price = $value->list_price;

				$list_cate_product->$key->sale_product_format = number_format($sale_product, 0, ',', '.');
				$list_cate_product->$key->list_price_format = number_format($list_price, 0, ',', '.');
			}
			$CFG->th_list_products = $list_cate_product;
		}
		return $CFG->th_list_products;
	}


	
	public static function get_busing_history() {

		global $USER;
		self::get_instance();
		
		$config = get_config('local_th_ecommercelib');
		$db = self::$db;
		$username = self::$username;
		$url = self::$url;
		$token = self::$token;
		

		$common = ripcord::client("$url/xmlrpc/2/common");
		$uid = $common->authenticate($db, $username, $token, array());

		$models = ripcord::client("$url/xmlrpc/2/object");

		$user_id = $USER->id;
		$phone = $USER->phone2;

		$busing_history = $models->execute_kw($db, $uid, $token, 'sale.order', 'get_sale_orders', null, array('phone' => $phone));
		
		return $busing_history;
	}
}

function local_th_ecommercelib_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
	global $DB, $CFG, $USER;

	// If block is in course context, then check if user has capability to access course.
	if ($context->get_course_context(false)) {
		require_course_login($course);
	} else if ($CFG->forcelogin) {
		require_login();
	} else {
		// Get parent context and see if user have proper permission.
		$parentcontext = $context->get_parent_context();
		if ($parentcontext->contextlevel === CONTEXT_COURSECAT) {
			// Check if category is visible and user can view this category.
			$category = $DB->get_record('course_categories', array('id' => $parentcontext->instanceid), '*', MUST_EXIST);
			if (!$category->visible) {
				require_capability('moodle/category:viewhiddencategories', $parentcontext);
			}
		} else if ($parentcontext->contextlevel === CONTEXT_USER && $parentcontext->instanceid != $USER->id) {
			// The block is in the context of a user, it is only visible to the user who it belongs to.
			send_file_not_found();
		}
		// At this point there is no way to check SYSTEM context, so ignoring it.
	}

	if ($filearea !== 'image_resize') {
		send_file_not_found();
	}

	$fs = get_file_storage();

	if (count($args) != 2) {
		send_file_not_found();
	}

	if (!$file = $fs->get_file($context->id, 'local_th_ecommercelib', $filearea, $args[0], '/', $args[1]) or $file->is_directory()) {
		send_file_not_found();
	}

	// NOTE: it woudl be nice to have file revisions here, for now rely on standard file lifetime,
	//       do not lower it because the files are dispalyed very often.
	\core\session\manager::write_close();
	send_stored_file($file, null, 0, $forcedownload, $options);
}