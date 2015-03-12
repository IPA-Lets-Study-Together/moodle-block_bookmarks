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
		// TO-DO: verify if the entry has been removed, echo will always return true
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

		// TO-DO: verify if the entry has been renamed ? or...
		echo ($DB->update_record('block_bookmarks', $data));
		redirect($redirecturl->out(false));

		break;


		// TO-DO: reorder bookmarks

		// TO-DO: remove all bookmarks
	default:
		header("HTTP/1.0 400 Bad Request");
}
