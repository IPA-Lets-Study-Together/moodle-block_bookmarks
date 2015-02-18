<?php
require_once('../../config.php');
//require_once($CFG->dirroot.'/blocks/accessibility/lib.php');
require_login();

if (!confirm_sesskey()) error('Bad Session Key');
$op = required_param('op', PARAM_TEXT);
$id = required_param('id', PARAM_INT); // bookmark id


// is user allowed to modify this item?
$where = array(
	'userid' => $USER->id,
	'id' => $id
);
$bookmarks =  $DB->get_record('block_bookmarks', $where, 'chapterid');
if(!$bookmarks) error("Bookmark 'id' doesn't exist or it's not owned by this specific user.");

$redirecturl = new moodle_url('/blocks/bookmarks/edit.php');
switch ($op) {
	case 'delete':
		
		// TO-DO: maybe not to remove it completely but mark it "not to show anymore" ?
		echo ($DB->delete_records('block_bookmarks', array('id' => $id)));
		redirect($redirecturl->out(false));

        break;


	case 'rename':
		$chapter_title = trim(required_param('title', PARAM_TEXT));
		if(strlen($chapter_title) != 0) $title = $chapter_title;
		else $title = null;
		$data = array(
			'id' => $id,
			'title' => $title
		);

		echo ($DB->update_record('block_bookmarks', $data));
		redirect($redirecturl->out(false));

		break;


		// TO-DO: reorder bookmarks

		// TO-DO: remove all bookmarks
	default:
		header("HTTP/1.0 400 Bad Request");
}
