<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$student_cache_id = function(&$CI) {
    return $CI->usermanager->get_student_simple_cache_id();
};
$no_student_id = function(&$CI) { return NULL; };
$admin_student_cache_id = function(&$CI) {
    return $CI->usermanager->get_student_simple_cache_id($CI->output->get_internal_value('student_id'));
};
$course_cache_id = function(&$CI) {
    return 'course_' . $CI->output->get_internal_value('course_id');
};

$hook['post_controller'] = array(
    'class' => 'Clear_Cache',
    'function' => 'execute',
    'filename' => 'clear_cache.php',
    'filepath' => 'hooks',
    'params' => array(
        '' => array(
            'students' => array(
                'save_basic_information' => array(
                    'frontend/*' => $student_cache_id,
                ), 
                'save_email' => array(
                    'frontend/students/my_account.tpl' => $student_cache_id,
                ), 
                'delete_avatar' => array(
                    'frontend/students/my_account.tpl' => $student_cache_id,
                ),
                'save_avatar' => array(
                    'frontend/students/my_account.tpl' => $student_cache_id,
                ),
            ),
            'courses' => array(
                'activate_course' => array(
                    'frontend/courses/index.tpl' => $student_cache_id,
                    'frontend/groups/index.tpl' => $student_cache_id,
                    'frontend/tasks/index.tpl' => $student_cache_id,
                    'frontend/projects/*' => $student_cache_id,
                ),
                'quick_course_change' => array(
                    'frontend/courses/index.tpl' => $student_cache_id,
                    'frontend/groups/index.tpl' => $student_cache_id,
                    'frontend/tasks/index.tpl' => $student_cache_id,
                    'frontend/projects/*' => $student_cache_id,
                ),
                'signup_to_course' => array(
                    'frontend/courses/index.tpl' => $student_cache_id,
                ),
            ),
            'groups' => array(
                'select_group' => array(
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                    'frontend/groups/index.tpl' => $student_cache_id,
                    'frontend/tasks/*' => $student_cache_id,
                    'frontend/projects/*' => $student_cache_id,
                ),
            ),
            'tasks' => array(
                'upload_solution' => array(
                    'frontend/tasks/index.tpl' => $student_cache_id,
                    'frontend/tasks/task.tpl' => function(&$CI) {
                        return $CI->usermanager->get_student_cache_id('task_set_' . $CI->output->get_internal_value('task_set_id'));
                    },
                ),
                'reset_task_cache' => array(
                    'frontend/tasks/task.tpl' => function(&$CI) {
                        return $CI->usermanager->get_student_cache_id('task_set_' . $CI->output->get_internal_value('task_set_id'));
                    },
                ),
            ),
            'fetests' => array(
                'evaluate_test_result' => array(
                    'frontend/tasks/index.tpl' => $student_cache_id,
                ),
            ),
            'projects' => array(
                'select_project' => array(
                    'frontend/projects/index.tpl' => $student_cache_id,
                    'frontend/projects/selection.tpl' => $no_student_id,
                    'frontend/projects/task.tpl' => $no_student_id,
                    'frontend/projects/overview.tpl' => $no_student_id,
                ),
                'reset_task_cache' => array(
                    'frontend/projects/task.tpl' => function($CI) {
                        return $CI->usermanager->get_student_cache_id('task_set_' . $CI->output->get_internal_value('task_set_id') . '|task_' . $CI->output->get_internal_value('task_id'));
                    },
                    'frontend/projects/overview.tpl' => $no_student_id,
                ),
                'upload_solution' => array(
                    'frontend/projects/index.tpl' => $student_cache_id,
                    'frontend/projects/task.tpl' => function($CI) {
                        return $CI->usermanager->get_student_cache_id('task_set_' . $CI->output->get_internal_value('task_set_id'));
                    },
                ),
            ),
        ),
        'admin/' => array(
            'periods' => array(
                'create' => array(
                    'frontend/*' => $no_student_id,
                ),
                'update' => array(
                    'frontend/*' => $no_student_id,
                ),
                'delete' => array(
                    'frontend/*' => $no_student_id,
                ),
                'move_up' => array(
                    'frontend/*' => $no_student_id,
                ),
                'move_down' => array(
                    'frontend/*' => $no_student_id,
                ),
            ),
            'courses' => array(
                'create' => array(
                    'frontend/*' => $no_student_id,
                ),
                'update' => array(
                    'frontend/*' => $no_student_id,
                ),
                'delete' => array(
                    'frontend/*' => $no_student_id,
                ),
            ),
            'groups' => array(
                'create' => array(
                    'frontend/groups/*' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ),
                'update' => array(
                    'frontend/groups/*' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ),
                'delete' => array(
                    'frontend/groups/*' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ),
            ),
            'rooms' => array(
                'create' => array(
                    'frontend/groups/*' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ),
                'update' => array(
                    'frontend/groups/*' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ),
                'delete' => array(
                    'frontend/groups/*' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ),
            ),
            'participants' => array(
                'approve_participation' => array(
                    'frontend/*' => $admin_student_cache_id,
                ),
                'disapprove_participation' => array(
                    'frontend/*' => $admin_student_cache_id,
                ),
                'delete_participation' => array(
                    'frontend/*' => $admin_student_cache_id,
                ),
                'add_participant' => array(
                    'frontend/*' => $no_student_id,
                ),
                'change_group' => array(
                    'frontend/*' => $admin_student_cache_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ),
            ),
            'tasks' => array(
                'update' => array(
                    'frontend/tasks/*' => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ),
                'delete' => array(
                    'frontend/tasks/*' => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ),
                'insert_to_task_set' => array(
                    'frontend/tasks/*' => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ),
            ),
            'task_set_types' => array(
                'update' => array(
                    'frontend/tasks/*' => $no_student_id,
                ),
                'delete' => array(
                    'frontend/tasks/*' => $no_student_id,
                ),
            ),
            'task_sets' => array(
                'create' => array(
                    'frontend/tasks/*' => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ),
                'update' => array(
                    'frontend/tasks/*' => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ),
                'delete' => array(
                    'frontend/tasks/*' => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ),
                'select_project' => array(
                    'frontend/projects/*' => $no_student_id,
                ),
            ),
            'task_set_permissions' => array(
                'create_permission' => array(
                    'frontend/tasks/*' => $no_student_id,
                ),
                'update_permission' => array(
                    'frontend/tasks/*' => $no_student_id,
                ),
                'delete_permission' => array(
                    'frontend/tasks/*' => $no_student_id,
                ),
            ),
            'tests' => array(
                'save_test_configuration' => array(
                    'frontend/tasks/*' => $no_student_id,
                ),
                'prepare_new_test' => array(
                    'frontend/tasks/*' => $no_student_id,
                ),
            ),
            'solutions' => array(
                'batch_save_solutions' => array(
                    'frontend/tasks/index.tpl' => $no_student_id,
                    'frontend/projects/index.tpl' => $no_student_id,
                ),
                'create_solution' => array(
                    'frontend/tasks/index.tpl' => $admin_student_cache_id,
                    'frontend/projects/index.tpl' => $admin_student_cache_id,
                ),
                'do_upload_student_solution' => array(
                    'frontend/tasks/*' => $admin_student_cache_id,
                    'frontend/projects/*' => $admin_student_cache_id,
                ),
                'remove_points' => array(
                    'frontend/tasks/index.tpl' => $no_student_id,
                    'frontend/projects/index.tpl' => $no_student_id,
                ),
                'update_valuation' => array(
                    'frontend/tasks/index.tpl' => $admin_student_cache_id,
                    'frontend/projects/index.tpl' => $admin_student_cache_id,
                ),
                'solution_version_switch_download_lock' => array(
                    'frontend/tasks/task.tpl' => $admin_student_cache_id,
                    'frontend/projects/task.tpl' => $admin_student_cache_id,
                ),
            ),
            'teachers' => array(
                'update_teacher' => array(
                    'frontend/tasks/index.tpl' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $no_student_id,
                    'frontend/projects/index.tpl' => $no_student_id,
                    'frontend/projects/overview.tpl' => $no_student_id,
                ),
                'delete_teacher' => array(
                    'frontend/tasks/index.tpl' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $no_student_id,
                    'frontend/projects/index.tpl' => $no_student_id,
                    'frontend/projects/overview.tpl' => $no_student_id,
                ),
                'save_basic_information' => array(
                    'frontend/tasks/index.tpl' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $no_student_id,
                    'frontend/projects/index.tpl' => $no_student_id,
                    'frontend/projects/overview.tpl' => $no_student_id,
                ),
                'save_email' => array(
                    'frontend/tasks/index.tpl' => $no_student_id,
                    'frontend/courses/course_details.tpl' => $no_student_id,
                    'frontend/projects/index.tpl' => $no_student_id,
                    'frontend/projects/overview.tpl' => $no_student_id,
                ),
            ),
        ),
    ),
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */