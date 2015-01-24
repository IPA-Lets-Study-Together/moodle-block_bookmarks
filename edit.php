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
 * An example of a stand-alone Moodle script.
 *
 * Says Hello, {username}, or Hello {name} if the name is given in the URL.
 *
 * @package   local_greet
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');        // 1
require_login();                                              // 2
$context = context_system::instance();                        // 3
//require_capability('local/greet:begreeted', $context);        // 4
$name = optional_param('name', '', PARAM_TEXT);               // 5
if (!$name) {
    //$name = fullname($USER);                                  // 6
    $name = fullname('hrvoje');                                  // 6
}
//add_to_log(SITEID, 'local_greet', 'begreeted','local/greet/index.php?name=' . urlencode($name));    // 7
$PAGE->set_context($context);                                 // 8
$PAGE->set_url(new moodle_url('/blocks/bookmarks/edit.php'), array('name' => $name));                              // 9
//$PAGE->set_title(get_string('welcome', 'local_greet'));       // 10
$PAGE->set_title('hello.');       // 10


global $USER;
global $DB;


	$where = array('userid' => $USER->id);
	$bookmarks =  $DB->get_records('block_bookmarks', $where);


$content = '';
if($bookmarks){
		foreach ($bookmarks as $bookmark) {
			$content .= html_writer::start_tag('li');
			$attrs = array(
				'href' => '#', // make chaPTER URL heRE
				'data-id' => $bookmark->id,

			);

// RAZVRSTATI PO NASLOVIMA CHAPTERA!!!

			if($bookmark->title == 'null') $bookmark->title = get_string('untitled-bkm-item', 'block_bookmarks');
			$content .= html_writer::tag('a', $bookmark->title, $attrs);
			$content .= html_writer::end_tag('li');
		}
	}
	else{
		$attrs = array('class' => 'no-bookmarks');
		$content .= html_writer::tag('li', get_string('no-bookmarks', 'block_bookmarks'), $attrs);
	}





echo $OUTPUT->header();                                       // 11
echo $OUTPUT->box($content. 'box. Return back...');                               // 12
//echo $OUTPUT->box(get_string('greet', 'local_greet', format_string($name)));                               // 12
echo $OUTPUT->footer();                                       // 13

