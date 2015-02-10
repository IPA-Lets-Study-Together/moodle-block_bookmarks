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
$PAGE->set_title(get_string('editing_page_title', 'block_bookmarks'));
// TO-DO: set some other page type?


$params['op'] = 'delete';
$db_delete_url = new moodle_url('/blocks/bookmarks/dbaccess.php', $params);

$params['op'] = 'rename';
$db_rename_url = new moodle_url('/blocks/bookmarks/dbaccess.php', $params);



$content = '';

// js warning
$content .= html_writer::tag('noscript', get_string('no-js', 'block_bookmarks'));


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




		// deletion form
		$attrs = array('action' => $db_delete_url->out(false), 'method' => 'POST',
				// TO-DO: This could be done better
				'onsubmit' => 'if(!confirm("Are you sure you want to delete a bookmark \''.$bookmark->title.'\' entirely? It cannot be undone.")) return false;'
			);
		$content .= html_writer::start_tag('form', $attrs);
		
			$attrs = array(
				'type' => 'hidden',
				'name' => 'id',
				'value' => $bookmark->id
			);
			$content .= html_writer::empty_tag('input', $attrs);

			$attrs = array(
				'type' => 'submit',
				'value' => 'Remove bookmark' //get_string('btn-add-bookmark', 'block_bookmarks')
			);
			$content .= html_writer::empty_tag('input', $attrs);

			

		$content .= html_writer::end_tag('form');



		// rename title form
		$attrs = array('action' => $db_rename_url->out(false), 'method' => 'POST',
				// TO-DO: This could be done better
				'onsubmit' => 'answer = prompt("Please enter a new bookmark title or cancel to abort the action:", \''.$bookmark->title.'\');
					if(answer === null) return false;
					else this.elements.item(0).value = answer;'
			);
		$content .= html_writer::start_tag('form', $attrs);

			// because of js, this has to be at the first index in the form
			$attrs = array(
				'type' => 'text', // this will br changed to 'hidden' if js is allowed
				'name' => 'title',
				'value' => $bookmark->title
			);
			$content .= html_writer::empty_tag('input', $attrs);

			$attrs = array(
				'type' => 'hidden',
				'name' => 'id',
				'value' => $bookmark->id
			);
			$content .= html_writer::empty_tag('input', $attrs);

		
			$attrs = array(
				'type' => 'submit',
				'value' => 'Rename bookmark' //get_string('btn-add-bookmark', 'block_bookmarks')
			);
			$content .= html_writer::empty_tag('input', $attrs);

		$content .= html_writer::end_tag('form');
		





		$content .= html_writer::end_tag('li');
	}

	// if there is js support, chage all <input type="text" to type="hidden"
	$content .= html_writer::tag('script', '
		var inputs = document.getElementsByTagName("input");
		for (var i = 0; i < inputs.length; i++) { 
			var isText = inputs[i].getAttribute("type"); 
			if (isText == "text") inputs[i].setAttribute("type", "hidden")
		}', array('type' => 'text/javascript'));

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

