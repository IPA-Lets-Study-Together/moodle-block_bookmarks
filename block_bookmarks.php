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
 * @package    block_bookmarks
 * @copyright  Copyright 2013 onwards University of Split, Faculty of Economics 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
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
	/* Initial idea was to transfer chapterid with the session for security reason. This however might cause that you create bookmark in another chapter if you have loaded two chapter pages in the same time becuase this another one will overwrite session variable
	public function specialization() {

		global $SESSION;
		global $chapter;

		// store chapterid so that ajax can access it while inserting bookmark to db
		if(isset($chapter->id))	$SESSION->chapterid = $chapter->id;
		else unset($SESSION->chapterid);
	}*/

	public function get_content()
	{
		global $chapter;

		if ($this->content !== null) {
		  return $this->content;
		}
		
		$this->content = new stdClass;
		$content = '';


		/*****************************

			 if not in moodle book chapter context, don't show anything

		*****************************/
		if(!isset($chapter)){
			// ====== display instructions for positioning a block by the side of Moodle Book Chapter

			$attrs = array('class' => 'alert alert-danger', 'role' => 'alert');
			$content = html_writer::start_tag('div', $attrs);
				// ====== glyphicon
				$attrs = array('class' => 'glyphicon glyphicon-exclamation-sign', 'aria-hidden' => 'true');
				$content .= html_writer::tag('span', '', $attrs);
				// ====== sr only message
				$attrs = array('class' => 'sr-only');
				$content .= html_writer::tag('span', get_string('sr-note', 'block_bookmarks'), $attrs);
				$content .= html_writer::empty_tag('br');
				// ====== message
				$content .= html_writer::tag('p', 'This block works only with Moodle Book chapter. You can create an instance of a block in each Moodle Book chapter view where you need it. To include a block globally across all Moodle Book chapter views please follow this instructions:');
				$content .= html_writer::start_tag('ol');
					$content .= html_writer::tag('li', 'Go to Moodle homepage. Create a block instance to the Moodle homepage and set the configuration for the block to be visible in any page throughout entire Moodle');
					$content .= html_writer::tag('li', 'Go to any Moodle Book chapter page. Access the configuration again and restrict the block to be visible only on Moodle Book type of pages');
				$content .= html_writer::end_tag('ol');
			$content .= html_writer::end_tag('div');


			$this->content->text = $content;
			return $this->content;
		}
		


		/*****************************

			INCLUDE JS AND PASS PARAMETERS

		*****************************/
		// language strings to pass to module.js
		$this->page->requires->string_for_js('untitled-bkm-item', 'block_bookmarks');
		$this->page->requires->string_for_js('browser-unsupported', 'block_bookmarks');
		$this->page->requires->string_for_js('aria-start-pin', 'block_bookmarks');
		$this->page->requires->string_for_js('aria-end-pin', 'block_bookmarks');
		$jscreation_data = array(
			'bookmark_creation_key' => self::KEY_CODE, // e.keyCode
			'chapterid' => $chapter->id
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
				$bookmarks = $this->_get_all_bookmarks();
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
						if($bookmark->title == null) $bookmark->title = get_string('untitled-bkm-item', 'block_bookmarks');
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
						'maxlength' => 50, // database limitation
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
			// TO-DO: Make interactive live instructions, change the step instructions for each user action (now do this, now do that...) and warnings such as: you have to select the text within the chapter, not ouside
			$content .= html_writer::tag('div', get_string('creation-instructions', 'block_bookmarks'));
		$content .= html_writer::end_tag('div');
		



		/*****************************

			NOTES / WARNINGS

		*****************************/
		$content .= html_writer::empty_tag('br');

		// ====== warning wrapper - Javascript is not enabled
		$content .= html_writer::start_tag('noscript');
			$content .= $this->_generate_warning('alert alert-danger no-js-message', 'no-js');
		$content .= html_writer::end_tag('noscript');

		// ====== warning wrapper - unsupported browser message
		$content .= $this->_generate_warning('alert alert-danger browser-unsupported-message', 'browser-unsupported');

		// ====== warning wrapper - test phase warning
		$showBetaTestPhase = TRUE; // by default
		if(isset($this->config->test_warning_enabled)) $showBetaTestPhase = $this->config->test_warning_enabled;

		if($showBetaTestPhase)
			$content .= $this->_generate_warning('alert alert-danger test-message', 'test-phase-note');

		// TO-DO: give a warning (bookmarks cannot be created here) is block is displayed outside of moodle book context (for example at site-index page)


		// ====== loader icon
		// html_writer::empty_tag('img', array('src' => new moodle_url(self::LOADER_ICON), 'alt' => 'loader icon')); // it doesn't work so it's implemented by CSS
		$spanattrs = array('class' => 'loader-icon');
		$content .= html_writer::start_tag('span', $spanattrs);
		$content .= html_writer::end_tag('span');


		$this->content->text = $content;
		return $this->content;
	}

	private function _get_all_bookmarks(){
		global $USER;
		global $DB;
		global $chapter;

		if(isset($chapter->id)){
			$where = array('userid' => $USER->id, 'chapterid' => $chapter->id);
			return $DB->get_records('block_bookmarks', $where);
		}
		else return null;
		
	}

	private function _generate_warning($class, $text_lang_key){
		$attrs = array('class' => $class, 'role' => 'alert');
		$content = html_writer::start_tag('div', $attrs);
			// ====== glyphicon
			$attrs = array('class' => 'glyphicon glyphicon-exclamation-sign', 'aria-hidden' => 'true');
			$content .= html_writer::tag('span', '', $attrs);
			// ====== sr only message
			$attrs = array('class' => 'sr-only');
			$content .= html_writer::tag('span', get_string('sr-note', 'block_bookmarks'), $attrs);
			$content .= html_writer::empty_tag('br');
			// ====== message
			$content .= html_writer::tag('span', get_string($text_lang_key, 'block_bookmarks'));
		$content .= html_writer::end_tag('div');

		return $content;
	}

	public function instance_allow_multiple() { return false; }
	public function applicable_formats() { return array(
			'mod-book-view' => true, // allow only in moodle book chapters, and index page for instancing it
			'site-index' => true,
			'course-*' => true, // for those who need it
			'course-index-*' => true // for those who need it
		);
	} 
}

