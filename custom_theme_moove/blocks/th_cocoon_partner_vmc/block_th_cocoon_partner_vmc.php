<?php

class block_th_cocoon_partner_vmc extends block_base {
	public function init() {
		$this->title = 'block_th_cocoon_partner_vmc';
	}

	public function hide_header()
	{
		return true;
	}

	public function get_content() {
		global $PAGE,$DB, $CFG;
		if ($this->content !== null) {
			return $this->content;
		}
		$this->content = new stdClass;
		$list_cate_product = $DB->get_field_sql("SELECT all_remote_product FROM {local_th_ecommercelib} WHERE id = 1");  
		$list_cate_product = json_decode($list_cate_product);
		$instanceid = $this->instance->id;

		$data = $this->config;

		$fs = get_file_storage();
		for($i=1;$i<=10;$i++){
			$image_partner = 'file_partner' . $i;
			$files = $fs->get_area_files($this->context->id, 'block_th_cocoon_partner_vmc', 'image_partner', $i, 'sortorder DESC, id ASC', false, 0, 0, 1);

			if (!empty($data->$image_partner) && count($files) >= 1) {
	          $mainfile = reset($files);
	          $mainfile = $mainfile->get_filename();
	          $data->$image_partner = moodle_url::make_pluginfile_url($this->context->id, 'block_th_cocoon_partner_vmc', 'image_partner', $i, '/', $mainfile, false)->out();
	        } else {
	          $data->$image_partner = '';
	        }
		}

		$image_partner_event = 'file_partner_event';
		$files = $fs->get_area_files($this->context->id, 'block_th_cocoon_partner_vmc', 'image_partner_event', 1, 'sortorder DESC, id ASC', false, 0, 0, 1);

		if (!empty($data->$image_partner_event) && count($files) >= 1) {
			$mainfile = reset($files);
			$mainfile = $mainfile->get_filename();
			$data->$image_partner_event = moodle_url::make_pluginfile_url($this->context->id, 'block_th_cocoon_partner_vmc', 'image_partner_event', 1, '/', $mainfile, false)->out();
		} else {
			$data->$image_partner_event = '';
		}

		$this->content->text = '
        <div class="th_cocoon_partner_vmc">
			<div class="th_cocoon_partner_vmc_title">
				<h2>Đồng hành cùng trung tâm <span style="color:tomato">VMC</span></h2>
			</div>
			<div id="items-slider'.$instanceid.'">';

		for($i=0;$i<=10;$i++){
			$file_partner = 'file_partner' . $i;
			if(isset($data->$file_partner)){
				$image = $data->$file_partner;
				if($image){
					$this->content->text .=	'
						<div class="partner_vmc_img">
							<img src="'.$image.'" class="swiper-lazy">
						</div>
					';
				}
			}
			
		}
		
		$this->content->text .= '</div>
							</div>';

		
		$this->content->text .= '
			<div class="th_cocoon_partner_vmc1">
				<div class="row th_cocoon_partner_card">                                            
					<div class="col-lg-8 col-md-12">
						<div class="banner-home-event">
							<a href="'.$data->url_partner_event.'" target="_blank">
								<img src="'.$data->file_partner_event.'" alt="">
							</a>
						</div>
					</div>
					<div class="col-lg-4 col-md-12">
						<div class="contents-home-event">
							<div class="content">
								'.$data->content_partner_event.'
							</div>
						</div>
					</div>
				</div>
			</div>';

		$options = '
		{
			"slidesToShow": 5, 
			"slidesToScroll": 1,
			"autoplay": true,
   	 		"autoplaySpeed": 2000,
			"swipe": false,
			"prevArrow": null,
  			"nextArrow": null,

			"responsive": [
				{
					"breakpoint":768,
					"settings":{
					   "slidesToShow":3,
					   "slidesToScroll":1
					}
				}
			]
		}';

		$PAGE->requires->js_call_amd('local_th_ecommercelib/main', 'makeSlider', array("#items-slider$instanceid", $options));
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

        for($i = 1; $i<=10; $i++) {
            $field = 'file_partner' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            file_save_draft_area_files($data->$field, $this->context->id, 'block_th_cocoon_partner_vmc', 'image_partner', $i, $filemanageroptions);
        }

		$field = 'file_partner_event';
		file_save_draft_area_files($data->$field, $this->context->id, 'block_th_cocoon_partner_vmc', 'image_partner_event', 1, $filemanageroptions);

        parent::instance_config_save($data, $nolongerused);
    }

    /**
     * When a block instance is deleted.
     */
    function instance_delete() {
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_th_cocoon_partner_vmc');
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

        for($i = 1; $i <=10; $i++) {
            $field = 'file_partner' . $i;
            if (!isset($data->$field)) {
                continue;
            }

            // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
            if (!$fs->is_area_empty($fromcontext->id, 'block_th_cocoon_partner_vmc', 'image_partner', $i, false)) {
                $draftitemid = 0;
                file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_th_cocoon_partner_vmc', 'image_partner', $i, $filemanageroptions);
                file_save_draft_area_files($draftitemid, $this->context->id, 'block_th_cocoon_partner_vmc', 'image_partner', $i, $filemanageroptions);
            }
        }

		if (!$fs->is_area_empty($fromcontext->id, 'block_th_cocoon_partner_vmc', 'image_partner_event', 1, false)) {
			$draftitemid = 0;
			file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_th_cocoon_partner_vmc', 'image_partner_event', 1, $filemanageroptions);
			file_save_draft_area_files($draftitemid, $this->context->id, 'block_th_cocoon_partner_vmc', 'image_partner_event', 1, $filemanageroptions);
		}

        return true;
    }
}
?>
