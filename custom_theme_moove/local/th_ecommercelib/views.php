<?php

require_once __DIR__ . '/../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';
require_once 'ripcord.php';

th_ecommercelib::init();

$list_cate_product = th_ecommercelib::$list_cate_product;

global $CFG;
$CFG->th_list_products = $list_cate_product;

print_object(sizeof($list_cate_product));
print_object($list_cate_product);

exit;