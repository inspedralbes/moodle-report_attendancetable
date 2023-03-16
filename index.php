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
 * index page for Attendance table plugin
 *
 * @package   report_attendancetable
 * @copyright 2022, Hasan Abuzoor <a21hasabuabu@inspedralbes.cat> <hassan.abuzoor66@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot . '/mod/attendance/locallib.php'); // Requires Attendance plugin to be installed.

$id = required_param('id', PARAM_INT); // Course id.

$attendanceparams = new mod_attendance_view_page_params(); // Page parameters, necessary to create mod_attendance_structure object.

$attendanceparams->studentid = null;
$attendanceparams->view = null;
$attendanceparams->curdate = null;
$attendanceparams->mode = 1;
$attendanceparams->groupby = 'course';
$attendanceparams->sesscourses = 'current';

// Requires at least one instance of attendance module to be inserted in the course.
$allattendances = get_coursemodules_in_course('attendance', $id);

if (count($allattendances) > 0) {
    // Get first attendance module instance found in this course.
    $firstattendance = $allattendances[array_keys($allattendances)[0]];

    $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
    $attendance = $DB->get_record('attendance', array('id' => $firstattendance->instance), '*', MUST_EXIST);

    require_login($course, true, $firstattendance);

    $context = context_module::instance($firstattendance->id);

    $attendanceparams->init($firstattendance);

    $attstructure = new mod_attendance_structure($attendance, $firstattendance, $course, $context, $attendanceparams);

    $output = $PAGE->get_renderer('report_attendancetable');
    $contextcourse = context_course::instance($id);
    $dataattendancetable = new stdclass();
    $printattendancetable = new attendancetable_print_table($dataattendancetable,
        $attstructure, $context, $contextcourse, $firstattendance->id);

    $url = new moodle_url("/report/attendancetable/index.php", array('id' => $id));

    $PAGE->set_url($url);
    $PAGE->requires->js('/report/attendancetable/script.js');
    $PAGE->requires->js_init_call('prova', array($printattendancetable->attendancespercourse,
        get_string('user_head', 'report_attendancetable'), get_string('all_courses_head', 'report_attendancetable')));

    echo $output->header();
    echo $output->render($printattendancetable);
    echo $output->footer();
} else {
    echo $OUTPUT->header();
    echo get_string('no_attendace', 'report_attendancetable');
    echo $OUTPUT->footer();
}
