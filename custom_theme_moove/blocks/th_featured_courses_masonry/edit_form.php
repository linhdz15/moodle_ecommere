<?php

defined('MOODLE_INTERNAL') || die();

class block_th_featured_courses_masonry_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG, $DB;
        

        $list_categories = [];
     
        $list_cate = $DB->get_records_sql("SELECT * FROM {course_categories} WHERE visible = 1");

        foreach($list_cate as $cate){
            $list_categories[$cate->id] = $cate->name;
        }
        
        
        $mform->addElement('header', 'configheader1', 'Danh mục');        
        $select = $mform->addElement('select', 'config_categories', 'Chọn danh mục', $list_categories);
 
    }
    
}