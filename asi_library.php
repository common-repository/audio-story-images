<?php

class Meow_ASI_Library {

	public function __construct() {
		add_filter( 'manage_media_columns', array( $this, 'manage_media_columns' ) );
		add_action( 'manage_media_custom_column', array( $this, 'manage_media_custom_column' ), 10, 2 );
		//add_action( 'admin_head', array( $this, 'admin_head' ), 10, 2 );
    //add_action( 'pre_get_posts', array( $this, 'media_library_filters' ) );
		//add_action( 'restrict_manage_posts', array( $this, 'media_dropdown' ) );

		add_action( 'admin_footer', array( $this, 'footer' ) );
		add_action( 'wp_ajax_ais_search', array( $this, 'wp_ajax_ais_search' ) );
		add_filter( 'wp_redirect', array( $this, 'wp_redirect' ), 25, 1 );
		add_filter( 'admin_head', array( $this, 'admin_head' ), 25, 1 );
	}

	function manage_media_columns( $cols ) {
		$cols["ASI"] = "Audio Story";
		return $cols;
	}

	function manage_media_custom_column( $column_name, $id ) {
		if ( $column_name != 'ASI' )
			return;
		$type = null;
		$text = null;

		$attached = get_post_meta( $id, '_asi_link', true );
		if ( empty( $attached ) ) {
			if ( wp_attachment_is( 'image', $id ) ) {
				$type = 'audio';
				$text = __ ( "Link this Image to an Audio file", 'audio-story-images' );
			}
			else if ( wp_attachment_is( 'audio', $id ) ) {
				$type = 'image';
				$text = __ ( "Link this Audio file to an Image", 'audio-story-images' );
			}
			else
				return;
	    ?>
	      (Unattached)<br>
				<a href="#the-list" onclick="findAnything.open( '<?php echo $type; ?>', '<?php echo $text; ?>', '<?php echo $id; ?>' ); return false;"
					class="hide-if-no-js">Attach</a>
	    <?php
		}
		else {
			$p = get_post( $attached );
			if ( wp_attachment_is( 'image', $attached ) ) {
				echo "<strong>IMG: " . $p->post_title . "</strong>";
			}
			else {
				echo "<strong>ISOUND: " . $p->post_title . "</strong>";
			}
			echo '<br /><a href="upload.php?ais_detach_id=' . $id . '&amp;ais_detach_id2=' . $p->ID .
				'&amp;_wpnonce=' . wp_create_nonce( 'ais-remove_' . $p->ID ) .
				'" class="hide-if-no-js detach-from-parent" aria-label="Detach from “' . $p->post_title .
				'”">Detach</a>';

		}
	}

	function admin_head() {
		global $pagenow;

		// Detach
		if ( 'upload.php' == $pagenow && isset( $_REQUEST['ais_detach_id'] ) && isset( $_REQUEST['ais_detach_id2'] ) ) {
			$id = $_REQUEST['ais_detach_id'];
			$id2 = $_REQUEST['ais_detach_id2'];
			check_admin_referer( 'ais-remove_' . $id2 );
			delete_post_meta( $id, '_asi_link' );
			delete_post_meta( $id2, '_asi_link' );
		}
	}

	function wp_redirect( $location )
	{
	  if ( !is_admin() )
	    return $location;
	  global $pagenow;

		// Attach
		if ( 'upload.php' == $pagenow && isset( $_REQUEST['found_ais_id'] ) ) {
      $parent_id = (int) $_REQUEST['found_ais_id'];
			$current_id = (int) $_REQUEST['current_ais_id'];
      if ( !$parent_id || !$current_id )
        return $location;
      update_post_meta( $current_id, '_asi_link', $parent_id );
			update_post_meta( $parent_id, '_asi_link', $current_id );
	  }

	  return $location;
	}

	function wp_ajax_ais_search() {
		$type = $_POST['type'];
		$s = wp_unslash( $_POST['search'] );
		$args = array(
				'post_type' => 'attachment',
				'post_status' => 'any',
				'posts_per_page' => 50,
				'post_mime_type' => $type == 'audio' ? array( 'audio/mpeg3', 'audio/mpeg', 'audio/wav' ) :
					array( 'image/jpeg', 'image/png', 'image/gif' )
		);
		if ( '' !== $s )
				$args['s'] = $s;

		$posts = get_posts( $args );

		if ( ! $posts ) {
				wp_send_json_error( __( 'No items found.' ) );
		}

		$html = '<table class="widefat"><thead><tr><th class="found-radio"><br /></th><th>' .
			__(' Title' ) . '</th><th class="no-break">' .
			__( 'Date' ) . '</th></tr></thead><tbody>';
		$alt = '';
		foreach ( $posts as $post ) {
				$title = trim( $post->post_title ) ? $post->post_title : __( '(no title)' );
				$alt = ( 'alternate' == $alt ) ? '' : 'alternate';
				if ( '0000-00-00 00:00:00' == $post->post_date )
					$time = '';
				else
					$time = mysql2date(__('Y/m/d'), $post->post_date);
				$html .= '<tr class="' . trim( 'found-posts ' . $alt ) . '"><td class="found-radio"><input type="radio" id="found-'.$post->ID.'" name="found_ais_id" value="' . esc_attr($post->ID) . '"></td>';
				$html .= '<td><label for="found-'.$post->ID.'">' . esc_html( $title ) . '</label></td><td class="no-break">'.esc_html( $time ) . '</td></tr>' . "\n\n";
		}
		$html .= '</tbody></table>';
		wp_send_json_success( $html );
	}

