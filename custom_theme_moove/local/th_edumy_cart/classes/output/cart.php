<?php

namespace local_th_edumy_cart\output;

require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';

use renderable;
use renderer_base;
use stdClass;
use templatable;
use th_ecommercelib;

class cart implements renderable, templatable {

	public $id = null;
	public $products = array();
	public $coupon_applied = [];
	public $order_total = [];
	public $claimable_discount_and_giftcard = [];
	public $coupon_checking = null;
	public $discount_code_checking = null;
	public $error_code = null;
	public $is_muangay = null;
	public $type_cart = null;
	public $order_comfirmation = null;
	public $user_info = null;
	public $payment_methods = null;
	public $shopping_link = null;

	public function __construct() {

	}

	public $auto_increatement_id = 0;

	public function add_product($product) {

		$product->id_cart = $this->auto_increatement_id;
		$this->products[$this->auto_increatement_id++] = $product;

	}

	public function remove_product($id) {
		if (array_key_exists($id, $this->products)) {
			unset($this->products[$id]);
			if (count($this->products) == 0) {
				$this->products = [];
			}
		}
	}

	public function add_coupon($coupon_id) {
		$this->coupon_checking = $coupon_id;
	}

	public function add_discount_code($discount_code) {
		$this->discount_code_checking = $discount_code;
	}

	public function remove_coupon($coupon_id) {
		$key_coupon = -1;
		foreach ($this->coupon_applied as $key => $value) {
			if ($coupon_id == $value['coupon_id']) {
				$key_coupon = $key;
			}
		}
		if ($key_coupon >= 0) {
			array_splice($this->coupon_applied, $key_coupon, 1);
		}
	}

	public function check_price_and_claimable_coupon() {
		$current_products = $this->products;
		$current_coupon = $this->coupon_applied;

		$req_products = [];
		$req_coupon_reward = [];
		$req_coupon_code = [];

		$flag = false;
		if ($this->coupon_checking != null) {
			$req_coupon_reward = [$this->coupon_checking];
			$flag = true;
		}

		if ($this->discount_code_checking != null) {
			$req_coupon_code[] = [$this->discount_code_checking];
			$flag = true;
		}

		if (!$flag) {
			foreach ($current_coupon as $key => $value) {
				if (array_key_exists('coupon_code', $value)) {
					$req_coupon_code[] = $value['coupon_code'];
				} else {
					$req_coupon_reward[] = $value['coupon_id'];
				}
			}
		}

		foreach ($current_products as $key => $value) {

			if (array_key_exists('is_combo', $value)) {
				$req_products[] = $value['default_code'];
			} else {
				$req_products[] = $value['shortname'];
			}
		}

		$api = th_ecommercelib::get_instance();
		$models = $api::$models;

		$request_param = array('products' => $req_products, 'coupon_reward' => $req_coupon_reward, 'coupon_code' => $req_coupon_code);

		$quotation = $models->execute_kw($api::$db, $api::$uid, $api::$token, 'sale.order', 'get_ordertotal_discount_and_gift_card', null, $request_param);

		if (array_key_exists('status', $quotation) && $quotation['status'] == 'error') {
			$this->error_code = $quotation['message'];
		} else {
			$this->error_code = null;
		}

		if ($this->error_code != null) {
			// $this->error_code = null;
		} else {
			$this->order_total = $quotation['order_total'];
			$this->claimable_discount_and_giftcard = $quotation['claimable_discount_and_giftcard'];
			$this->coupon_applied = $quotation['coupon_applied'];
		}

		$this->coupon_checking = null;
		$this->discount_code_checking = null;

		return $quotation;
	}

	public function send_comfirm_order() {
		$current_products = $this->products;
		$current_coupon = $this->coupon_applied;

		$req_products = [];
		$req_coupon_reward = [];
		$req_coupon_code = [];

		if ($this->user_info == null) {
			$response = new stdClass();
			$response->status = "Error";
			$response->message = "User info not provided";
			return $response;
		} else {
			$req_customer = (array) $this->user_info;
		}

		foreach ($current_products as $key => $value) {

			if (array_key_exists('is_combo', $value)) {
				$req_products[] = $value['default_code'];
			} else {
				$req_products[] = $value['shortname'];
			}
		}

		$api = th_ecommercelib::get_instance();
		$models = $api::$models;

		// foreach ($this->coupon_applied as $key => $value) {
		// 	$this->coupon_applied[$key]['price'] = floatval($value['price']);
		// 	print_object($this->coupon_applied[$key]['price']);
		// }
		// print_object($this->order_total);

		$request_param = array('products' => $req_products, 'coupon_applied' => $this->coupon_applied, 'order_total' => $this->order_total, 'customer' => $req_customer);

		$confirm_order = $models->execute_kw($api::$db, $api::$uid, $api::$token, 'sale.order', 'comfirm_order', null, $request_param);

		return $confirm_order;
	}

	/**
	 * Export this data so it can be used as the context for a mustache template.
	 *
	 * @return stdClass
	 */
	public function export_for_template(renderer_base $output) {
		global $CFG;

		$account_name = get_config('theme_th_edumy', 'account_name');
		$account_number = get_config('theme_th_edumy', 'account_number');
		$bank = get_config('theme_th_edumy', 'bank');
		$payment_method = get_config('theme_th_edumy', 'list_payment_method');
		$payment_method = explode( ',',  $payment_method);
		$method_text = [];
		foreach($payment_method as $method) {		
			if($method == 1) {
				$method_text[] = array('text' => 'Thanh toán bằng chuyển khoản ngân hàng', 'value' => 1);
			}
			if($method == 2) {
				$method_text[] = array('text' => 'Thanh toán bằng ONEPAY', 'value' => 2);
			}
		}
		$data = new stdClass();
		$data->products = array_values($this->products);
		$order_total = $this->order_total;
		foreach ($order_total as $key => $value) {
			$order_total[$key] = number_format($value, 0, ',', '.');
		}
		$coupon_applied = $this->coupon_applied;
		foreach ($coupon_applied as $key => $value) {
			$coupon_applied[$key]['price'] = number_format($value['price'], 0, ',', '.');
		}

		$data->order_total = $order_total;
		$data->claimable_discount_and_giftcard = $this->claimable_discount_and_giftcard;
		$data->coupon_applied = $coupon_applied;
		$data->is_muangay = $this->is_muangay;
		$data->type_cart = $this->type_cart;
		$data->show_custominfo = false;
		$data->order_comfirmation = $this->order_comfirmation;
		$data->account_name = $account_name;
		$data->account_number = $account_number;
		$data->bank = $bank;
		$data->user_info = $this->user_info;
		$data->payment_methods = $method_text;

		if ($this->error_code != null) {
			$data->error_code = $this->error_code;
		}
		// print_object($data);
		$data->shopping_link = $this->shopping_link = "$CFG->wwwroot/?redirect=0";
		// print_object($data);
		// exit;
		return $data;
	}
}