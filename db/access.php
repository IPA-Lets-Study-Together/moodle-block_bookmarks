<?php

defined('MOODLE_INTERNAL') || die();
$capabilities = array(
    // New standard capability 'addinstance'.
    'block/bookmarks:addinstance' => array(
        'captype'       => 'write',
        'contextlevel'  => CONTEXT_COURSE,
        'archetypes'    => array(
            'editingteacher'    => CAP_ALLOW,
            'manager'           => CAP_ALLOW
        ),
        'clonepermissionsfrom'  => 'moodle/site:manageblocks'
    ),
    'block/bookmarks:myaddinstance' => array(
      'riskbitmask'  => RISK_PERSONAL,
      'captype'      => 'read',
      'contextlevel' => CONTEXT_SYSTEM,
      'archetypes'   => array(
        'user' => CAP_ALLOW,
      ),
      'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),
);