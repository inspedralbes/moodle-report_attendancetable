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
 * Renderables file for Attendance table plugin
 *
 * @package   report_attendancetable
 * @copyright 2022, Hasan Abuzoor <a21hasabuabu@inspedralbes.cat> <hassan.abuzoor66@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/mod/attendance/locallib.php');

/**
 * Represents users attendance data and attendance instances for each course.
 *
 * @copyright  2022, Hasan Abuzoor <a21hasabuabu@inspedralbes.cat> <hassan.abuzoor66@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attendancetable_print_table implements renderable {
    /** @var array  of attendance instances in each course*/
    public $attendancespercourse;
    /** @var array  of attendance instances stats for each user*/
    public $data;
    /** @var int  id of the first attendance instance of the actual course*/
    public $idatt;

    /**
     * Gets all data and saves them into the class attributes.
     *
     * @param stdclass $attptable - Save all data
     * @param mod_attendance_structure $att - Get data from attendance module
     * @param context_module $context - The context of the attendace module
     * @param context $contextcourse - The context of the actual course
     * @param int $idatt - The id of the first attendance instance found in a course
     * @return string html code
     */
    public function __construct(stdclass $attptable, mod_attendance_structure $att,
        context_module $context, context $contextcourse, $idatt) {
        $users = get_enrolled_users($context, '');
        $data = [];
        $idsattencourse = [];

        foreach ($users as $user) {
            global $USER, $DB;
            $roles = get_user_roles($contextcourse, $user->id, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;
            if (
                has_capability('mod/attendance:canbelisted', $context, null, false) &&
                has_capability('mod/attendance:view', $context)
            ) {
                if ($rolename == 'student') {
                    $userdata = new attendance_user_data($att, $user->id);
                    if ($userdata->user->id == $USER->id) {
                        $totalattendance = 0;
                        $totalpercentage = 0;
                        $totalstats = [];

                        foreach ($userdata->coursesatts as $ca) {
                            $usersummary = new stdClass();
                            $userattsummary = new mod_attendance_summary($ca->attid, $user->id);
                            $userstats = isset($userattsummary->get_taken_sessions_summary_for($user->id)
                                ->userstakensessionsbyacronym[0]) ? $userattsummary->get_taken_sessions_summary_for($user->id)
                                ->userstakensessionsbyacronym[0] : null;
                            $selectstatus = "SELECT * FROM mdl_attendance_statuses WHERE attendanceid = {$ca->attid};";
                            $attstatusresult = $DB->get_records_sql($selectstatus);
                            $acronyms = [];
                            foreach ($attstatusresult as $status) {
                                array_push($acronyms, $status->acronym);
                            }
                            $totalstats['P'] += isset($userstats[$acronyms[0]]) ? $userstats[$acronyms[0]] : 0;
                            $totalstats['A'] += isset($userstats[$acronyms[1]]) ? $userstats[$acronyms[1]] : 0;
                            $totalstats['L'] += isset($userstats[$acronyms[2]]) ? $userstats[$acronyms[2]] : 0;
                            $totalstats['E'] += isset($userstats[$acronyms[3]]) ? $userstats[$acronyms[3]] : 0;

                            $userstats['P'] = isset($userstats[$acronyms[0]]) ? $userstats[$acronyms[0]] : 0;
                            $userstats['A'] = isset($userstats[$acronyms[1]]) ? $userstats[$acronyms[1]] : 0;
                            $userstats['L'] = isset($userstats[$acronyms[2]]) ? $userstats[$acronyms[2]] : 0;
                            $userstats['E'] = isset($userstats[$acronyms[3]]) ? $userstats[$acronyms[3]] : 0;

                            if (isset($userdata->summary[$ca->attid])) {
                                $usersummary = $userdata->summary[$ca->attid]->get_all_sessions_summary_for($userdata->user->id);
                            }

                            if ($usersummary->numtakensessions > 0) {
                                $totalattendance++;
                                $totalpercentage = $totalpercentage + $usersummary->takensessionspercentage * 100;
                            }

                            $absent = get_string('Aacronym', 'mod_attendance');
                            $present = get_string('Pacronym', 'mod_attendance');
                            $late = get_string('Lacronym', 'mod_attendance');
                            $excused = get_string('Eacronym', 'mod_attendance');

                            $course = get_course($ca->courseid);

                            $presentaverage = (format_float($usersummary->takensessionspercentage * 100) . '%' );
                            $data[$user->id][$course->shortname][$ca->attname] =
                                ['stats' => $userstats, 'average' => $presentaverage];
                            $idsattencourse[$course->shortname][$ca->attname] = '-';
                            $course = get_course($ca->courseid);

                        }

                        if (empty($totalattendance)) {
                            $average = '-';
                        } else {
                            $average = format_float($totalpercentage / $totalattendance) . '%';
                        }
                        $data[$user->id]['total'] = ['stats' => $totalstats, 'average' => $average];
                    }
                }
            } else {
                if ($rolename == 'student') {
                    $userdata = new attendance_user_data($att, $user->id);

                    $totalattendance = 0;
                    $totalpercentage = 0;
                    $totalstats = ['P' => 0, 'A' => 0, 'T' => 0, 'J' => 0];

                    foreach ($userdata->coursesatts as $ca) {
                        $usersummary = new stdClass();
                        $userattsummary = new mod_attendance_summary($ca->attid, $user->id);
                        $userstats = isset($userattsummary->get_taken_sessions_summary_for($user->id)
                            ->userstakensessionsbyacronym[0]) ? $userattsummary->get_taken_sessions_summary_for($user->id)
                            ->userstakensessionsbyacronym[0] : null;

                        $selectstatus = "SELECT * FROM mdl_attendance_statuses WHERE attendanceid = {$ca->attid};";
                        $attstatusresult = $DB->get_records_sql($selectstatus);
                        $acronyms = [];
                        foreach ($attstatusresult as $status) {
                            array_push($acronyms, $status->acronym);
                        }
                        $totalstats['P'] += isset($userstats[$acronyms[0]]) ? $userstats[$acronyms[0]] : 0;
                        $totalstats['A'] += isset($userstats[$acronyms[1]]) ? $userstats[$acronyms[1]] : 0;
                        $totalstats['L'] += isset($userstats[$acronyms[2]]) ? $userstats[$acronyms[2]] : 0;
                        $totalstats['E'] += isset($userstats[$acronyms[3]]) ? $userstats[$acronyms[3]] : 0;

                        $userstats['P'] = isset($userstats[$acronyms[0]]) ? $userstats[$acronyms[0]] : 0;
                        $userstats['A'] = isset($userstats[$acronyms[1]]) ? $userstats[$acronyms[1]] : 0;
                        $userstats['L'] = isset($userstats[$acronyms[2]]) ? $userstats[$acronyms[2]] : 0;
                        $userstats['E'] = isset($userstats[$acronyms[3]]) ? $userstats[$acronyms[3]] : 0;

                        if (isset($userdata->summary[$ca->attid])) {
                            $usersummary = $userdata->summary[$ca->attid]->get_all_sessions_summary_for($userdata->user->id);
                        }

                        if ($usersummary->numtakensessions > 0) {
                            $totalattendance++;
                            $totalpercentage = $totalpercentage + $usersummary->takensessionspercentage * 100;
                        }

                        $absent = get_string('Aacronym', 'mod_attendance');
                        $present = get_string('Pacronym', 'mod_attendance');
                        $late = get_string('Lacronym', 'mod_attendance');
                        $excused = get_string('Eacronym', 'mod_attendance');

                        $course = get_course($ca->courseid);

                        $presentaverage = (format_float($usersummary->takensessionspercentage * 100) . '%' );
                        $data[$user->id][$course->shortname][$ca->attname] = ['stats' => $userstats, 'average' => $presentaverage];
                        $idsattencourse[$course->shortname][$ca->attname] = '-';
                        $course = get_course($ca->courseid);
                    }

                    if (empty($totalattendance)) {
                        $average = '-';
                    } else {
                        $average = format_float($totalpercentage / $totalattendance) . '%';
                    }
                        $data[$user->id]['total'] = ['stats' => $totalstats, 'average' => $average];
                }
            }
        }

        $this->attendancespercourse = $idsattencourse;
        $this->data = $data;
        $this->idatt = $idatt;
    }
}
