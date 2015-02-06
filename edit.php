<?php

// TO-DO: Improve user experience!!


require_once(dirname(__FILE__) . '/../../config.php');
require_login(); 
//require_capability('local/greet:begreeted', $context); 

global $USER;
global $DB;

// page preparation
$context = context_system::instance();
$PAGE->set_context($context); 
$PAGE->set_url(new moodle_url('/blocks/bookmarks/edit.php'));
$PAGE->set_title(get_string('bookmarks-editor', 'block_bookmarks'));
// TO-DO: set some other page type?


$params['op'] = 'delete';
$db_url = new moodle_url('/blocks/bookmarks/dbaccess.php', $params);



$content = '';


// load bookmarks
$where = array('userid' => $USER->id);
$temp_chapterid = -1;
$bookmarks =  $DB->get_records('block_bookmarks', $where, 'chapterid');
if($bookmarks){
	foreach ($bookmarks as $bookmark) {
		// sort bookmarks from their chapters
		if($bookmark->chapterid !== $temp_chapterid){
			if ($temp_chapterid !== -1) $content .= html_writer::end_tag('ul'); // the last ending <ul> tag is added after for loop
			$content .= html_writer::tag('h1', 'Chapter id #'.$bookmark->chapterid);
			$content .= html_writer::start_tag('ul');
			$temp_chapterid = $bookmark->chapterid;
		}

		// create bookmark item
		$content .= html_writer::start_tag('li');
		$attrs = array(
			'href' => '#',
			'data-id' => $bookmark->id,
		);

		if($bookmark->title == 'null') $bookmark->title = get_string('untitled-bkm-item', 'block_bookmarks');
		$content .= html_writer::tag('a', $bookmark->title, $attrs);





		$attrs = array('action' => $db_url->out(false), 'method' => 'POST');
		$content .= html_writer::start_tag('form', $attrs);
		
			$attrs = array(
				'type' => 'submit',
				'value' => 'Remove bookmark' //get_string('btn-add-bookmark', 'block_bookmarks')
			);
			$content .= html_writer::empty_tag('input', $attrs);

			$attrs = array(
				'type' => 'hidden',
				'name' => 'id',
				'value' => $bookmark->id
			);
			$content .= html_writer::empty_tag('input', $attrs);

		$content .= html_writer::end_tag('form');
		










		// TO-DO: edit title






		$content .= html_writer::end_tag('li');
	}
	$content .= html_writer::end_tag('ul'); // ending ul tag
}
else
{
	$attrs = array('class' => 'no-bookmarks');
	$content .= html_writer::tag('li', get_string('no-bookmarks', 'block_bookmarks'), $attrs);
}

// TO-DO: depending on witch chapter it was accessed for, jump with # link to specific bookmarks...
// TO-DO: Create a link to get back to a chapter that user came from (check accessibility block functionalities for example)
// TO-DO: include separate JS file. You must ask a user if he is sure to remove the bookmark


// output
echo $OUTPUT->header();
echo $OUTPUT->box($content);
echo $OUTPUT->footer();

