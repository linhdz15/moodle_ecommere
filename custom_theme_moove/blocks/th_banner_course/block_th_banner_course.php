<?php

class block_th_banner_course extends block_base {
	public function init() {
		$this->title = get_string('title', 'block_th_banner_course');
	}

	public function hide_header()
	{
		return true;
	}

	public function get_content() {
		global $CFG;
		if ($this->content !== null) {
			return $this->content;
		}
		$this->content = new stdClass;
		
		$this->content->footer = '
		<div class="th_banner_course">
		<div class="cd-top-banner have-bg opt-2" style="background-image: url("https://cdn-skill.kynaenglish.vn/uploads/categories/img/cover-tieng-trung.jpg");">
		<div class="container">
			<div class="course-detail--left">
					<h1 class="cd-title">Tiếng Hoa cho người mới bắt đầu 1</h1>
			<div class="gv-info">
			<div class="gv-left">
				<img src="https://cdn-skill.kynaenglish.vn/uploads/user/106639/img/avatar.cover-100x100.jpg" alt="Kyna - Avatar    Giang Vien">
			</div>
			<div class="gv-right">
				<h2 class="gv-name">Nguyễn Minh Thúy</h2>
				<h4 class="gv-title">Giảng viên trường ĐH KHXH &amp; NV TP.HCM</h4>
				<div class="gv-short-des truncate-2 m-b-0"><p><span style="color: #333333;">- Công tác tại Khoa Ngữ văn Trung Quốc trường Đại học KHXH &amp; NV TP.HCM từ 1995 đến nay.</span></p>
				<p><span style="color: #333333;">- Chuyên ngành: Tiếng Hán hiện đại</span></p>
				<p><span style="color: #333333;">Được đào tạo:</span><br><span style="color: #333333;"> - Hệ cử nhân khoa Trung văn trường Đại học Sư Phạm TP.HCM (khoá 1990 - 1995)</span><br><span style="color: #333333;"> - Thạc sĩ ngành Ngôn ngữ học so sánh trường ĐHKHXH &amp; NV TP.HCM (1999 - 2003)</span></p></div>
				<a class="gv-btn-view-more" href="/giang-vien/nguyen-minh-thuy">Xem thêm</a>
			</div>
		</div>
		
		<ul class="crs-short-info">
			<li>
				<img class="crs-icon-info" src="https://cdn-skill.kynaenglish.vn/img/level.svg" alt="Kyna - Icon trinh do">	  
			  <p>Trình độ: <span>Cơ bản</span></p>
			</li>
				<li>
					<img class="crs-icon-info" src="https://cdn-skill.kynaenglish.vn/img/rating.svg" alt="Kyna - Icon danh gia">
					  
			  <div class="crs-total-star">
				<span>4.3</span>
				<img class="is-mobile" src="https://cdn-skill.kynaenglish.vn/img/star-fill.svg" alt="Kyna - Star">
				<div class="wrap-star is-desktop">
												<img src="https://cdn-skill.kynaenglish.vn/img/star-fill.svg" alt="Kyna - Star">
															  <img src="https://cdn-skill.kynaenglish.vn/img/star-fill.svg" alt="Kyna - Star">
															  <img src="https://cdn-skill.kynaenglish.vn/img/star-fill.svg" alt="Kyna - Star">
															  <img src="https://cdn-skill.kynaenglish.vn/img/star-fill.svg" alt="Kyna - Star">
															  <img src="https://cdn-skill.kynaenglish.vn/img/star-half.svg" alt="Kyna - Star">
										  </div>
			  </div>
			</li>
				  <li>
						  <img class="crs-icon-info" src="https://cdn-skill.kynaenglish.vn/img/certificate.svg" alt="Kyna - Icon hoan thanh">
					  
			  <p>Cấp chứng nhận <span>hoàn thành</span></p>
			</li>
		  </ul>
	  
		</div>
	  </div>
	  <div class="cd-overlay" style="background-color:rgba(0, 0, 0, 0.6)"></div>
	  </div>
	  </div>';
		
		return $this->content;
	}
}
?>