define([
	'jquery',
	'local_th_ecommercelib/slick',
	], function($,slick) {
	return {
		makeSlider: function(selector,options) {

			$(document).ready(function() {
				var obj = JSON.parse(options);
				$(selector).not('.slick-initialized').slick(obj);
			});
		},
	};

});