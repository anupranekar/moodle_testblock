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
 * Block definition class for the block_moodle_testblock plugin.
 *
 * @package   block_moodle_testblock
 * @copyright 2022, Anup Ranekar <anup.ranekar@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_moodle_testblock extends block_base {

    /**
     * Initialises the block.
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_moodle_testblock');
    }

    /**
     * Gets the block contents.
     *
     * @return string The block HTML.
     */
    public function get_content() {
        global $OUTPUT, $DB, $COURSE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        $data = array();
        $modinfo = get_fast_modinfo($COURSE->id, $USER->id);
        $coursemodules = $modinfo->get_cms();
        $completion = new completion_info($COURSE);

        foreach ($coursemodules as $coursemodule) {
            if ($coursemodule->visible) {
                $type = $coursemodule->modname;
                $modurl = new moodle_url("/mod/$type/view.php", array('id' => $coursemodule->id));
                $completiondata = $completion->get_data($coursemodule, false, $USER->id);
                array_push(
                    $data,
                    array(
                        'cmid' => $coursemodule->id,
                        'act_name' => $coursemodule->name,
                        'act_url' => $modurl->out(),
                        'creation_date' => date('d-M-Y', $coursemodule->added),
                        'show_completion_status' => $completiondata->completionstate,
                    ),
                );
            }
        }

        $this->content->text = $OUTPUT->render_from_template('block_moodle_testblock/content', $data);

        return $this->content;
    }

    /**
     * Defines in which pages this block can be added.
     *
     * @return array of the pages where the block can be added.
     */
    public function applicable_formats() {
        return [
            'all' => false,
            'course-view' => true,
        ];
    }
}
