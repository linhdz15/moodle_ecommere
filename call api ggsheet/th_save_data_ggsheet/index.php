<?php

require_once '../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->libdir . '/csvlib.class.php';
require_once $CFG->dirroot . '/local/th_save_data_ggsheet/lib.php';
require 'classes/vendor/autoload.php';

global $DB, $CFG, $COURSE, $USER;

if (!$course = $DB->get_record('course', array('id' => $COURSE->id))) {
    print_error('invalidcourse', 'local_th_save_data_ggsheet', $COURSE->id);
}
require_login($COURSE->id);
require_capability('local/th_save_data_ggsheet:view', context_course::instance($COURSE->id));

$PAGE->set_url(new moodle_url('/local/th_save_data_ggsheet/index.php'));
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Auto show course');
$PAGE->set_title('Auto show course');
$editurl = new moodle_url('/local/th_save_data_ggsheet/index.php');
$PAGE->settingsnav->add('Auto show course', $editurl);

$client = new Google_Client();
$client->setAuthConfig('classes/ggauth.json');
$client->addScope(Google_Service_Sheets::SPREADSHEETS);

$service = new Google_Service_Sheets($client);

$spreadsheetId = '1kODP4agvC6Xs66IauMxRWEIDsIzIHxIISmCTi6acHk0';

$range = 'A1'; // Địa chỉ ô cần ghi dữ liệu

$values = [
    ['Data 1', 'Data 2', 'Data 3'], // Dữ liệu bạn muốn lưu
];

$body = new Google_Service_Sheets_ValueRange([
    'values' => $values
]);

$params = [
    'valueInputOption' => 'RAW'
];

$result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
