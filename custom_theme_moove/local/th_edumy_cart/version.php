<?php

defined('MOODLE_INTERNAL') || die();

$plugin->component    = 'local_th_edumy_cart';
$plugin->version      = 2023101300; // YYYYMMDDXX (year, month, day, 24-hr time)
$plugin->dependencies = array(
    'local_thlib' => '2021100000',
);
$plugin->release   = 'v3.10-r1';
$plugin->supported = [310, 310];
$plugin->maturity  = MATURITY_STABLE;
