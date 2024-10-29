/*global $, jQuery, alert, AsiController */

jQuery(document).ready(function ($) {
	"use strict";
	
	$('body').imagesLoaded(function () {
		
		var options = {
				layout: {
					'position': 'top-left'
				},
				icon_style: {
					'color': 'white',
					'size': 'small',
					'model': 'model-1'
				}
			},
			asiController = new AsiController();
		
		asiController.init(options);

		$(document).on('click', '.asi-player-icon', function (e) {
			e.preventDefault();
			var asi_id = $(this).attr('asi-id');
			asiController.toggleAudio(asi_id);
		});

		$('audio').on("ended", function () {
			$(this).currentTime = 0;
			var asi_id = $(this).attr('asi-id');
			asiController.toggleAudio(asi_id);
		});

		$(window).on('resize', function () {
			asiController.placeIcons(options.layout.position);
		});
		
	});
	
});