<?php
define('AJAX_SCRIPT', true);

require_once('../../config.php');
require_login();

if (!confirm_sesskey()) error('Bad Session Key');
$op = required_param('op', PARAM_TEXT);

$redirecturl = new moodle_url('/blocks/bookmarks/edit.php');

switch ($op) {
	case 'insert':
		
		$db_data = array();
		$db_data['start_offset'] = required_param('start_offset', PARAM_INT);
		$db_data['end_offset'] = required_param('end_offset', PARAM_INT);
		$db_data['start_nodetree'] = required_param('start_nodetree', PARAM_TEXT);
		$db_data['end_nodetree'] = required_param('end_nodetree', PARAM_TEXT);
		$chapter_title = trim(required_param('title', PARAM_TEXT));
		if(strlen($chapter_title) != 0) $db_data['title'] = $chapter_title;
		else $db_data['title'] = null;
		$db_data['userid'] = $USER->id;
		$db_data['chapterid'] = required_param('chapterid', PARAM_INT);
		$db_data['date'] = time(); // UNIX timestamp
		echo ($DB->insert_record('block_bookmarks', $db_data)); // inserted id

		break;

	default:
		header("HTTP/1.0 400 Bad Request");
}
