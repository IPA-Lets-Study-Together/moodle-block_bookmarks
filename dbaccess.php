<?php
// this is used for AJAX dbaccess

require_once('../../config.php');
//require_once($CFG->dirroot.'/blocks/accessibility/lib.php');
require_login();

// TO-DO: Check the HTTP referrer here! It must be called from block

$op = required_param('op', PARAM_TEXT);

switch ($op) {
    case 'insert':
    	// TO-DO: validate arguments
    	// TO-DO: security check and preprocessing to prevent db attacks
 
    	array_shift($_POST);
        $db_data = $_POST;
        $db_data['userid'] = $USER->id;
        $db_data['chapterid'] = $SESSION->chapterid; // from block
        $db_data['date'] = time(); // UNIX timestamp
        echo ($DB->insert_record('block_bookmarks', $db_data));
    
    
        break;
    default:
        header("HTTP/1.0 400 Bad Request");
}
