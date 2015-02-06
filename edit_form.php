<?php

// This settings will be available throughout the block.
// Usage example: $this->config->test_warning_enabled

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