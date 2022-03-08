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
 * Local functions.
 *
 * @package    report_coursesize
 * @copyright  2022 Catalyst IT {@link http://www.catalyst.net.nz}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Get sql snippet for course filesizes.
 * @return string
 */
function local_coursesize_filesize_sql() {
    $sqlunion = "UNION ALL
                    SELECT c.id, f.filesize
                    FROM {block_instances} bi
                    JOIN {context} cx1 ON cx1.contextlevel = ".CONTEXT_BLOCK. " AND cx1.instanceid = bi.id
                    JOIN {context} cx2 ON cx2.contextlevel = ". CONTEXT_COURSE. " AND cx2.id = bi.parentcontextid
                    JOIN {course} c ON c.id = cx2.instanceid
                    JOIN {files} f ON f.contextid = cx1.id
                UNION ALL
                    SELECT c.id, f.filesize
                    FROM {course_modules} cm
                    JOIN {context} cx ON cx.contextlevel = ".CONTEXT_MODULE." AND cx.instanceid = cm.id
                    JOIN {course} c ON c.id = cm.course
                    JOIN {files} f ON f.contextid = cx.id";

    return "SELECT id AS course, SUM(filesize) AS filesize
              FROM (SELECT c.id, f.filesize
                      FROM {course} c
                      JOIN {context} cx ON cx.contextlevel = ".CONTEXT_COURSE." AND cx.instanceid = c.id
                      JOIN {files} f ON f.contextid = cx.id {$sqlunion}) x
                GROUP BY id";
}

/**
 * Get sql snippet for backup filesizes.
 * @return string
 */
function local_coursesize_backupsize_sql() {
    return "SELECT id AS course, SUM(filesize) AS filesize
              FROM (SELECT c.id, f.filesize
                      FROM {course} c
                      JOIN {context} cx ON cx.contextlevel = ".CONTEXT_COURSE." AND cx.instanceid = c.id
                      JOIN {files} f ON f.contextid = cx.id AND f.component = 'backup') x
            GROUP BY id";
}