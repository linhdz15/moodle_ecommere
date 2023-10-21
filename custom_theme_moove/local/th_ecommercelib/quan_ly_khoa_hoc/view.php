<?php

require_once '../../../config.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/upload_image_resize/edit_form.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';

global $DB, $OUTPUT, $PAGE, $COURSE;

$courseid = $COURSE->id;
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
	print_error('invalidcourse', 'block_th_bulk_override', $courseid);
}

require_login($courseid);

$pageurl = '/local/th_ecommercelib/upload_image_resize/view.php';
$title   = 'Danh sách khóa học';
$PAGE->set_url('/local/th_ecommercelib/upload_image_resize/view.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($title);
$PAGE->set_title($title);
  
echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo "</br>";

$list_course = $DB->get_records_sql('SELECT * FROM {course} WHERE NOT id = 1');
$component = 'local_th_ecommercelib';
$filearea = 'image_resize';
$fs = get_file_storage();

$table = new html_table();
$table->head = array('Stt', 'Tên khóa học', 'Tên rút gọn khóa học', 'Thao tác');
$stt = 0;

foreach($list_course as $course){
    $course_id = $course->id;
    $context = context_course::instance($course_id, MUST_EXIST);
    $contextid = $context->id;

    $link_edit = new moodle_url('/local/th_ecommercelib/quan_ly_khoa_hoc/edit.php', ['id' => $course_id]);
	$edit = html_writer::link($link_edit, $OUTPUT->pix_icon('t/edit', get_string('edit')), array('title' => get_string('edit')));

    $baseurl = new moodle_url('/local/th_ecommercelib/quan_ly_khoa_hoc/view.php');

    $stt = $stt + 1;
    $row = new html_table_row();
	$cell = new html_table_cell($stt);
	$row->cells[] = $cell;

    $link_course = new moodle_url('/course/view.php', ['id' => $course_id]);
	$course_name = html_writer::link($link_course, $course->fullname);

    $cell = new html_table_cell($course_name);
	$row->cells[] = $cell;
    $cell = new html_table_cell($course->shortname);
    $row->cells[] = $cell;

    $cell = new html_table_cell($edit);
    $row->cells[] = $cell;
    $table->data[] = $row;
}

$table->attributes = array('class' => 'th_image_resize_course_table', 'border' => '1');
$table->attributes['style'] = "width: 100%; text-align:center;";
$html = html_writer::table($table);
echo $html;

$lang = current_language();
$PAGE->requires->js_call_amd('local_thlib/main', 'init', array('.th_image_resize_course_table', 'image resize', $lang));
echo $OUTPUT->footer();

