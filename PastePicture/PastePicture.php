<?php
require_once( config_get( 'class_path' ) . 'MantisPlugin.class.php' );

class PastePicturePlugin extends MantisPlugin  {

	/**
	 *  A method that populates the plugin information and minimum requirements.
	 */
	function register( ) {
		$this->name = "Paste Picture";
		$this->description = "Paste screenshot(clipboard picture, print screen etc) directly into new issue or edit issue page (works with Chromium based browsers only)";
		$this->page = 'config';

		$this->version = '1.0';
		$this->requires = array(
			'MantisCore' => '1.2.0',
		);

		$this->author = 'fatman';
		$this->contact = 'fatmanone@gmail.com';
		$this->url = 'http://www.greendot.ro';
	}


	function init() {

		plugin_event_hook( 'EVENT_REPORT_BUG', 'reportBug' );
		plugin_event_hook( 'EVENT_REPORT_BUG_FORM', 'reportBugFormTop' );
		plugin_event_hook( 'EVENT_VIEW_BUG_DETAILS', 'reportBugFormTop' );

	}

	function reportBugFormTop( $p_event, $p_project_id ) {

		//comment next line if jquery conflict
		echo '<script type="text/javascript" src="plugins/PastePicture/pages/jquery-1.8.0.min.js"></script>';
		echo '<script type="text/javascript" src="plugins/PastePicture/pages/paste.js"></script>';
		echo '<script type="text/javascript" src="plugins/PastePicture/pages/pastePicture.js"></script>';	
		
		/*
		html_javascript_link( 'jquery-1.8.0.min.js' );
		html_javascript_link( 'jquery.paste_image_reader.js' );
		html_javascript_link( 'pastePicture.js' );
		*/

	}

	function reportBug( $p_event, $p_bug_data ) {
		$p_var_name = 'ufile';
		if(isset($_POST[$p_var_name])){
			$f = $_POST[$p_var_name][0];
		
			$h="data:image/png;base64,";
			if(substr($f,0,strlen($h)) == $h){

				$data = base64_decode(substr($f,strlen($h)));
				$fn=tempnam("/tmp", "CLPBRD");
				file_put_contents($fn,$data);
				chmod($fn,0777);
				$t_result = array();
				$pi = pathinfo($fn);
				$t_result[0]['name'] = $pi['filename'].".png";
				$t_result[0]['type'] = "image/png";
				$t_result[0]['size'] = strlen($data);
				$t_result[0]['tmp_name'] = $fn;
				$t_result[0]['error'] = 0;
				file_add($p_bug_data->id,$t_result[0]);
			}
		}
	}
}
?>