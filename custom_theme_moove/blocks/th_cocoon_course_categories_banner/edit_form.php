<?php

defined('MOODLE_INTERNAL') || die();

class block_th_cocoon_course_categories_banner_edit_form extends block_edit_form {

	protected function specific_definition($mform) {
		global $CFG, $DB;

		//Config list categories
		echo '<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous">';

		$options = [];
		for ($i = 1; $i <= 20; $i++) {
			$options[$i] = $i;
		}

		$list_cate = $DB->get_records_sql("SELECT * FROM {course_categories} WHERE visible = 1");

		$list_categories = [];
		foreach ($list_cate as $cate) {
			$list_categories[$cate->id] = $cate->name;
		}

		$mform->addElement('header', 'configheader1', 'Danh mục');
		$mform->addElement('select', 'config_max_categories', 'Số danh mục hiển thị', $options);
		$mform->setDefault('config_max_categories', 10);
		$mform->addElement('autocomplete', 'config_categories', 'Chọn danh mục', $list_categories, [
			'multiple' => true,
		]);
		$mform->addRule('config_categories', null, 'required');

		//Config slider

		$mform->addElement('header', 'configheader1', 'Slide 1');

		$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
			'subdirs' => 0,
			'maxfiles' => 1,
			'accepted_types' => array('.jpg', '.png', '.gif'));

		$f = $mform->addElement('filemanager', 'config_file_slide1', 'Ảnh Slide 1', null, $filemanageroptions);

		$mform->addElement('header', 'configheader2', 'Slide 2');

		$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
			'subdirs' => 0,
			'maxfiles' => 1,
			'accepted_types' => array('.jpg', '.png', '.gif'));

		$f = $mform->addElement('filemanager', 'config_file_slide2', 'Ảnh Slide 2', null, $filemanageroptions);

		$mform->addElement('header', 'configheader3', 'Slide 3');

		$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
			'subdirs' => 0,
			'maxfiles' => 1,
			'accepted_types' => array('.jpg', '.png', '.gif'));

		$f = $mform->addElement('filemanager', 'config_file_slide3', 'Ảnh Slide 3', null, $filemanageroptions);

		//Config banner

		$mform->addElement('header', 'configbanner1', 'Banner 1');

		$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
			'subdirs' => 0,
			'maxfiles' => 1,
			'accepted_types' => array('.jpg', '.png', '.gif'));

		$f = $mform->addElement('filemanager', 'config_file_banner1', 'Ảnh Banner 1', null, $filemanageroptions);

		$mform->addElement('text', 'config_link_banner1', 'Link banner 1');
		$mform->setType('config_link_banner1', PARAM_URL);

		$mform->addElement('header', 'configbanner2', 'Banner 2');

		$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
			'subdirs' => 0,
			'maxfiles' => 1,
			'accepted_types' => array('.jpg', '.png', '.gif'));

		$f = $mform->addElement('filemanager', 'config_file_banner2', 'Ảnh Banner 2', null, $filemanageroptions);

		$mform->addElement('text', 'config_link_banner2', 'Link banner 2');
		$mform->setType('config_link_banner2', PARAM_URL);

		$mform->addElement('header', 'configbanner3', 'Banner 3');

		$filemanageroptions = array('maxbytes' => $CFG->maxbytes,
			'subdirs' => 0,
			'maxfiles' => 1,
			'accepted_types' => array('.jpg', '.png', '.gif'));

		$f = $mform->addElement('filemanager', 'config_file_banner3', 'Ảnh Banner 3', null, $filemanageroptions);

		$mform->addElement('text', 'config_link_banner3', 'Link banner 3');
		$mform->setType('config_link_banner3', PARAM_URL);

		//config icon

		$list_cate = $DB->get_records_sql("SELECT * FROM {course_categories} WHERE visible = 1");
		$sl_cate = count($list_cate);

		$mform->addElement('header', 'config_icon', 'Icon');

		$mform->addElement('html', '<b>ICON THAM KHẢO:</b>');
		$mform->addElement('html', '<div><i class="fas fa-child"></i><span>&ensp;&lt;i class=&quot;fas fa-child&quot;&gt;&lt;/i&gt;</span></div>');
		$mform->addElement('html', '<div><i class="fal fa-briefcase"></i><span>&ensp;&lt;i class=&quot;fal fa-briefcase&quot;&gt;&lt;/i&gt;</span></div>');
		$mform->addElement('html', '<div><i class="fal fa-brain"></i><span>&ensp;&lt;i class=&quot;fal fa-brain&quot;&gt;&lt;/i&gt;</span></div>');
		$mform->addElement('html', '<div><i class="fal fa-head-side-medical"></i><span>&ensp;&lt;i class=&quot;fal fa-head-side-medical&quot;&gt;&lt;/i&gt;</span></div>');
		$mform->addElement('html', '<div><i class="fal fa-hand-holding-heart"></i><span>&ensp;&lt;i class=&quot;fal fa-hand-holding-heart&quot;&gt;&lt;/i&gt;</span></div>');
		$mform->addElement('html', '<div><i class="fal fa-spa"></i><span>&ensp;&lt;i class=&quot;fal fa-spa&quot;&gt;&lt;/i&gt;</span></div>');
		$mform->addElement('html', '<div><i class="fal fa-handshake-alt"></i><span>&ensp;&lt;i class=&quot;fal fa-handshake-alt&quot;&gt;&lt;/i&gt;</span></div>');

		for ($i = 1; $i <= $sl_cate; ++$i) {
			$mform->addElement('text', 'config_icon_html' . $i, 'Icon ' . $i);
			$mform->setDefault('config_icon_html' . $i, '<i class="fas fa-child"></i>');
			$mform->setType('config_icon_html' . $i, PARAM_CLEANHTML);

		}

	}

	function validation($data, $files) {
		if (count($data['config_categories']) > $data['config_max_categories']) {
			return array('config_categories' => 'Chỉ được hiển thị tối đa ' . $data['config_max_categories'] . ' danh mục. Vui lòng chọn lại.');
		}
	}

	function set_data($defaults) {
		if (!empty($this->block->config) && is_object($this->block->config)) {

			for ($i = 1; $i <= 3; $i++) {
				$field = 'file_slide' . $i;
				$conffield = 'config_file_slide' . $i;
				$draftitemid = file_get_submitted_draft_itemid($conffield);

				file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_th_cocoon_course_categories_banner', 'slides', $i, array('subdirs' => false));
				$defaults->$conffield['itemid'] = $draftitemid;
				$this->block->config->$field = $draftitemid;
			}

			for ($i = 1; $i <= 3; $i++) {
				$field = 'file_banner' . $i;
				$conffield = 'config_file_banner' . $i;
				$draftitemid = file_get_submitted_draft_itemid($conffield);
				file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_th_cocoon_course_categories_banner', 'banner', $i, array('subdirs' => false));
				$defaults->$conffield['itemid'] = $draftitemid;
				$this->block->config->$field = $draftitemid;
			}
		}

		parent::set_data($defaults);
	}
}