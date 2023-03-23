Report Attendance Table
=======================
* Maintained by: Alexis Navas,  Hasan Abuzoor
* Copyright: 2023, Alexis Navas <a22alenavest@inspedralbes.cat>, Hasan Abuzoor <a21hasabuabu@inspedralbes.cat>
* License: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


Description
===========
Report attendance table is a plugin based on [Attendance](https://moodle.org/plugins/mod_attendance), used to show teachers their students'
attendance percentage and show each student their own attendance percentage, across all courses and sections with an Attendance activity.

Instructions
===========
Once mod_attendance is installed:

Manual download
---------------
1. Download the plugin
2. Copy its content to a folder called attendancetable inside your moodle/report
![Folder screenshot](/screenshots/report_folder.png)
3. As admin, go to site administration and follow the necessary steps to install the plugin
![Sidebar](/screenshots/sidebar.png)
![Report upgrade 1](/screenshots/upgrade.png)
![Report upgrade 2](/screenshots/plugin_upgrade.png)

Setting up the block
--------------------
0. Go to a course
1. Turn editing on
2. Add an Attendance activity to your course (if there's none yet)
3. Once Attendance is set up, click on the gear on the top right part of the course, then click on More
![Course gear](/screenshots/report_access.png)
4. Click on Attendance Table on Reports in the Course administration section
![Admin page](/screenshots/course_admin.png)
![Report view](/screenshots/report_view.png)

Recommended
===========
Download [Block Attendance Table](https://github.com/inspedralbes/moodle-block_attendancetable), which shows the lowest attendance students
and is more easily accessible for students.

Requirements
============
* 'mod_attendance'          =>  2021050702 [Attendance](https://moodle.org/plugins/mod_attendance)

Useful links
============
* [Moodle Forum](https://moodle.org/mod/forum/index.php?id=5)
* [Moodle Plugins Directory](https://docs.moodle.org/dev/Main_Page)
* [Block GitHub](https://github.com/inspedralbes/moodle-block_attendancetable)
* [Report GitHub](https://github.com/inspedralbes/moodle-report_attendancetable)