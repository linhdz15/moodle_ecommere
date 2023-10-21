<?php
// This file is part of local_th_ecommercelib for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings page
 *
 * @package       local_th_ecommercelib
 * @author        Andreas Hruska (andreas.hruska@tuwien.ac.at)
 * @author        Katarzyna Potocka (katarzyna.potocka@tuwien.ac.at)
 * @author        Simeon Naydenov (moniNaydenov@gmail.com)
 * @copyright     2014 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig/*$ADMIN->fulltree*/) {

	$settings = new admin_settingpage('local_th_ecommercelib', get_string('pluginname', 'local_th_ecommercelib'));
	$ADMIN->add('localplugins', $settings);

	$configs = array();

	// $values = array();
	// $values[] = get_string('lastname');
	// $values[] = get_string('firstname');

	$configs[] = new admin_setting_configtext('local_th_ecommercelib/url',
		'https://IP:PORT',
		'IP:PORT',
		'http://192.168.1.31:8069', PARAM_TEXT);

	$configs[] = new admin_setting_configtext('local_th_ecommercelib/db',
		'Database Name',
		'database name',
		'ecommerce', PARAM_TEXT);

	$configs[] = new admin_setting_configtext('local_th_ecommercelib/username',
		'username',
		'username',
		'long', PARAM_TEXT);

	// $configs[] = new admin_setting_configtext('local_th_ecommercelib/password',
	// 	'password',
	// 	'password',
	// 	'1', PARAM_TEXT);

	$configs[] = new admin_setting_configtext('local_th_ecommercelib/token',
		'token',
		'Elearning Ecommerce Key',
		'', PARAM_TEXT, 50);

	$configs[] = new admin_setting_configtext('local_th_ecommercelib/pricelistid',
		'pricelistid',
		'pricelist id',
		2, PARAM_INT);

	$configs[] = new admin_setting_configtext('local_th_ecommercelib/maxkilobyte_image_resize',
		'maxkilobyte_image_resize',
		'Dung lượng ảnh được phép tải lên. Đơn vị kilobyte (KB)',
		50, PARAM_INT);

	$configs[] = new admin_setting_configtext('local_th_ecommercelib/max_width_image',
	'max_width_image',
	'Chiều rộng tối đa của ảnh, Đơn vị pixel (px)',
	300, PARAM_INT);

	$configs[] = new admin_setting_configtext('local_th_ecommercelib/max_height_image',
		'max_height_image',
		'Chiều dài tối đa của ảnh, Đơn vị pixel (px)',
		300, PARAM_INT);

	foreach ($configs as $config) {
		$config->plugin = 'local_th_ecommercelib';
		$settings->add($config);
	}

}
