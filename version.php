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
 * Info for Attendance table plugin
 *
 * @package   report_attendancetable
 * @copyright 2022, Hasan Abuzoor <a21hasabuabu@inspedralbes.cat> <hassan.abuzoor66@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2023050800; // The current plugin version.
$plugin->requires = 2020061500; // Moodle version this plugin requires.
$plugin->component = 'report_attendancetable'; // Plugin name and type.
$plugin->dependencies = array('mod_attendance' => 2021050702); // Requires Attendance plugin.
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.1.0';
