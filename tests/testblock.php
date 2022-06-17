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
 * Moodle test block tests.
 *
 * @package    block_moodle_testblock
 * @category   test
 * @copyright  2022, Anup Ranekar <anup.ranekar@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Test block class.
 *
 * @package    block_moodle_testblock
 * @category   test
 * @copyright  2022, Anup Ranekar <anup.ranekar@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_moodle_testblock_testcase extends basic_testcase {
    /** @var stdClass Keeps course object */
    private $course;

    /** @var stdClass Keeps assign object */
    private $assign;

    /** @var stdClass Keeps quiz object */
    private $quiz;

    /**
     * Setup test data.
     */
    public function setUp(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and activities.
        $this->course = $this->getDataGenerator()->create_course();
        $this->assign = $this->getDataGenerator()->create_module('assign', array('course' => $this->course->id, 'name' => 'Test1'));
        $this->quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $this->course->id, 'name' => 'Test2'));
    }

    /**
     * Test block content.
     */
    public function test_content() {
        global $CFG, $USER;

        $datum = array();
        $modinfo = get_fast_modinfo($this->course->id, $USER->id);
        $coursemodules = $modinfo->get_cms();
        $completion = new completion_info($this->course);
        // Prepare data.
        foreach ($coursemodules as $coursemodule) {
            if ($coursemodule->visible) {
                $type = $coursemodule->modname;
                $modurl = new moodle_url("/mod/$type/view.php", array('id' => $coursemodule->id));
                $completiondata = $completion->get_data($coursemodule, false, $USER->id);
                array_push(
                    $datum,
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

        // Check data.
        $this->assertEquals($datum[0]['cmid'], 1);
        $this->assertEquals($datum[0]['act_name'], 'Test1');
        $url1 = new moodle_url("/mod/assign/view.php", array('id' => 1));
        $this->assertEquals($datum[0]['act_url'], $url1->out());
        $this->assertEquals($datum[0]['creationdate'], date('d-M-Y'));
        $this->assertEquals($datum[0]['show_completion_status'], 0);

        $this->assertEquals($datum[1]['cmid'], 2);
        $this->assertEquals($datum[1]['act_name'], 'Test2');
        $url2 = new moodle_url("/mod/quiz/view.php", array('id' => 2));
        $this->assertEquals($datum[1]['act_url'], $url2->out());
        $this->assertEquals($datum[1]['creationdate'], date('d-M-Y'));
        $this->assertEquals($datum[1]['show_completion_status'], 0);
    }
}
