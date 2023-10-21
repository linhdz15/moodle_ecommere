<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir . '/externallib.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->dirroot . '/local/th_ecommercelib/lib.php';
require_once $CFG->dirroot .'/local/th_ecommercelib/ripcord.php';

class local_th_ecommercelib_external extends external_api {

   /**
   * Returns description of method parameters
   * @return external_function_parameters
   */
    public static function update_course_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * update course
     * @param array $groups array of group description arrays (with keys groupname and courseid)
     * @return array of newly created groups
     */
    public static function update_course() {

        try {
            th_ecommercelib::init();
            $status = 1;
            $message = 'ok';
        } catch (Exception $e) {
             $status = 0;
             $message = $e->getMessage();
        }

        return array('status'=> $status, 'message' => $message);
    }
    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function update_course_returns() {

        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_INT, 'status', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_TEXT, 'message', VALUE_OPTIONAL),
            ), 'message'
        );
    }
}