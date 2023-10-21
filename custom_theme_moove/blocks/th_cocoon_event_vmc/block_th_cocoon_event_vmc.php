<?php

class block_th_cocoon_event_vmc extends block_base {
	public function init() {
		$this->title = 'block_th_cocoon_event_vmc';
	}

    public function hide_header()
    {
        return true;
    }

	public function get_content() {
		global $CFG,$DB;
		if ($this->content !== null) {
			return $this->content;
		}
		$this->content = new stdClass;
		$list_cate_product = $DB->get_field_sql("SELECT all_remote_product FROM {local_th_ecommercelib} WHERE id = 1");  
		$list_cate_product = json_decode($list_cate_product);

		$data = $this->config;

		$fs = get_file_storage();
		for($i=1;$i<5;$i++){
			$sliderimage = 'file_event' . $i;
			$files = $fs->get_area_files($this->context->id, 'block_th_cocoon_event_vmc', 'image_event', $i, 'sortorder DESC, id ASC', false, 0, 0, 1);

			if (!empty($data->$sliderimage) && count($files) >= 1) {
	          $mainfile = reset($files);
	          $mainfile = $mainfile->get_filename();
	          $data->$sliderimage = moodle_url::make_pluginfile_url($this->context->id, 'block_th_cocoon_event_vmc', 'image_event', $i, '/', $mainfile, false)->out();
	        } else {
	          $data->$sliderimage = $CFG->wwwroot .'/theme/edumy/images/home/1.jpg';
	        }
		}

		$html = '<div class="th_cocoon_event_vmc">
                    <div class="th_cocoon_event_vmc_title">
                        <h2 class="title-cate">Sự kiện <span style="color:tomato">nổi bật</span></h2>
                    </div>
                    <div class="row th_cocoon_event_card">';

		for($i=1; $i<5; $i++){
            $file_event = 'file_event' . $i;
            $name_event = 'name_event' . $i;
            $date_event = 'date_event' . $i;
            $url_event = 'url_event' . $i;
            $image = $data->$file_event;
            $name = $data->$name_event;
            $date = date('d/m/Y', $data->$date_event);
            $url = $data->$url_event;

            if(mb_strlen($name, 'UTF-8') > 60) {        
                $name = mb_substr($name , 0 , 60, 'UTF-8') . '...';
            }

            if($name && $url_event){
                $html .= '<div class="col-xl-3 col-lg-4 col-md-6 col-xs-12">
                            <div class="event_item">
                                <div class = "event-img">
                                <a href = "'.$url.'"><img src = "'.$image.'" alt = "" class = "img-fluid d-block mx-auto event_img"></a>
                                </div>
                                <div class = "event-info">
                                    <div class="event-date">
                                        <i class="far fa-clock"></i><span>'.$date.'</span>
                                    </div>
                                    <a href = "'.$url.'" class = "d-block text-decoration-none py-2 event-name">'.$name.'</a>
                                </div>      
                            </div>              
                        </div>
				';
            }
		}

		$html .= '</div>
            <div class="viewall-events">
                <a href="'.$data->all_events.'"><i class="fas fa-plus"></i> Xem tất cả</a>
            </div>
		</div>
        ';
		
		$this->content->text = $html;

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

        for($i = 1; $i < 5; $i++) {
            $field = 'file_event' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            file_save_draft_area_files($data->$field, $this->context->id, 'block_th_cocoon_event_vmc', 'image_event', $i, $filemanageroptions);
        }

        parent::instance_config_save($data, $nolongerused);
    }

    /**
     * When a block instance is deleted.
     */
    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_th_cocoon_event_vmc');
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

        for($i = 1; $i < 5; $i++) {
            $field = 'file_event' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
            if (!$fs->is_area_empty($fromcontext->id, 'block_th_cocoon_event_vmc', 'image_event', $i, false)) {
                $draftitemid = 0;
                file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_th_cocoon_event_vmc', 'image_event', $i, $filemanageroptions);
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_th_cocoon_event_vmc', 'image_event', $i, $filemanageroptions);
            }
        }

        return true;
    }
}
?>