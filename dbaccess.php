<?php
// this is used for AJAX dbaccess

require_once('../../config.php');
//require_once($CFG->dirroot.'/blocks/accessibility/lib.php');
require_login();


$redirecturl = new moodle_url('/blocks/bookmarks/edit.php');



// TO-DO: Check the HTTP referrer here! It must be called from block

$op = required_param('op', PARAM_TEXT);

// TO-DO: validate arguments
// TO-DO: security check and preprocessing to prevent db attacks
// TO-DO: check if it's AJAX access?

switch ($op) {
	case 'insert':
		
 
		array_shift($_POST);
		$db_data = $_POST;
		$db_data['userid'] = $USER->id;
		$db_data['chapterid'] = $SESSION->chapterid; // from block
		$db_data['date'] = time(); // UNIX timestamp
		echo ($DB->insert_record('block_bookmarks', $db_data));

		break;

	case 'delete':
		
		$id = required_param('id', PARAM_TEXT);
		// TO-DO: maybe not to remove it completely but mark it "not to show anymore" ?
		echo ($DB->delete_records('block_bookmarks', array('id' => $id)));

		//if (!accessibility_is_ajax()) {
            redirect($redirecturl->out(false));

        break;


	case 'rename':
		$id = required_param('id', PARAM_TEXT);
		$title = required_param('title', PARAM_TEXT);
		$data = array(
			'id' => $id,
			'title' => $title
		);

		echo ($DB->update_record('block_bookmarks', $data));

		//if (!accessibility_is_ajax()) {
            redirect($redirecturl->out(false));


	
	
		break;


		// TO-DO: reorder bookmarks
	default:
		header("HTTP/1.0 400 Bad Request");
}
