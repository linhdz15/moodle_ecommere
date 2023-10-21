<?php

$services = array(
    'th_ecommercelib' => array( //the name of the web service
        'functions' => array('th_update_course'), //web service functions of this service
        'requiredcapability' => 0, //if set, the web service user need this capability to access
        //any function of this service. For example: 'some/capability:specified'
        'restrictedusers' => 0, //if enabled, the Moodle administrator must link some user to this service
        //into the administration
        'enabled' => 1, //if enabled, the service can be reachable on a default installation
        'shortname' => 'th_ecommerce', //the short name used to refer to this service from elsewhere including when fetching a token
    ),
);

$functions = array(
    'th_update_course' => array(
        'classname' => 'local_th_ecommercelib_external',
        'methodname' => 'update_course',
        'classpath' => 'local/th_ecommercelib/classes/external.php',
        'description' => 'update info course',
        'type' => 'write',
    ),
);