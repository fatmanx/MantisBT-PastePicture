<?php
# MantisBT - a php based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

# MODIFIED

	/**
	 * Add file to a bug and then view the bug
	 *
	 * @package MantisBT
	 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	 * @copyright Copyright (C) 2002 - 2012  MantisBT Team - mantisbt-dev@lists.sourceforge.net
	 * @link http://www.mantisbt.org
	 */
	 /**
	  * MantisBT Core API's
	  */

	require_once( 'core.php' );
	require_once( 'file_api.php' );


	function gpc_get_fileCustom( $p_var_name, $p_default = null ) {
	if( isset( $_FILES[$p_var_name] ) ) {

		# FILES are not escaped even if magic_quotes is ON, this applies to Windows paths.
		$t_result = $_FILES[$p_var_name];
	}
	else if(isset($_POST[$p_var_name])){
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
		}
	}
	else if( func_num_args() > 1 ) {
		# check for a default passed in (allowing null)
		$t_result = $p_default;
	} else {

		error_parameters( $p_var_name );
		trigger_error( ERROR_GPC_VAR_NOT_FOUND, ERROR );
	}
	return $t_result;

}



	$f_bug_id = gpc_get_int( 'bug_id', -1 );
	$f_files  = gpc_get_fileCustom( 'ufile', -1 );

	if ( $f_bug_id == -1 && $f_files == -1 ) {
		# _POST/_FILES does not seem to get populated if you exceed size limit so check if bug_id is -1
		trigger_error( ERROR_FILE_TOO_BIG, ERROR );
	}

	form_security_validate( 'bug_file_add' );

	$t_bug = bug_get( $f_bug_id, true );
	if( $t_bug->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the bug we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_bug->project_id;
	}

	if ( !file_allow_bug_upload( $f_bug_id ) ) {
		access_denied();
	}

	access_ensure_bug_level( config_get( 'upload_bug_file_threshold' ), $f_bug_id );

	// Process array of files to upload
	for( $i = 0; $i < count( $f_files ); $i++ ) {
		if( !empty( $f_files[$i]['name'] ) ) {
			$t_file['name']     = $f_files[$i]['name'];
			$t_file['tmp_name'] = $f_files[$i]['tmp_name'];
			$t_file['type']     = $f_files[$i]['type'];
			$t_file['error']    = $f_files[$i]['error'];
			$t_file['size']     = $f_files[$i]['size'];
			file_add( $f_bug_id, $t_file, 'bug' );
		}
	}

	form_security_purge( 'bug_file_add' );

	# Determine which view page to redirect back to.
	$t_redirect_url = string_get_bug_view_url( $f_bug_id );

	html_page_top( null, $t_redirect_url );
?>
<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';
	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php
	html_page_bottom();
