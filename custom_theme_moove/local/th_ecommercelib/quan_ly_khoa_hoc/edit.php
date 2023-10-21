<?php

require_once '../../../config.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/quan_ly_khoa_hoc/edit_form.php';
require_once $CFG->dirroot . '/lib/filelib.php';

global $DB, $OUTPUT, $PAGE, $COURSE;

$courseid = $COURSE->id;
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
	print_error('invalidcourse', 'block_th_bulk_override', $courseid);
}

$id = optional_param('id', 0, PARAM_INT);
$delete    = optional_param('delete', 0, PARAM_BOOL);
$confirm   = optional_param('confirm', 0, PARAM_BOOL);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

require_login($courseid);
require_capability('local/th_ecommercelib:view', context_course::instance($courseid));

$pageurl = '/local/th_ecommercelib/quan_ly_khoa_hoc/edit.php';
$title   = 'Chỉnh sửa khóa học';
$PAGE->set_url('/local/th_ecommercelib/quan_ly_khoa_hoc/edit.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($title);
$PAGE->set_title($title);

$th_edit_course_edit_form = new th_edit_course_edit_form();

if ($id) {
    $course_edit = $DB->get_record('course', array('id' => $id));
    $data = $DB->get_record('th_ecommerce_course', array('course_id' => $id));

    $entry = new stdClass();
    $entry->id = $id;
    $entry->course_name = $course_edit->fullname;
    $entry->course_shortname = $course_edit->shortname;
    if($data){
        $entry->course_price = $data->price;
        $entry->course_price_sale = $data->price_sale;
    } else {
        $entry->course_price = '';
        $entry->course_price_sale = '';
    }
    
    $th_edit_course_edit_form->set_data($entry);
}

if ($delete) {
	$PAGE->url->param('delete', 1);
	if ($confirm and confirm_sesskey()) {
		
		redirect($CFG->wwwroot . '/local/th_ecommercelib/upload_image_resize/view.php', get_string('success'), null, \core\output\notification::NOTIFY_SUCCESS);
	}
	$strheading = get_string('delete');
	$PAGE->navbar->add($strheading);
	$PAGE->set_title($strheading);
	$PAGE->set_heading($COURSE->fullname);
	echo $OUTPUT->header();
	echo $OUTPUT->heading($strheading);
	$record  = $DB->get_record('course', array('id' => $id));
	$yesurl  = new moodle_url('/local/th_ecommercelib/upload_image_resize/edit.php', array('id' => $id, 'confirm' => 1, 'delete' => 1, 'sesskey' => sesskey()));
	$message = "Bạn có chắc chắn muốn xóa ảnh khóa học " . html_writer::tag('b', $record->fullname . "($record->shortname)");
	echo $OUTPUT->confirm($message, $yesurl, new moodle_url('/local/th_ecommercelib/upload_image_resize/view.php'));
	echo $OUTPUT->footer();
	die;
}

if ($th_edit_course_edit_form->is_cancelled()) {
    $courseurl = $CFG->wwwroot . "/local/th_ecommercelib/quan_ly_khoa_hoc/view.php";
    redirect($courseurl);
} else if ($fromform = $th_edit_course_edit_form->get_data()) {

    $course_id = $fromform->id;

    $th_ecommerce_course = $DB->get_record('th_ecommerce_course', array('course_id' => $course_id));
    if($th_ecommerce_course){
        $data = new stdClass();
        $data->id = $th_ecommerce_course->id;
        $data->course_id = $course_id;
        $data->price = $fromform->course_price;
        $data->price_sale = $fromform->course_price_sale;
        $DB->update_record('th_ecommerce_course', $data);
    } else {
        $data = new stdClass();
        $data->course_id = $course_id;
        $data->price = $fromform->course_price;
        $data->price_sale = $fromform->course_price_sale;
        $DB->insert_record('th_ecommerce_course', $data, false);
    }

    redirect($CFG->wwwroot . "/local/th_ecommercelib/quan_ly_khoa_hoc/view.php", 'Chỉnh sửa khóa học thành công', null, \core\output\notification::NOTIFY_SUCCESS);
    
} else {
    // form didn't validate or this is the first display
    echo $OUTPUT->header();
    echo $OUTPUT->heading($title);
    echo "</br>";
    $th_edit_course_edit_form->display();
    echo $OUTPUT->footer();
}
