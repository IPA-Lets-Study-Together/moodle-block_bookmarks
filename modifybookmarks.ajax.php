<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 
/**
 * This file is used for database access
 *
 * @package    block_bookmarks
 * @copyright  Copyright 2013 onwards University of Split, Faculty of Economics 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
