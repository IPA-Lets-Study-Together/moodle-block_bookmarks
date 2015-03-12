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

// MUST HAVE
$string['pluginname'] = 'Bookmarks (within book chapter)';
$string['bookmarks'] = 'Bookmarks'; // block title
$string['bookmarks:addinstance'] = 'Add a new Bookmarks block';
$string['bookmarks:myaddinstance'] = 'Add a new Bookmarks block to the My Moodle page';

// LISTING BOOKMARK ITEMS
$string['untitled-bkm-item'] = 'Untitled bookmark';
$string['user-bookmarks-title'] = 'User bookmarks';
$string['no-bookmarks'] = 'No user bookmarks exists in this chapter';
$string['bookmarks-editor'] = 'Bookmarks editor';
$string['aria-start-pin'] = 'A begining of the bookmark'; // beginning of the user bookmark xx
$string['aria-end-pin'] = 'The end of the bookmark. Press enter to jump back to the bookmarks list or continue to read the chapter'; // access to jump back to the list

// CREATING BOOKMARK ITEMS
$string['bookmarks-creation-title'] = 'Bookmark creation';
$string['enter-title'] = 'Enter the title and press enter';
$string['btn-add-bookmark'] = 'Add';
$string['creation-success'] = 'User bookmark is created. Press enter to jump back to the chapter';
$string['creation-instructions'] = 'To create a bookmark select the desired part of the chapter text, press <strong>Ctrl+Shift+Space</strong>, enter the title and press enter <strong>Enter</strong>';

// NOTES AND WARNINGS
$string['sr-note'] = 'Warning:';
$string['test-phase-note'] = 'This is a beta version of a block. Some functionality might not work properly. Please keep in mind that chapter text editing might cause user bookmarks to lose their correct positions within the text. The plugin works exclusively in modern web browsers with Javascript enabled support, including Internet Explorer 9 and higher';
$string['browser-unsupported'] = 'This browser is not supported. The plugin works exclusively in modern web browsers with Javascript enabled support, including Internet Explorer 9 and higher';
$string['no-js'] = 'The Javascript in your web browser is not enabled. Please enable Javascript or use different web browser';

// CONFIGURATION FORM
$string['config_test_warning_enabled'] = 'Beta version';
$string['config_test_warning_enabled_checkbox'] = 'Display "beta version" warning';
$string['config_test_warning_enabled_help'] = 'This is a beta version of a block. Some functionality might not work properly. Please keep in mind that chapter text editing might cause user bookmarks to lose their correct positions within the text. The plugin works exclusively in modern web browsers with Javascript enabled support, including Internet Explorer 9 and higher'; // NOT VALID FOR MOODLE: $string['test-phase-note'];

// EDITING BOOKMARKS
$string['editing_page_title'] = 'Bookmarks editor'; // NOT VALID FOR MOODLE: $string['bookmarks-editor'];