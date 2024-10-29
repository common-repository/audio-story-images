/*global $, jQuery, alert*/

jQuery(document).ready(function ($) {
	
	"use strict";
	
	window.AsiController = function () {

		var $images = $('.asi-img');

		/*
		/ Rename asi-audio tags into audio ones.
		/ Param: none
		*/
		function renameAudioTags() {
			$('asi-audio').each(function () {
				var asi_id = $(this).attr('asi-id');
				$(this).replaceWith('<audio asi-id="' + asi_id + '">' + $(this).html() + '</audio>');
			});
		}

		/*
		/ Add the icons and style them
		/ Param: icon_style ( default )
		*/
		function addIcons(icon_style) {
			
			var icon_paused, icon_playing;
			
			// Choosing model
			switch (icon_style.model) {
			case 'model-1':
				icon_paused = '<div class="icon icon-model1-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model1-playing asi-player-icon asi-plus-icon"></div>';
				break;
			case 'model-2':
				icon_paused = '<div class="icon icon-model2-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model2-paused asi-player-icon asi-plus-icon"></div>';
				break;
			case 'model-3':
				icon_paused = '<div class="icon icon-model3-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model3-playing asi-player-icon asi-plus-icon"></div>';
				break;
			case 'model-4':
				icon_paused = '<div class="icon icon-model4-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model4-paused asi-player-icon asi-plus-icon"></div>';
				break;
			case 'model-5':
				icon_paused = '<div class="icon icon-model5-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model5-paused asi-player-icon asi-plus-icon"></div>';
				break;
			case 'model-6':
				icon_paused = '<div class="icon icon-model6-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model6-paused asi-player-icon asi-plus-icon"></div>';
				break;
			case 'model-7':
				icon_paused = '<div class="icon icon-model7-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model7-paused asi-player-icon asi-plus-icon"></div>';
				break;
			case 'model-8':
				icon_paused = '<div class="icon icon-model8-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model8-paused asi-player-icon asi-plus-icon"></div>';
				break;
			default:
				icon_paused = '<div class="icon icon-model1-paused asi-player-icon asi-minus-icon"></div>';
				icon_playing = '<div class="icon icon-model1-playing asi-player-icon asi-plus-icon"></div>';
			}
			
			$images.each(function (index, image) {
				var asi_id = $(this).attr('asi-id');
				$(this).attr('asi-id', asi_id);
				$(icon_paused).insertAfter($(this)).attr('asi-id', asi_id);
				$(icon_playing).insertAfter($(this)).attr('asi-id', asi_id);
			});
			
			// Coloring icons 
			$('.asi-player-icon').css({
				'color': icon_style.color
			});

			// Sizing icons
			switch (icon_style.size) {
			case 'small':
				$('.asi-player-icon').css({
					'font-size': '20px',
					'line-height': '20px',
					'width': '20px',
					'height': '20px'
				});
				break;

			case 'medium':
				$('.asi-player-icon').css({
					'font-size': '50px',
					'line-height': '50px',
					'width': '50px',
					'height': '50px'
				});
				break;

			case 'big':
				$('.asi-player-icon').css({
					'font-size': '80px',
					'line-height': '80px',
					'width': '80px',
					'height': '80px'
				});
				break;

			default:
				$('.asi-player-icon').css({
					'font-size': '30px',
					'line-height': '30px',
					'width': '30px'
				});
			}
		}

		/*
		/ Place the icons
		/ Param: icon_position ( top-left, top-right, bottom-left, bottom-right )
		*/
		function placeIcons(layout) {
			// Icon position
			var $icons = $('.asi-player-icon');
			$icons.each(function (index, icon) {

				var asi_id = $(this).attr('asi-id'),
					$linked_image = $('.asi-img[asi-id=' + asi_id + ']'),
					
					img_height = $linked_image.height(),
					img_width = $linked_image.width(),

					img_top = $linked_image.position().top,
					img_left = $linked_image.position().left,

					padding_vertical = 15,
					padding_horizontal = 5,

					icon_size = $(this).outerWidth(),
					
					icon_top = img_top + padding_vertical,
					icon_bottom = img_top + img_height - padding_horizontal - icon_size,
					icon_left = img_left + padding_horizontal,
					icon_right = img_left + img_width + padding_vertical,
					icon_center_top = img_top + img_height / 2 - icon_size / 2,
					icon_center_left = img_left + img_width / 2 - icon_size / 2;

				switch (layout.position) {
				case 'center':
					$(this).css({
						top: icon_center_top,
						left: icon_center_left
					});
					break;

				case 'top-left':
					$(this).css({
						top: icon_top,
						left: icon_left
					});
					break;

				case 'top-right':
					$(this).css({
						top: icon_top,
						right: icon_right
					});
					break;

				case 'bottom-left':
					$(this).css({
						top: icon_bottom,
						left: icon_left
					});
					break;

				case 'bottom-right':
					$(this).css({
						top: icon_bottom,
						right: icon_right
					});
					break;
				}
			});
		}

		/*
		/ Play or pause a sound
		/ Param: asi_id ( integer )
		*/
		this.toggleAudio = function (asi_id) {
			var $icon_minus = $('.asi-minus-icon[asi-id=' + asi_id + ']'),
				$icon_plus = $('.asi-plus-icon[asi-id=' + asi_id + ']'),
				$audio = $('audio[asi-id=' + asi_id + ']');

			if (!$audio.hasClass('playing')) {
				$('audio').trigger('pause').removeClass('playing');
				$('.asi-plus-icon').hide();
				$('.asi-minus-icon').show().css('display', 'inline');

				$audio.trigger("play");
				$icon_minus.hide();
				$icon_plus.show().css('display', 'inline');
				$audio.addClass('playing');
			} else {
				$audio.trigger("pause");
				$icon_minus.show().css('display', 'inline');
				$icon_plus.hide();
				$audio.removeClass('playing');
			}
		};

		this.init = function (options) {
			var layout = options.layout,
				icon_style = options.icon_style;

			renameAudioTags();
			addIcons(icon_style);
			placeIcons(layout, icon_style);
		};

	};
	
});