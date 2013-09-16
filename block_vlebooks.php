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
defined('MOODLE_INTERNAL') || die();

class block_vlebooks extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_vlebooks');
    }

    public function get_content() {

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isloggedin() or isguestuser()) {
            return '';      // Never useful unless you are logged in as real users
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $renderer = $this->page->get_renderer('block_vlebooks');
        $this->content->text .= $renderer->content();

        return $this->content;
    }

    /** hides the header, when there is no title given
     * 
     * @return boolean true when header should be hidden.
     */
    public function hide_header() {

        return (empty($this->config->title));
    }

    public function has_config() {

        return true;
    }

    public function specialization() {

        if (!empty($this->config->title)) {

            $this->title = $this->config->title;

        } else {

            $this->title = get_string('pluginname', 'block_vlebooks');
        }
    }

    public function applicable_formats() {

        return array('course' => true, 'site' => true, 'my-index' => true);
    }
}