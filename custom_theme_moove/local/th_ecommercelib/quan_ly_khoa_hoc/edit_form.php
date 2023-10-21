<?php

    require_once($CFG->dirroot . '/lib/formslib.php');
    require_once $CFG->dirroot . '/lib/filelib.php';

    class th_edit_course_edit_form extends moodleform {

        function definition() {

            global $DB;

            $mform = $this->_form;
            $mform->addElement('header', 'displayinfo', get_string('filter'));

            $mform->addElement('hidden', 'id');
		    $mform->setType('id', PARAM_RAW);

            $attributes = array();
            $mform->addElement('text', 'course_name', 'Tên khóa học', $attributes);
            $mform->setType('course_name', PARAM_TEXT);
            $mform->addElement('text', 'course_shortname', 'Tên rút gọn', $attributes);
            $mform->setType('course_shortname', PARAM_TEXT);
            $mform->addElement('float', 'course_price', 'Giá tiền', $attributes);
            $mform->setType('course_price', PARAM_INT);
            $mform->addElement('float', 'course_price_sale', 'Giá tiền khuyến mãi', $attributes);
            $mform->setType('course_price_sale', PARAM_INT);

            $this->add_action_buttons(true,  get_string('submit'));
        }
        
        function validation($data, $files) {

            return array();
        }
    }
?>
