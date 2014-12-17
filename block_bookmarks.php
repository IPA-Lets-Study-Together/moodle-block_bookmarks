<?php

defined('MOODLE_INTERNAL') || die();
//require_once($CFG->dirroot.'/blocks/bookmarks/lib.php');




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

	  /*if (!empty($this->config->title)) {
	    $this->title = $this->config->title;
	  } else {
	    $this->title = 'Hrvojev block';
	  }*/

	  // store chapterid so that ajax can access it while inserting bookmark to db
	  if(isset($_REQUEST['chapterid']))
	  $SESSION->chapterid = $_REQUEST['chapterid'];
	 
	  /*if (empty($this->config->text)) {
	    $this->config->text = 'Default text ...';
	  } */  
	}

	// dodaj blocku posebnu css klasu
	/*public function html_attributes() {
	    $attributes = parent::html_attributes(); // Get default values
	    $attributes['class'] .= ' block_'. $this->name(); // Append our class to class attribute
	    return $attributes;
	}*/

	public function get_content()
	{
		/*if ($this->content !== null) {
		  return $this->content;
		}*/
	


		$this->content         =  new stdClass;
		//$this->content->text   = '<script type="text/javascript">function changeme(){document.getElementById("tochange").innerHTML = "Najbolji"}</script><h2>Ovo je moj prvi block!</h2><p id="tochange" style="color:red">Kako je dobar! <a onclick="changeme()">Klikni tu</a></p>';

		if (! empty($this->config->text)) { // settings od blocka
		    $this->content->text .= $this->config->text;
		}

		// if you dont want the block to be displayed, $this->content should be equal to the empty string ('')

		//$this->content->footer = 'Dakle footer';







		// INCLUDE JS AND PASS PARAMETERS
		// ===============================================
		// language strings to pass to module.js
		//$this->page->requires->string_for_js('saved', 'block_accessibility');
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
		$attrs = array('class' => 'bookmarks_creation');
		$content .= html_writer::start_tag('div', $attrs);

			$content .= html_writer::tag('h4', '???? INSERT NEW BOOKMARK', $attrs);

			// BOOKMARK TITLE TEXT FIELD
			$attrs = array(
				'class' => 'fld_bookmarkTitle', 
				'type' => 'text',
				'placeholder' => '???? Unesite naziv i pritisnite enter'
			);
			$content .= html_writer::empty_tag('input', $attrs);

			// BOOKMARK INSERTION BUTTON
			$attrs = array(
				'class' => 'btn_storeSelection',
				'type' => 'button',
				'value' => '???? DUGME'
			);
			$content .= html_writer::empty_tag('input', $attrs);

			// BOOKMARK CREATION STATUS MESSAGE
			$attrs = array('class' => 'btn_backToChapter');
			$content .= html_writer::tag('a', '???? TEEEST', $attrs);

			// BOOKMARK CREATION INSTRUCTIONS
			$content .= html_writer::tag('div', '???? INSTRUCTIONS');


		$content .= html_writer::end_tag('div');

		$this->content->text = $content;


		
		return $this->content;
	}

	// za dozvoliti više instanci istog modula unutar istog course-a
	/*public function instance_allow_multiple() {
	  return true;
	}*/

	// Since version 2.4, the following line must be added to the /blocks/simplehtml/block_simplehtml.php file in order to enable global configuration:
	//function has_config() {return true;}


	/*public function hide_header() {
	  return true;
	}*/


	// presretanje lokalnih i globalnih postavki?
	/*public function instance_config_save($data) {

		// moze i ovako $allowHTML = $CFG->Allow_HTML;

	  if(get_config('bookmarks', 'Allow_HTML') == '1') {
	    $data->text = strip_tags($data->text);
	  }
	 
	  // And now forward to the default implementation defined in the parent class
	  return parent::instance_config_save($data);
	}*/


}

