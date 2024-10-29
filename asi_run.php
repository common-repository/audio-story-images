<?php

class Meow_ASI_Run {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'the_content' ) );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'imagesLoaded', plugins_url( '/js/imagesLoaded.js', __FILE__ ), array( 'jquery' ), '0.0.1', false );
		wp_enqueue_script( 'asi-core', plugins_url( '/js/asi-core.js', __FILE__ ), array( 'jquery', 'imagesLoaded' ), '0.0.1', false );
		wp_enqueue_script( 'asi-run', plugins_url( '/js/asi-run.js', __FILE__ ), array( 'jquery', 'asi-core', 'imagesLoaded' ), '0.0.1', false );
		wp_enqueue_style( 'asi-css', plugin_dir_url( __FILE__ ) . 'css/asi.css' );
		wp_enqueue_style( 'asi-icons', plugin_dir_url( __FILE__ ) . 'css/icons.css' );
	}

	function the_content( $content ) {
		$content = mb_convert_encoding( $content, 'HTML-ENTITIES', "UTF-8" );
		$document = new DOMDocument();
		libxml_use_internal_errors( true );
		if ( empty( $content ) )
			return;
		$document->loadHTML( utf8_decode( $content ) );
		$imgs = $document->getElementsByTagName( 'img' );
		$body = $document->getElementsByTagName( 'body' );
		if ( empty( $body ) )
			return $content;
		$body = $body->item(0);
		foreach ( $imgs as $img ) {
			 $existing_class = $img->getAttribute( 'class' );
			 preg_match( '/wp-image-(?P<id>\d+)/i', $existing_class, $matches );
			 if ( count( $matches ) > 0 ) {
				 $id = $matches['id'];
				 $attached = get_post_meta( $matches['id'], '_asi_link', true );
				 if ( !empty( $attached ) ) {
					 $existing_class = $img->getAttribute( 'class' );
					 $img->setAttribute( 'class', "$existing_class asi-img" );
					 $img->setAttribute( 'asi-id', $id );
					 $div = $body->appendChild( $document->createElement( "asi-audio" ) );
					 $div->setAttribute( 'asi-id', $id );
					 $src = $div->appendChild( $document->createElement( "source" ) );
					 $src->setAttribute( 'src', wp_get_attachment_url( $attached ) );
				 }
			 }
		}
		$html = $document->saveHTML();
		return $html;
	}

}

?>
