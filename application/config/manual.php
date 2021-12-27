<?php

$config['backend_index'] = [
    [
        'title' => 'lang:help_index_manual_welcome',
        'index' => 'manual/welcome',
        'type'  => 'link',
        'sub'   => null,
    ],
    [
        'title' => 'lang:help_index_backend_dashboard',
        'index' => 'dashboard/dashboard',
        'type'  => 'link',
        'sub'   => [
            [
                'title' => 'lang:help_index_backend_widgets_course_overview',
                'index' => 'widgets/course_overview',
                'type'  => 'link',
                'sub'   => null,
            ],
            [
                'title' => 'lang:help_index_backend_widgets_unevaluated_solutions',
                'index' => 'widgets/unevaluated_solutions',
                'type'  => 'link',
                'sub'   => null,
            ],
        ],
    ],
    [
        'title' => 'lang:help_index_backend_task_sets',
        'type'  => 'text',
        'sub'   => [
            [
                'title' => 'lang:help_index_backend_new_task_set_form',
                'index' => 'admin_task_sets/new_task_set',
                'type'  => 'link',
                'sub'   => null,
            ],
        ],
    ],
];