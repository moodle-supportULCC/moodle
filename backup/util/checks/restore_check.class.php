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
 * @package    moodlecore
 * @subpackage backup-factories
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing different restore checks
 *
 * This class contains various static methods available in order to easily
 * perform a bunch of restore architecture tests
 *
 * TODO: Finish phpdocs
 */
abstract class restore_check {

    public static function check_courseid($courseid) {
        global $DB;
        // id must exist in course table
        if (! $DB->record_exists('course', array('id' => $courseid))) {
            throw new restore_controller_exception('restore_check_course_not_exists', $courseid);
        }
        return true;
    }

    public static function check_user($userid) {
        global $DB;
        // userid must exist in user table
        if (! $DB->record_exists('user', array('id' => $userid))) {
            throw new restore_controller_exception('restore_check_user_not_exists', $userid);
        }
        return true;
    }

    public static function check_security($restore_controller, $apply) {
        global $DB;

        if (! $restore_controller instanceof restore_controller) {
            throw new restore_controller_exception('restore_check_security_requires_restore_controller');
        }
        $restore_controller->log('checking plan security', backup::LOG_INFO);

        // Some handy vars
        $type     = $restore_controller->get_type();
        $mode     = $restore_controller->get_mode();
        $courseid = $restore_controller->get_courseid();
        $coursectx= get_context_instance(CONTEXT_COURSE, $courseid);
        $userid   = $restore_controller->get_userid();

        // Note: all the checks along the function MUST be performed for $userid, that
        // is the user who "requested" the course restore, not current $USER at all!!

        // First of all, check the main restore[course|section|activity] principal caps
        // Lacking the corresponding one makes this to break with exception always
        switch ($type) {
            case backup::TYPE_1COURSE :
                if (!has_capability('moodle/restore:restorecourse', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/restore:restorecourse';
                    throw new restore_controller_exception('restore_user_missing_capability', $a);
                }
                break;
            case backup::TYPE_1SECTION :
                if (!has_capability('moodle/restore:restoresection', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/restore:restoresection';
                    throw new restore_controller_exception('restore_user_missing_capability', $a);
                }
                break;
            case backup::TYPE_1ACTIVITY :
                if (!has_capability('moodle/restore:restoreactivity', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/restore:restoreactivity';
                    throw new restore_controller_exception('restore_user_missing_capability', $a);
                }
                break;
            default :
                print_error('unknownrestoretype');
        }

        // Now, if restore mode is hub or import, check userid has permissions for those modes
        switch ($mode) {
            case backup::MODE_HUB:
                if (!has_capability('moodle/restore:restoretargethub', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/restore:restoretargethub';
                    throw new restore_controller_exception('restore_user_missing_capability', $a);
                }
                break;
            case backup::MODE_IMPORT:
                if (!has_capability('moodle/restore:restoretargetimport', $coursectx, $userid)) {
                    $a = new stdclass();
                    $a->userid = $userid;
                    $a->courseid = $courseid;
                    $a->capability = 'moodle/restore:restoretargetimport';
                    throw new restore_controller_exception('restore_user_missing_capability', $a);
                }
                break;
        }

        // Now, enforce 'moodle/restore:userinfo' to 'users' setting, applying changes if allowed,
        // else throwing exception
        $userssetting = $restore_controller->get_plan()->get_setting('users');
        $prevvalue    = $userssetting->get_value();
        $prevstatus   = $userssetting->get_status();
        $hasusercap   = has_capability('moodle/restore:userinfo', $coursectx, $userid);

        // If setting is enabled but user lacks permission
        if (!$hasusercap && $prevvalue) { // If user has not the capability and setting is enabled
            // Now analyse if we are allowed to apply changes or must stop with exception
            if (!$apply) { // Cannot apply changes, throw exception
                $a = new stdclass();
                $a->setting = 'users';
                $a->value = $prevvalue;
                $a->capability = 'moodle/restore:userinfo';
                throw new restore_controller_exception('restore_setting_value_wrong_for_capability', $a);

            } else { // Can apply changes
                $userssetting->set_value(false);                              // Set the value to false
                $userssetting->set_status(base_setting::LOCKED_BY_PERMISSION);// Set the status to locked by perm
            }
        }

        // Now, if mode is HUB or IMPORT, and still we are including users in restore, turn them off
        // Defaults processing should have handled this, but we need to be 100% sure
        if ($mode == backup::MODE_IMPORT || $mode == backup::MODE_HUB) {
            $userssetting = $restore_controller->get_plan()->get_setting('users');
            if ($userssetting->get_value()) {
                $userssetting->set_value(false);                              // Set the value to false
                $userssetting->set_status(base_setting::LOCKED_BY_PERMISSION);// Set the status to locked by perm
            }
        }

        return true;
    }
}