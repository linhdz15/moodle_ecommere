<?php

defined('MOODLE_INTERNAL') || die();

class block_th_cocoon_event_vmc_edit_form extends block_edit_form {

	protected function specific_definition($mform) {
		global $CFG, $DB;

		for ($i = 1; $i < 5; ++$i) {
			$mform->addElement('header', 'configheader' . $i, 'Sự kiện ' . $i);

			$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
				'subdirs' => 0,
				'maxfiles' => 1,
				'accepted_types' => array('.jpg', '.png', '.gif'));

			$mform->addElement('filemanager', 'config_file_event' . $i, 'Ảnh sự kiện ' . $i, null, $filemanageroptions);

			$mform->addElement('text', 'config_name_event' . $i, 'Tên sự kiện ' . $i);
			$mform->setType('config_name_event' . $i, PARAM_TEXT);

			$mform->addElement('text', 'config_url_event' . $i, 'Link sự kiện ' . $i);
			$mform->setType('config_url_event' . $i, PARAM_TEXT);

			$mform->addElement('date_selector', 'config_date_event' . $i, 'Thời gian sự kiện ' . $i);
        	$mform->setType('config_date_event' . $i, PARAM_INT);
		}

		$mform->addElement('header', 'configheader_allevents', 'Link tất cả sự kiện');
		$mform->addElement('text', 'config_all_events', 'Link');
		$mform->setType('config_all_events', PARAM_TEXT);
	}

	function set_data($defaults) {
		if (!empty($this->block->config) && is_object($this->block->config)) {

			for ($i = 1; $i < 5; $i++) {
				$field = 'file_event' . $i;
				$conffield = 'config_file_event' . $i;
				$draftitemid = file_get_submitted_draft_itemid($conffield);

				file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_th_cocoon_event_vmc', 'image_event', $i, array('subdirs' => false));
				$defaults->$conffield['itemid'] = $draftitemid;
				$this->block->config->$field = $draftitemid;
			}
		}

		parent::set_data($defaults);
	}
}