	function footer() {
		?>
		<script>
		var findAnything;
		( function( $ ){
			findAnything = {
				type: null,
				open: function( type, desc, id ) {
					if ( findAnything.type == null && type == null ) {
						alert("An action is required by Meowlib FindAnything.");
						return;
					}
					findAnything.type = type;
					var overlay = $( '#ui-find-overlay-anything-' + findAnything.type );
					if ( overlay.length === 0 ) {
						$( 'body #posts-filter' ).append( '<div id="find-anything" class="find-box" style="display: none;">'
							+ '<div id="find-anything-head" class="find-box-head">'
							+ desc + '<div id="find-anything-close"></div>'
							+ '</div>'
							+ '<div class="find-box-inside">'
							+ '	<div class="find-box-search">'
							+ '		<label class="screen-reader-text" for="find-anything-input">Search</label>'
							+ '		<input type="text" id="find-anything-input" name="search" value="">'
							+ '		<span class="spinner"></span>'
							+ '		<input type="button" id="find-anything-search" value="Search" class="button">'
							+ '		<div class="clear"></div>'
							+ '	</div>'
							+ '	<div id="find-anything-response"></div>'
							+ '</div>'
							+ '<div class="find-box-buttons">'
							+ '<input type="hidden" name="current_ais_id" value="' + id + '">'
							+ '	<input type="submit" name="find-anything-submit" id="find-anything-submit" class="button button-primary alignright" value="Select"><div class="clear"></div>'
							+ '</div></div>' );
						$( 'body' ).append( '<div id="ui-find-overlay-anything-' + findAnything.type
							+ '" class="ui-find-overlay"></div>' );
					}
					overlay.show();
					$( '#find-anything' ).show();
					$( '#find-anything-input' ).focus().keyup( function( event ){
						if ( event.which == 27 ) {
							findAnything.close();
						}
					});
					$( '#ui-find-overlay-anything-' + findAnything.type ).on( 'click', function () {
						findAnything.close();
					});
					findAnything.send();
					return false;
				},

				close: function() {
					$( '#find-anything-response' ).remove();
					$(' #find-anything' ).remove();
					$( '#ui-find-overlay-anything-' + findAnything.type ).remove();
				},

				send: function() {
					var post = {
						action: 'ais_search',
						type: findAnything.type,
						search: $( '#find-anything-input' ).val()
					},
					spinner = $( '.find-box-search .spinner' );
					spinner.addClass( 'is-active' );

					$.ajax( ajaxurl, {
						type: 'POST',
							data: post,
						dataType: 'json'
					}).always( function() {
						spinner.removeClass( 'is-active' );
					}).done( function( x ) {
						if ( ! x.success ) {
							$( '#find-anything-response' ).text( attachMediaBoxL10n.error );
						}
						$( '#find-anything-response' ).html( x.data );
					}).fail( function() {
						$( '#find-anything-response' ).text( attachMediaBoxL10n.error );
					});
				}
			};

			$( document ).ready( function() {
				$( '#find-anything-submit' ).click( function( event ) {
					if ( ! $( '#find-anything-response input[type="radio"]:checked' ).length ) {
						event.preventDefault();
					}
				});
				$( '#find-anything .find-box-search :input' ).keypress( function( event ) {
					if ( 13 == event.which ) {
						findAnything.send();
						return false;
					}
				});
				$( '#find-anything-search' ).click( findAnything.send );
				$( '#doaction, #doaction2' ).click( function( event ) {
					$( 'select[name^="action"]' ).each( function() {
						var optionValue = $( this ).val();
						if ( 'attach' === optionValue ) {
							event.preventDefault();
							findAnything.open();
						} else if ( 'delete' === optionValue ) {
							if ( ! showNotice.warn() ) {
								event.preventDefault();
							}
						}
					});
				});

				// Enable whole row to be clicked
				$( '.find-box-inside' ).on( 'click', 'tr', function() {
					$( this ).find( '.found-radio input' ).prop( 'checked', true );
				});
			});
		})( jQuery );
		</script>
		<?php
	}
}

?>
