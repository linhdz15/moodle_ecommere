<?php

class block_th_cocoon_course_categories_banner extends block_base {
	public function init() {
		$this->title = 'block_th_cocoon_course_categories_banner';
	}

	function instance_allow_multiple() {
        return false;
    }

	public function get_content() {
		global $CFG, $PAGE, $DB;
		require_once($CFG->libdir . '/filelib.php');
	
		if ($this->content !== null) {
			return $this->content;
		}
		$this->content = new stdClass;
		global $COURSE;
		$context = context_course::instance($COURSE->id);

		$this->content->footer = '<div class= "cousre_categori_banner">
			<div class="row">
				<div class="cousre_categori col-xl-4">
				<ul>';

		$data = $this->config;
		$list_cate = $data->categories;

		echo '<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous">';

		$this->content->footer .= '
			<li>
				<a href="'.$CFG->wwwroot.'/blocks/th_cocoon_course_categories_banner/th_all_course.php"><span><i class="fas fa-books"></i>Tất cả khóa học</span></i></i></a>
			</li>
			<li>
				<a href="#combo_course"><span><i class="fab fa-leanpub"></i>Khóa học combo</span></i></i></a>
			</li>';
		$i = 0;
		foreach($list_cate as $cate){
			$cate = $DB->get_record('course_categories', array('id' => $cate));
			$i = $i + 1;
			$icon_html = 'icon_html' . $i;
			$this->content->footer .= '
			<li>
				<a href="'.$CFG->wwwroot.'/course/index.php?categoryid='.$cate->id.'"><span>'.$data->$icon_html.''.$cate->name.'</span><i class="fas fa-caret-right"></i></i></a>
			</li>';
		}

		
		$fs = get_file_storage();

		for($i=1;$i<=3;$i++){
			$sliderimage = 'file_slide' . $i;
			$files = $fs->get_area_files($this->context->id, 'block_th_cocoon_course_categories_banner', 'slides', $i, 'sortorder DESC, id ASC', false, 0, 0, 1);

			if (!empty($data->$sliderimage) && count($files) >= 1) {
	          $mainfile = reset($files);
	          $mainfile = $mainfile->get_filename();
	          $data->$sliderimage = moodle_url::make_pluginfile_url($this->context->id, 'block_th_cocoon_course_categories_banner', 'slides', $i, '/', $mainfile, false)->out();
	        } else {
	          $data->$sliderimage = $CFG->wwwroot .'/theme/edumy/images/home/1.jpg';
	        }
		}

		for($i=1;$i<=3;$i++){
			$sliderimage = 'file_banner' . $i;
			$files = $fs->get_area_files($this->context->id, 'block_th_cocoon_course_categories_banner', 'banner', $i, 'sortorder DESC, id ASC', false, 0, 0, 1);

			if (!empty($data->$sliderimage) && count($files) >= 1) {

		        $mainfile = reset($files);
		        $mainfile = $mainfile->get_filename();
		        $data->$sliderimage = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/{$this->context->id}/block_th_cocoon_course_categories_banner/banner/" . $i . '/' . $mainfile);
	        } else {
	          $data->$sliderimage = $CFG->wwwroot .'/theme/edumy/images/home/1.jpg';
	        }
		}

		$this->content->footer .= '
				</ul>	
				</div>
				<div class="cousre_banner col-xl-8 col-lg-12">
					<div class="th_slider">
						<div id="carouselExampleIndicators" class="slider">
							<div class="slide active">
								<img src="'.$data->file_slide1.'" alt="">                                        
							</div>
							<div class="slide">
								<img src="'.$data->file_slide2.'" alt="">
							</div>
							<div class="slide">
								<img src="'.$data->file_slide3.'" alt="">                                        
							</div>					
							<div class="navigation">
								<i class="fas fa-chevron-left prev-btn"></i>
								<i class="fas fa-chevron-right next-btn"></i>
							</div>
						</div>
					</div>

					<div class="course_banner_img row">
						<div class="banner_img col-lg-4 col-md-4 col-sm-4 col-4">
              				<a href="'.$data->link_banner1.'"><img src="'.$data->file_banner1.'" class="d-block w-100" alt="..."></a>
						</div>
						<div class="banner_img col-lg-4 col-md-4 col-sm-4 col-4">
              				<a href="'.$data->link_banner2.'"><img src="'.$data->file_banner2.'" class="d-block w-100" alt="..."></a>
						</div>
						<div class="banner_img col-lg-4 col-md-4 col-sm-4 col-4">
              				<a href="'.$data->link_banner3.'"><img src="'.$data->file_banner3.'" class="d-block w-100" alt="..."></a>
						</div>
					</div>
				</div>
			</div>
  		</div>';
		
		$PAGE->requires->js_call_amd('block_th_cocoon_course_categories_banner/main', 'main', array());
		return $this->content;
	}

	/**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $CFG;

        $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
                                    'subdirs'       => 0,
                                    'maxfiles'      => 1,
                                    'accepted_types' => array('.jpg', '.png', '.gif'));

        for($i = 1; $i <= 3; $i++) {
            $field = 'file_slide' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            file_save_draft_area_files($data->$field, $this->context->id, 'block_th_cocoon_course_categories_banner', 'slides', $i, $filemanageroptions);
        }

        for($i = 1; $i <= 3; $i++) {
            $field = 'file_banner' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            file_save_draft_area_files($data->$field, $this->context->id, 'block_th_cocoon_course_categories_banner', 'banner', $i, $filemanageroptions);
        }

        parent::instance_config_save($data, $nolongerused);
    }

    /**
     * When a block instance is deleted.
     */
    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_th_cocoon_course_categories_banner');
        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {
        global $CFG;

        $fromcontext = context_block::instance($fromid);
        $fs = get_file_storage();

        if (!empty($this->config) && is_object($this->config)) {
            $data = $this->config;
            $data->slidesnumber = is_numeric($data->slidesnumber) ? (int)$data->slidesnumber : 0;
        } else {
            $data = new stdClass();
            $data->slidesnumber = 0;
        }

        $filemanageroptions = array('maxbytes'      => $CFG->maxbytes,
                                    'subdirs'       => 0,
                                    'maxfiles'      => 1,
                                    'accepted_types' => array('.jpg', '.png', '.gif'));

        for($i = 1; $i <= 3; $i++) {
            $field = 'file_slide' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
            if (!$fs->is_area_empty($fromcontext->id, 'block_th_cocoon_course_categories_banner', 'slides', $i, false)) {
                $draftitemid = 0;
                file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_th_cocoon_course_categories_banner', 'slides', $i, $filemanageroptions);
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_th_cocoon_course_categories_banner', 'slides', $i, $filemanageroptions);
            }
        }

        for($i = 1; $i <= 3; $i++) {
            $field = 'file_banner' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
            if (!$fs->is_area_empty($fromcontext->id, 'block_th_cocoon_course_categories_banner', 'banner', $i, false)) {
                $draftitemid = 0;
                file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_th_cocoon_course_categories_banner', 'banner', $i, $filemanageroptions);
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_th_cocoon_course_categories_banner', 'banner', $i, $filemanageroptions);
            }
        }

        return true;
    }
}
?>



