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
 * Renderer file for Attendance table plugin
 *
 * @package   report_attendancetable
 * @copyright 2022, Hasan Abuzoor <a21hasabuabu@inspedralbes.cat> <hassan.abuzoor66@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/renderables.php');

/**
 * Attendance table plugin renderer class.
 *
 * @copyright 2022, Hasan Abuzoor <a21hasabuabu@inspedralbes.cat> <hassan.abuzoor66@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_attendancetable_renderer extends plugin_renderer_base {

    /**
     * Renders a users attendance table.
     *
     * @param attendancetable_print_table $attptable - user data
     * @return string html code
     */
    protected function render_attendancetable_print_table(attendancetable_print_table $attptable) {
        global $CFG;

        if (count($attptable->data) == 0) {
            return html_writer::nonempty_tag('p', get_string('no_users', 'report_attendancetable'));
        } else {
            $out = $this->output->heading(format_string(get_string("title", "report_attendancetable")), 1);

            $table = new html_table();
            $head = new stdClass();

            $head->cells[] = get_string('user_head', 'report_attendancetable');
            $head->cells[] = get_string('all_courses_head', 'report_attendancetable');

            foreach ($attptable->attendancespercourse as $curs => $content) {
                foreach ($content as $att => $guion) {
                    $head->cells[] = $att;
                }
            }

            $table->attributes['border'] = 1;
            $table->id = get_string('tableid', 'report_attendancetable');
            $table->head = $head->cells;

            $rows = new html_table_row();

            $rows->cells[] = '------';
            $cell = new html_table_cell();
                        $cell = html_writer::start_div('grid');
                        $cell .= html_writer::div("Pre", 'precentage');
                        $cell .= html_writer::div(get_string('Pacronym', 'mod_attendance'), 'present');
                        $cell .= html_writer::div(get_string('Lacronym', 'mod_attendance'), 'late');
                        $cell .= html_writer::div(get_string('Eacronym', 'mod_attendance'), 'excused');
                        $cell .= html_writer::end_div();
            $rows->cells[] = $cell;
            $table->data[] = $rows;
            foreach ($attptable->attendancespercourse as $curs => $content) {
                foreach ($content as $att => $guion) {
                    $cell = new html_table_cell();
                        $cell = html_writer::start_div('grid');
                        $cell .= html_writer::div(get_string('Aacronym', 'mod_attendance'), 'absent');
                        $cell .= html_writer::div(get_string('Pacronym', 'mod_attendance'), 'present');
                        $cell .= html_writer::div(get_string('Lacronym', 'mod_attendance'), 'late');
                        $cell .= html_writer::div(get_string('Eacronym', 'mod_attendance'), 'excused');
                        $cell .= html_writer::end_div();
                        $rows->cells[] = $cell;
                }
            }

            foreach ($attptable->data as $user => $value) {
                $rows = new html_table_row();

                $rows->cells[] = html_writer::link("{$CFG->wwwroot}/mod/attendance/view.php?studentid={$user}
                    &mode=1&id={$attptable->idatt}", fullname(core_user::get_user($user)));
                $cell = new html_table_cell();
                $cell = html_writer::start_div('grid');
                $cell .= html_writer::div($attptable->data[$user]['total']['stats']['A'], 'main');
                $cell .= html_writer::div($attptable->data[$user]['total']['average'], 'prec');
                $cell .= html_writer::div($attptable->data[$user]['total']['stats']['P'], 'present');
                $cell .= html_writer::div($attptable->data[$user]['total']['stats']['T'], 'late');
                $cell .= html_writer::div($attptable->data[$user]['total']['stats']['J'], 'excused');
                $cell .= html_writer::end_div();
                $rows->cells[] = $cell;
                $table->data[] = $rows;

                foreach ($attptable->attendancespercourse as $curs => $content) {
                    foreach ($content as $att => $guion) {
                        $cell = new html_table_cell();
                        if (isset($attptable->data[$user][$curs][$att])) {
                            $cell = html_writer::start_div('grid');
                            $cell .= html_writer::div($attptable->data[$user][$curs][$att]['average'], 'main');
                            $cell .= html_writer::div($attptable->data[$user][$curs][$att]['stats']['A'], 'absent');
                            $cell .= html_writer::div($attptable->data[$user][$curs][$att]['stats']['P'], 'present');
                            $cell .= html_writer::div($attptable->data[$user][$curs][$att]['stats']['T'], 'late');
                            $cell .= html_writer::div($attptable->data[$user][$curs][$att]['stats']['J'], 'excused');
                            $cell .= html_writer::end_div();
                            $rows->cells[] = $cell;
                        } else {
                            $rows->cells[] = '---';
                        }
                    }
                }
            }

            $absent = get_string('Pacronym', 'mod_attendance');
            $present = get_string('Aacronym', 'mod_attendance');
            $late = get_string('Lacronym', 'mod_attendance');
            $excused = get_string('Eacronym', 'mod_attendance');
            $fullabsent = get_string('Pfull', 'mod_attendance');
            $fullpresent = get_string('Afull', 'mod_attendance');
            $fulllate = get_string('Lfull', 'mod_attendance');
            $fullexcused = get_string('Efull', 'mod_attendance');

            $out .= html_writer::div(html_writer::table($table), '', ['id' => 'container']);
            $out .= html_writer::empty_tag('hr');
            $out .= html_writer::nonempty_tag('p', "{$present} = {$fullpresent} | {$absent} = {$fullabsent} |
                {$late} = {$fulllate} | {$excused} = {$fullexcused}");

            return $out;
        }
    }
}
