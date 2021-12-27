<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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

$student_cache_id = static function (&$CI) {
    return $CI->usermanager->get_student_simple_cache_id();
};
$no_student_id = static function (&$CI) {
    return null;
};
$admin_student_cache_id = static function (&$CI) {
    return $CI->usermanager->get_student_simple_cache_id($CI->output->get_internal_value('student_id'));
};
$course_cache_id = static function (&$CI) {
    return 'course_' . $CI->output->get_internal_value('course_id');
};

$hook['post_controller_constructor'][] = [
    'class'    => 'DB_Fix',
    'function' => 'do_fix',
    'filename' => 'db_fix.php',
    'filepath' => 'hooks',
];

$hook['post_controller'] = [
    'class'    => 'Clear_Cache',
    'function' => 'execute',
    'filename' => 'clear_cache.php',
    'filepath' => 'hooks',
    'params'   => [
        ''       => [
            'students' => [
                'save_basic_information' => [
                    'frontend/*' => $student_cache_id,
                ],
                'save_email'             => [
                    'frontend/students/my_account.tpl' => $student_cache_id,
                ],
                'delete_avatar'          => [
                    'frontend/students/my_account.tpl' => $student_cache_id,
                ],
                'save_avatar'            => [
                    'frontend/students/my_account.tpl' => $student_cache_id,
                ],
            ],
            'courses'  => [
                'activate_course'     => [
                    'frontend/courses/index.tpl' => $student_cache_id,
                    'frontend/groups/index.tpl'  => $student_cache_id,
                    'frontend/tasks/index.tpl'   => $student_cache_id,
                    'frontend/projects/*'        => $student_cache_id,
                ],
                'quick_course_change' => [
                    'frontend/courses/index.tpl' => $student_cache_id,
                    'frontend/groups/index.tpl'  => $student_cache_id,
                    'frontend/tasks/index.tpl'   => $student_cache_id,
                    'frontend/projects/*'        => $student_cache_id,
                ],
                'signup_to_course'    => [
                    'frontend/courses/index.tpl' => $student_cache_id,
                ],
            ],
            'groups'   => [
                'select_group' => [
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                    'frontend/groups/index.tpl'           => $student_cache_id,
                    'frontend/tasks/*'                    => $student_cache_id,
                    'frontend/projects/*'                 => $student_cache_id,
                ],
            ],
            'tasks'    => [
                'upload_solution'  => [
                    'frontend/tasks/index.tpl' => $student_cache_id,
                    'frontend/tasks/task.tpl'  => static function (&$CI) {
                        return $CI->usermanager->get_student_cache_id('task_set_' . $CI->output->get_internal_value('task_set_id'));
                    },
                ],
                'reset_task_cache' => [
                    'frontend/tasks/task.tpl' => static function (&$CI) {
                        return $CI->usermanager->get_student_cache_id('task_set_' . $CI->output->get_internal_value('task_set_id'));
                    },
                ],
            ],
            'fetests'  => [
                'evaluate_test_result' => [
                    'frontend/tasks/index.tpl' => $student_cache_id,
                ],
            ],
            'projects' => [
                'select_project'   => [
                    'frontend/projects/index.tpl'     => $student_cache_id,
                    'frontend/projects/selection.tpl' => $no_student_id,
                    'frontend/projects/task.tpl'      => $no_student_id,
                    'frontend/projects/overview.tpl'  => $no_student_id,
                ],
                'reset_task_cache' => [
                    'frontend/projects/task.tpl'     => static function ($CI) {
                        return $CI->usermanager->get_student_cache_id('task_set_' . $CI->output->get_internal_value('task_set_id') . '|task_' . $CI->output->get_internal_value('task_id'));
                    },
                    'frontend/projects/overview.tpl' => $no_student_id,
                ],
                'upload_solution'  => [
                    'frontend/projects/index.tpl' => $student_cache_id,
                    'frontend/projects/task.tpl'  => static function ($CI) {
                        return $CI->usermanager->get_student_cache_id('task_set_' . $CI->output->get_internal_value('task_set_id'));
                    },
                ],
            ],
            'cli_test' => [
                'index' => [
                    'frontend/tasks/index.tpl' => $admin_student_cache_id,
                ],
            ],
        ],
        'admin/' => [
            'periods'               => [
                'create'    => [
                    'frontend/*' => $no_student_id,
                ],
                'update'    => [
                    'frontend/*' => $no_student_id,
                ],
                'delete'    => [
                    'frontend/*' => $no_student_id,
                ],
                'move_up'   => [
                    'frontend/*' => $no_student_id,
                ],
                'move_down' => [
                    'frontend/*' => $no_student_id,
                ],
            ],
            'courses'               => [
                'create' => [
                    'frontend/*' => $no_student_id,
                ],
                'update' => [
                    'frontend/*' => $no_student_id,
                ],
                'delete' => [
                    'frontend/*' => $no_student_id,
                ],
            ],
            'course_content'        => [
                'create'                    => [
                    'frontend/content/*' => $no_student_id,
                ],
                'update'                    => [
                    'frontend/content/*' => $no_student_id,
                ],
                'delete'                    => [
                    'frontend/content/*' => $no_student_id,
                ],
                'change_publication_status' => [
                    'frontend/content/*' => $no_student_id,
                ],
                'change_public_status'      => [
                    'frontend/content/*' => $no_student_id,
                ],
                'update_sorting'            => [
                    'frontend/content/*' => $no_student_id,
                ],
            ],
            'course_content_groups' => [
                'create' => [
                    'frontend/content/*' => $no_student_id,
                ],
                'update' => [
                    'frontend/content/*' => $no_student_id,
                ],
                'delete' => [
                    'frontend/content/*' => $no_student_id,
                ],
            ],
            'groups'                => [
                'create' => [
                    'frontend/groups/*'                   => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ],
                'update' => [
                    'frontend/groups/*'                   => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ],
                'delete' => [
                    'frontend/groups/*'                   => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ],
            ],
            'rooms'                 => [
                'create' => [
                    'frontend/groups/*'                   => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ],
                'update' => [
                    'frontend/groups/*'                   => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ],
                'delete' => [
                    'frontend/groups/*'                   => $no_student_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ],
            ],
            'participants'          => [
                'approve_participation'    => [
                    'frontend/*' => $admin_student_cache_id,
                ],
                'disapprove_participation' => [
                    'frontend/*' => $admin_student_cache_id,
                ],
                'delete_participation'     => [
                    'frontend/*' => $admin_student_cache_id,
                ],
                'add_participant'          => [
                    'frontend/*' => $no_student_id,
                ],
                'change_group'             => [
                    'frontend/*'                          => $admin_student_cache_id,
                    'frontend/courses/course_details.tpl' => $course_cache_id,
                ],
            ],
            'tasks'                 => [
                'update'             => [
                    'frontend/tasks/*'    => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ],
                'delete'             => [
                    'frontend/tasks/*'    => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ],
                'insert_to_task_set' => [
                    'frontend/tasks/*'    => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ],
            ],
            'task_set_types'        => [
                'update' => [
                    'frontend/tasks/*' => $no_student_id,
                ],
                'delete' => [
                    'frontend/tasks/*' => $no_student_id,
                ],
            ],
            'task_sets'             => [
                'create'                    => [
                    'frontend/tasks/*'    => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ],
                'update'                    => [
                    'frontend/tasks/*'    => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ],
                'delete'                    => [
                    'frontend/tasks/*'    => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ],
                'select_project'            => [
                    'frontend/projects/*' => $no_student_id,
                ],
                'change_publication_status' => [
                    'frontend/tasks/*'    => $no_student_id,
                    'frontend/projects/*' => $no_student_id,
                ],
                'update_sorting'            => [
                    'frontend/tasks/*' => $no_student_id,
                ],
            ],
            'task_set_permissions'  => [
                'create_permission' => [
                    'frontend/tasks/*' => $no_student_id,
                ],
                'update_permission' => [
                    'frontend/tasks/*' => $no_student_id,
                ],
                'delete_permission' => [
                    'frontend/tasks/*' => $no_student_id,
                ],
            ],
            'tests'                 => [
                'save_test_configuration' => [
                    'frontend/tasks/*' => $no_student_id,
                ],
                'prepare_new_test'        => [
                    'frontend/tasks/*' => $no_student_id,
                ],
            ],
            'solutions'             => [
                'batch_save_solutions'                  => [
                    'frontend/tasks/index.tpl'    => $no_student_id,
                    'frontend/projects/index.tpl' => $no_student_id,
                ],
                'create_solution'                       => [
                    'frontend/tasks/index.tpl'    => $admin_student_cache_id,
                    'frontend/projects/index.tpl' => $admin_student_cache_id,
                ],
                'do_upload_student_solution'            => [
                    'frontend/tasks/*'    => $admin_student_cache_id,
                    'frontend/projects/*' => $admin_student_cache_id,
                ],
                'remove_points'                         => [
                    'frontend/tasks/index.tpl'    => $no_student_id,
                    'frontend/projects/index.tpl' => $no_student_id,
                ],
                'update_valuation'                      => [
                    'frontend/tasks/index.tpl'    => $admin_student_cache_id,
                    'frontend/projects/index.tpl' => $admin_student_cache_id,
                ],
                'solution_version_switch_download_lock' => [
                    'frontend/tasks/task.tpl'    => $admin_student_cache_id,
                    'frontend/projects/task.tpl' => $admin_student_cache_id,
                ],
            ],
            'teachers'              => [
                'update_teacher'         => [
                    'frontend/tasks/index.tpl'            => $no_student_id,
                    'frontend/courses/course_details.tpl' => $no_student_id,
                    'frontend/projects/index.tpl'         => $no_student_id,
                    'frontend/projects/overview.tpl'      => $no_student_id,
                ],
                'delete_teacher'         => [
                    'frontend/tasks/index.tpl'            => $no_student_id,
                    'frontend/courses/course_details.tpl' => $no_student_id,
                    'frontend/projects/index.tpl'         => $no_student_id,
                    'frontend/projects/overview.tpl'      => $no_student_id,
                ],
                'save_basic_information' => [
                    'frontend/tasks/index.tpl'            => $no_student_id,
                    'frontend/courses/course_details.tpl' => $no_student_id,
                    'frontend/projects/index.tpl'         => $no_student_id,
                    'frontend/projects/overview.tpl'      => $no_student_id,
                ],
                'save_email'             => [
                    'frontend/tasks/index.tpl'            => $no_student_id,
                    'frontend/courses/course_details.tpl' => $no_student_id,
                    'frontend/projects/index.tpl'         => $no_student_id,
                    'frontend/projects/overview.tpl'      => $no_student_id,
                ],
            ],
        ],
    ],
];

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */
