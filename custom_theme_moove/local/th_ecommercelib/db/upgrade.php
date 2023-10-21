<?php

defined('MOODLE_INTERNAL') || die;

function xmldb_local_th_ecommercelib_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    if ($oldversion < 2023061503) {

        if (!$dbman->table_exists('local_th_ecommercelib')) {

            $table = new xmldb_table('local_th_ecommercelib');

            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('all_remote_product', XMLDB_TYPE_TEXT);

            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $dbman->create_table($table);
        }        
    }
    return true;
}
