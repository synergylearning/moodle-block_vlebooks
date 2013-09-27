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
 * Main code for block vlebooks
 *
 * @package   block_modules_dashboard
 * @copyright 2013 Andreas Wagner, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    // ...installation test of mcrypt.
    if (!function_exists('mcrypt_encrypt')) {
        $settingsdesc = new lang_string('mycryptnotinstalled', 'block_vlebooks');
    } else {
        $settingsdesc = new lang_string('settingsdesc', 'block_vlebooks');
    }

    $settings->add(new admin_setting_heading('block_vlebooks/settings',
                    new lang_string('settings', 'block_vlebooks'),
                    $settingsdesc));

    $settings->add(new admin_setting_configtext('block_vlebooks/loginurl',
                    new lang_string('loginurl', 'block_vlebooks'),
                    new lang_string('loginurldesc', 'block_vlebooks'),
                    'http://www.vlebooks.com/vleweb/', PARAM_TEXT, 80));

    $settings->add(new admin_setting_configtext('block_vlebooks/id',
                    new lang_string('id', 'block_vlebooks'),
                    new lang_string('iddesc', 'block_vlebooks'),
                    '', PARAM_TEXT, 30));

    $settings->add(new admin_setting_configtext('block_vlebooks/accId',
                    new lang_string('accId', 'block_vlebooks'),
                    new lang_string('accIddesc', 'block_vlebooks'),
                    '', PARAM_TEXT, 30));

}