<?php

class Meow_ASI_Core {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/*
		INIT
	*/

	function init() {
		if ( is_admin() ) {
			include( 'asi_library.php' );
			new Meow_ASI_Library;
		}
		else {
			include( 'asi_run.php' );
			new Meow_ASI_Run;
		}
	}

	/*
		CORE
	*/

	function log( $data ) {
		if ( !get_option( 'asu_debuglogs', false ) )
			return;
		$fh = fopen( trailingslashit( plugin_dir_path( __FILE__ ) ) . '/audio-story-images.log', 'a' );
		$date = date( "Y-m-d H:i:s" );
		fwrite( $fh, "$date: {$data}\n" );
		fclose( $fh );
	}
}

?>
