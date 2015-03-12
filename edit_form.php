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
 * This settings will be available globally throughout the block.
 *
 * Usage example: $this->config->test_warning_enabled
 *
 * @package    block_bookmarks
 * @copyright  Copyright 2013 onwards University of Split, Faculty of Economics 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_bookmarks_edit_form extends block_edit_form {
	protected function specific_definition($mform) {	


		// allow warning about 'beta test phase' block
		$config_test_warning = "config_test_warning_enabled";
		$block_name = 'block_bookmarks';
		$mform->addElement('advcheckbox', $config_test_warning,
			get_string ($config_test_warning, $block_name),
			get_string ($config_test_warning.'_checkbox', $block_name),
			null,
			array (0, 1)
		);
		$mform->setDefault($config_test_warning, 1);
		$mform->setType ($config_test_warning, PARAM_INT);
		$mform->addHelpButton($config_test_warning, $config_test_warning, $block_name);		
		
	}
}