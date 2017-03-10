<?php

if (!function_exists('get_task_set_timed_class')) {
    function get_task_set_timed_class($time, $uploads, $solutions) {
        if (is_null($uploads) || !$uploads) {
            return '';
        }

        if (!is_null($time) && trim($time) !== '') {
            if ((int)$solutions > 0) {
                if (strtotime($time) < date('U')) {
                    return 'task_set_time_after_deadline_with_submits';
                }
            } else {
                if (strtotime($time . ' +7 days') < date('U')) {
                    return 'task_set_time_long_after_deadline';
                }
                if (strtotime($time) < date('U')) {
                    return 'task_set_time_after_deadline';
                }
            }
            if (strtotime($time . ' -1 day') <= date('U')) {
                return 'task_set_time_day_before_deadline';
            }
            if (strtotime($time . ' -2 days') <= date('U')) {
                return 'task_set_time_two_days_before_deadline';
            }
            if (strtotime($time . ' -7 days') <= date('U')) {
                return 'task_set_time_week_before_deadline';
            }
        }
        return '';
    }
}

if (!function_exists('get_task_sets_color_legend')) {
    function get_task_sets_color_legend($admin = false) {
        $CI =& get_instance();

        $CI->parser->assign('admin', (bool)$admin);

        return $CI->parser->parse('partials/helper/task_sets_color_legend.tpl', array(), true, false);
    }
}