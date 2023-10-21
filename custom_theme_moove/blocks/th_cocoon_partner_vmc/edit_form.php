<?php

defined('MOODLE_INTERNAL') || die();

class block_th_cocoon_partner_vmc_edit_form extends block_edit_form {

	protected function specific_definition($mform) {
		global $CFG;

		for ($i = 1; $i <= 10; ++$i) {
			$mform->addElement('header', 'configheader' . $i, 'Đồng hành cùng VMC ' . $i);

			$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
				'subdirs' => 0,
				'maxfiles' => 1,
				'accepted_types' => array('.jpg', '.png', '.gif'));

			$mform->addElement('filemanager', 'config_file_partner' . $i, 'Ảnh đối tác ' . $i, null, $filemanageroptions);
		}

		$mform->addElement('header', 'configheader_partner_event', 'Tổ chức sự kiện');
		$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
			'subdirs' => 0,
			'maxfiles' => 1,
			'accepted_types' => array('.jpg', '.png', '.gif'));

		$mform->addElement('filemanager', 'config_file_partner_event', 'Ảnh banner', null, $filemanageroptions);
		$mform->addElement('text', 'config_url_partner_event', 'Link');
		$mform->setType('config_url_partner_event', PARAM_TEXT);
		$mform->addElement('textarea', 'config_content_partner_event', 'Nội dung');

		$content_html = '<p style="text-align:center;">Tham khảo thông tin về các sự kiện trung tâm đã tổ chức tại: <a href="https://bit.ly/3NR4p57">https://bit.ly/3NR4p57</a></p>
						<p style="text-align:center;">Doanh nghiệp có nhu cầu phối hợp tổ chức vui lòng để lại thông tin tại form: <a href="https://sum.vn/zSpfFl">https://sum.vn/zSpfFl</a></p>
						<p style="text-align:center;"><strong><em>Hoặc liên hệ trực tiếp qua hotline: 0966.000.643</em></strong></p>';
		$mform->setDefault('config_content_partner_event', $content_html);
		$mform->setType('config_content_partner_event', PARAM_CLEANHTML);
	}

	function set_data($defaults) {
		if (!empty($this->block->config) && is_object($this->block->config)) {

			for ($i = 1; $i <= 10; $i++) {
				$field = 'file_partner' . $i;
				$conffield = 'config_file_partner' . $i;
				$draftitemid = file_get_submitted_draft_itemid($conffield);

				file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_th_cocoon_partner_vmc', 'image_partner', $i, array('subdirs' => false));
				$defaults->$conffield['itemid'] = $draftitemid;
				$this->block->config->$field = $draftitemid;
			}

			$field = 'file_partner_event';
			$conffield = 'config_file_partner_event';
			$draftitemid = file_get_submitted_draft_itemid($conffield);

			file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_th_cocoon_partner_vmc', 'image_partner_event', 1, array('subdirs' => false));
			$defaults->$conffield['itemid'] = $draftitemid;
			$this->block->config->$field = $draftitemid;
		}

		parent::set_data($defaults);
	}
}