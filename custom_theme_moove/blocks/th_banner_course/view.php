<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/mathslib.php';

global $DB, $OUTPUT, $PAGE, $COURSE, $USER;

// Check for all required variables.
$courseid = $COURSE->id;
$returnto = optional_param('returnto', 'course', PARAM_ALPHANUM); // Generic navigation return page switch.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
	print_error('invalidcourse', 'block_th_banner_course', $courseid);
}

require_login($courseid);
require_capability('block/th_banner_course:view', context_course::instance($COURSE->id));

$pageurl = '/blocks/th_banner_course/view.php';
$title = get_string('title', 'block_th_banner_course');
$PAGE->set_url('/blocks/th_banner_course/view.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('th_banner_course', 'block_th_banner_course'));
$PAGE->set_title($SITE->fullname . ': ' . get_string('title', 'block_th_banner_course'));

$editurl = new moodle_url('/blocks/th_banner_course/view.php');
$settingsnode = $PAGE->navbar->add(get_string('breadcrumb', 'block_th_banner_course'), $editurl);
$settingsnode->make_active();

$listcourses = $DB->get_records_sql("SELECT * FROM {course} WHERE NOT id = 1");

$table = new html_table();
$table->head = array('STT', 'Fullname', 'Shortname', 'Công thức');
$stt = 0;

foreach ($listcourses as $k => $course) {
	$course_id = $course->id;

	$id = $DB->get_field_sql("SELECT id FROM {grade_items} WHERE calculation LIKE '%0.1%' AND calculation LIKE '%0.3%' AND calculation LIKE '%0.6%' AND courseid = '$course_id'");

	if (!empty($id)) {
		$gpr = new grade_plugin_return();
		$grade_item = grade_item::fetch(array('id'=>$id, 'courseid'=>$course->id));

		$calculation = calc_formula::localize($grade_item->calculation);
		$calculation = grade_item::denormalize_formula($calculation, $grade_item->courseid);

		if ($calculation == '=sum([[cc]]*0,1;[[kt]]*0,3;[[ddk]]*0,6)') {
			$stt = $stt + 1;
			$row = new html_table_row();
			$cell = new html_table_cell($stt);
			$row->cells[] = $cell;
			$cell = new html_table_cell($course->fullname);
			$row->cells[] = $cell;
			$cell = new html_table_cell($course->shortname);
			$row->cells[] = $cell;
			$cell = new html_table_cell($calculation);
			$row->cells[] = $cell;
			$table->data[] = $row;
		}
	}
}

$html = html_writer::table($table);

echo $OUTPUT->header();
echo "<center><h4>DANH SÁCH MÔN HỌC CHỨA CÔNG THỨC</h4></center>";
echo $html;
echo $OUTPUT->footer();

?>

