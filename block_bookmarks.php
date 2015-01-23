<?php

defined('MOODLE_INTERNAL') || die();

class block_bookmarks extends block_base {

	// CONFIGURATION
	CONST JS_CREATION_URL = '/blocks/bookmarks/bookmarks_creation.js';
	CONST JS_MAPPER_URL = '/blocks/bookmarks/bookmarks_mapper.js';
	CONST KEY_CODE = 32; // space key (ctrl+shift+space), bookmark_creation_key


	public function init() {
		$this->title = get_string('bookmarks', 'block_bookmarks'); // it's necessary for each block to have a unique, non-empty title after init() is called so that Moodle can use those titles to differentiate between all of the installed blocks.
	}

	// public function specialization(){} // called after init()
	public function specialization() {

		global $SESSION;
		global $chapter;

		// store chapterid so that ajax can access it while inserting bookmark to db
		if(isset($chapter->id))	$SESSION->chapterid = $chapter->id;
		else unset($SESSION->chapterid);
	}

	public function get_content()
	{
		if ($this->content !== null) {
		  return $this->content;
		}
		$this->content = new stdClass;

		// INCLUDE JS AND PASS PARAMETERS
		// ===============================================
		// language strings to pass to module.js
		$this->page->requires->string_for_js('untitled-bkm-item', 'block_bookmarks');
		$this->page->requires->string_for_js('browser-unsupported', 'block_bookmarks');
		$jscreation_data = array(
			'bookmark_creation_key' => self::KEY_CODE // // e.keyCode
		); 
		$jscreation_module = array(
			'name'  =>  'block_bookmarks_creation',
			'fullpath'  =>  self::JS_CREATION_URL, // use 'self' otherwise it might use globally defined constant
			'requires'  =>  array('base', 'node', 'event-key')
		);
		$jsmapper_data = array(

		); 
		$jsmapper_module = array(
			'name'  =>  'block_bookmarks_mapper',
			'fullpath'  =>  self::JS_MAPPER_URL, // use 'self' otherwise it might use globally defined constant
			'requires'  =>  array('base', 'node', 'querystring-stringify-simple')
		);

		// include js script and pass the arguments
		$this->page->requires->js_init_call('M.bkmCreation.init', $jscreation_data, false, $jscreation_module);
		$this->page->requires->js_init_call('M.bkmMapper.init', $jsmapper_data, false, $jsmapper_module);



		$content = '';
		/*****************************

			BOOKMARK LISTING PART

		*****************************/
		// ====== heading
		$content .= html_writer::tag('h4', get_string('user-bookmarks-title', 'block_bookmarks'));
	
		// ====== navigation list
		$attrs = array('class' => 'bookmarks_listing');
		$content .= html_writer::start_tag('nav', $attrs);

			// ====== bookmarks list
			$content .= html_writer::start_tag('ul');

				// TO-DO: make one tamplate element here so that JS can use it for its dynamic insertions
				$bookmarks = $this->get_all_bookmarks();
				//var_dump($bookmarks);

				// ====== li and a elements
				if($bookmarks){
					foreach ($bookmarks as $bookmark) {
						$content .= html_writer::start_tag('li');
						$attrs = array(
							'href' => '#', // will be filled by js later, together with id
							'class' => 'bookmark_link',
							'data-startNodeTree' => $bookmark->start_nodetree,
							'data-endNodeTree' => $bookmark->end_nodetree,
							'data-startOffset' => $bookmark->start_offset,
							'data-endOffset' => $bookmark->end_offset
						);
						if($bookmark->title == 'null') $bookmark->title = get_string('untitled-bkm-item', 'block_bookmarks');
						$content .= html_writer::tag('a', $bookmark->title, $attrs);
						$content .= html_writer::end_tag('li');
					}
				}
				else{
					$attrs = array('class' => 'no-bookmarks');
					$content .= html_writer::tag('li', get_string('no-bookmarks', 'block_bookmarks'), $attrs);
				}
				//var_dump($GLOBALS);

			$content .= html_writer::end_tag('ul');

			// ====== bookmark manipulation link (bookmarks editor)
			$editor_url = new moodle_url('/blocks/bookmarks/edit.php');
			$attrs = array('href' => $editor_url->out(false));
			$content .= html_writer::tag('a', get_string('bookmarks-editor', 'block_bookmarks'), $attrs);

		$content .= html_writer::end_tag('nav');

		/*****************************

			BOOKMARK CREATION PART

		*****************************/
		// ====== wrapper
		$attrs = array('class' => 'bookmarks_creation');
		$content .= html_writer::start_tag('div', $attrs);

			// ====== heading
			$content .= html_writer::tag('h4', get_string('bookmarks-creation-title', 'block_bookmarks'));

			// ====== form
			// TO-DO: not sure if it makes sense to make a form to work in non-js mode since you need js anway to capture user selection
			$attrs = array('action' => '#error', 'class' => 'form_insertBookmark');
			$content .= html_writer::start_tag('form', $attrs);

				// ====== user friendly label for bookmark title (label start+span)
				$attrs = array('class' => 'bookmarkTitleLabel'); // no need for id since it is wrapping what it needs to wrap
				$content .= html_writer::start_tag('label', $attrs);
				$content .= html_writer::tag('div', get_string('enter-title', 'block_bookmarks'));

					// ====== title input text box
					$attrs = array(
						'class' => 'fld_bookmarkTitle', 
						'type' => 'text',
						'placeholder' => get_string('enter-title', 'block_bookmarks')
					);
					$content .= html_writer::empty_tag('input', $attrs);
				
					// ====== submit button - insert bookmark
					$attrs = array(
						'class' => 'btn_insertBookmark',
						'type' => 'submit',
						'value' => get_string('btn-add-bookmark', 'block_bookmarks')
					);
					$content .= html_writer::empty_tag('input', $attrs);
				$content .= html_writer::end_tag('label');		
			$content .= html_writer::end_tag('form');

			// BOOKMARK INSERTION BUTTON - this one is used to capture the selection (alternative to ctrl+shift+space)
			/* dugme korisničkog sučelja za kreiranje oznake služi za privremenu pohranu selekcije, kao i shortcut key
			$attrs = array(
				'class' => 'btn_storeSelection',
				'type' => 'button',
				'value' => 'Pohrani označen tekst'
			);
			$content .= html_writer::empty_tag('input', $attrs);*/

			// ====== creation message and back to chapter link
			$attrs = array('class' => 'btn_backToChapter'); // other attrs will be filled dinamically
			$content .= html_writer::tag('a', get_string('creation-success', 'block_bookmarks'), $attrs);

			// TO-DO: what happens on failure? What status does user get?

			// ====== bookmark creation instructions
			// TO-DO: Make interactive live instructions, change the step instructions for each user action (now do this, now do that...)
			$content .= html_writer::tag('div', get_string('creation-instructions', 'block_bookmarks'));
		$content .= html_writer::end_tag('div');
		



		/*****************************

			NOTES

		*****************************/
		// ====== warning wrapper - test phase message
		$content .= html_writer::empty_tag('br');
		$attrs = array('class' => 'alert alert-danger test-message', 'role' => 'alert');
		$content .= html_writer::start_tag('div', $attrs);
			// ====== glyphicon
			$attrs = array('class' => 'glyphicon glyphicon-exclamation-sign', 'aria-hidden' => 'true');
			$content .= html_writer::tag('span', '', $attrs);
			// ====== sr only message
			$attrs = array('class' => 'sr-only');
			$content .= html_writer::tag('span', get_string('sr-note', 'block_bookmarks'), $attrs);
			$content .= html_writer::empty_tag('br');
			// ====== message
			$content .= html_writer::tag('span', get_string('test-phase-note', 'block_bookmarks'), $attrs);
		$content .= html_writer::end_tag('div');


		// ====== warning wrapper - browser unsupported
		$attrs = array('class' => 'alert alert-danger browser-unsupported-message', 'role' => 'alert');
		$content .= html_writer::start_tag('div', $attrs);
			// ====== glyphicon
			$attrs = array('class' => 'glyphicon glyphicon-exclamation-sign', 'aria-hidden' => 'true');
			$content .= html_writer::tag('span', '', $attrs);
			// ====== sr only message
			$attrs = array('class' => 'sr-only');
			$content .= html_writer::tag('span', get_string('sr-note', 'block_bookmarks'), $attrs);
			$content .= html_writer::empty_tag('br');
			// ====== message
			$content .= html_writer::tag('span', get_string('browser-unsupported', 'block_bookmarks'), $attrs);
		$content .= html_writer::end_tag('div');




		$this->content->text = $content;
		return $this->content;
	}

	private function get_all_bookmarks(){
		global $USER;
		global $DB;
		global $chapter;

		if(isset($chapter->id)){
			$where = array('userid' => $USER->id, 'chapterid' => $chapter->id);
			return $DB->get_records('block_bookmarks', $where);
		}
		else return null;
		
	}

	public function instance_allow_multiple() { return false; }
	public function has_config() { return true;} 
}

