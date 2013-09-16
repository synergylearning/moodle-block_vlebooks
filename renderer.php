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
 * @package   block_vlebooks
 * @copyright 2013 Andreas Wagner, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/blocks/vlebooks/lib.php');

/** renderer for block vlebooks, you may override this renderer in themes */
class block_vlebooks_renderer extends plugin_renderer_base {

    public function content() {
        global $CFG;

        $strclickhere = get_string('clickheretologin', 'block_vlebooks');

        $attributes = array(
            'src' => $CFG->wwwroot . '/blocks/vlebooks/pix/logo-170.png',
            'alt' => get_string('logoalt', 'block_vlebooks'),
            'title' => $strclickhere, 'class' => 'vlebooks-logo');

        // ...get the image html output first.
        $image = html_writer::empty_tag('img', $attributes);

        // ...get the text.
        $text = html_writer::tag('div', $strclickhere);
        return html_writer::link(block_vlebooks_get_url(), $image . $text, array('target' => '_blank'));
    }
}