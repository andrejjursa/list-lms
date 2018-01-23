<?php

$config['adminmenu'] = array(
    array(
        'title' => 'lang:adminmenu_title_dashboard',
        'pagetag' => 'dashboard',
        'link' => 'admin_dashboard',
        'icon' => '<i class="fa fa-home" aria-hidden="true"></i>',
        'class' => '',
        'sub' => NULL,
    ),
    array(
        'title' => 'lang:adminmenu_title_organisation',
        'pagetag' => 'organisation_tree',
        'link' => 'external:javascript:void(0);',
        'icon' => '<i class="fa fa-calendar" aria-hidden="true"></i>',
        'class' => 'inactive',
        'sub' => array(
            array(
                'title' => 'lang:adminmenu_title_periods',
                'pagetag' => 'periods',
                'link' => 'admin_periods',
                'icon' => '<i class="fa fa-calendar-o" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_courses',
                'pagetag' => 'courses',
                'link' => 'admin_courses',
                'icon' => '<i class="fa fa-book" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_groups',
                'pagetag' => 'groups',
                'link' => 'admin_groups',
                'icon' => '<i class="fa fa-users" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
        ),
    ),
    array(
        'title' => 'lang:adminmenu_title_content',
        'pagetag' => 'content_tree',
        'link' => 'external:javascript:void(0);',
        'icon' => '<i class="fa fa-list-alt" aria-hidden="true"></i>',
        'class' => 'inactive',
        'sub' => array(
            array(
                'title' => 'lang:adminmenu_title_tasks',
                'pagetag' => 'tasks',
                'link' => 'admin_tasks',
                'icon' => '<i class="fa fa-tasks" aria-hidden="true"></i>',
                'class' => '',
                'sub' => array(
                    array(
                        'title' => 'lang:adminmenu_title_categories',
                        'pagetag' => 'categories',
                        'link' => 'admin_categories',
                        'icon' => '<i class="fa fa-tags" aria-hidden="true"></i>',
                        'class' => '',
                        'sub' => NULL,
                    ),
                ),
            ),
            array(
                'title' => 'lang:adminmenu_title_task_sets',
                'pagetag' => 'task_sets',
                'link' => 'admin_task_sets',
                'icon' => '<i class="fa fa-list" aria-hidden="true"></i>',
                'class' => '',
                'sub' => array(
                    array(
                        'title' => 'lang:adminmenu_title_task_sets_sorting',
                        'pagetag' => 'task_sets_sorting',
                        'link' => 'admin_task_sets/sorting',
                        'icon' => '<i class="fa fa-sort" aria-hidden="true"></i>',
                        'class' => '',
                        'sub' => NULL,
                    ),
                    array(
                        'title' => 'lang:adminmenu_title_task_set_types',
                        'pagetag' => 'task_set_types',
                        'link' => 'admin_task_set_types',
                        'icon' => '<i class="fa fa-server" aria-hidden="true"></i>',
                        'class' => '',
                        'sub' => NULL,
                    ),
                ),
                //'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_course_content',
                'pagetag' => 'course_content',
                'link' => 'admin_course_content',
                'class' => '',
                'sub' => array(
                    array(
                        'title' => 'lang:adminmenu_title_course_content_sorting',
                        'pagetag' => 'course_content_sorting',
                        'link' => 'admin_course_content/sorting',
                        'class' => '',
                        'sub' => NULL,
                    ),
                    array(
                        'title' => 'lang:adminmenu_title_course_content_groups',
                        'pagetag' => 'course_content_groups',
                        'link' => 'admin_course_content_groups',
                        'class' => '',
                        'sub' => NULL,
                    ),
                ),
            ),
            array(
                'title' => 'lang:adminmenu_title_restrictions',
                'pagetag' => 'restrictions',
                'link' => 'admin_restrictions',
                'icon' => '<i class="fa fa-times" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
        ),
    ),
    array(
        'title' => 'lang:adminmenu_title_students',
        'pagetag' => 'students_tree',
        'link' => 'external:javascript:void(0);',
        'icon' => '<i class="fa fa-user-circle" aria-hidden="true"></i>',
        'class' => 'inactive',
        'sub' => array(
            array(
                'title' => 'lang:adminmenu_title_solutions',
                'pagetag' => 'solutions',
                'link' => 'admin_solutions',
                'icon' => '<i class="fa fa-check-square-o" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_valuation_tables',
                'pagetag' => 'valuation_tables',
                'link' => 'admin_solutions/valuation_tables',
                'icon' => '<i class="fa fa-table" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_participants',
                'pagetag' => 'participants',
                'link' => 'admin_participants',
                'icon' => '<i class="fa fa-list-ol" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_students_manager',
                'pagetag' => 'students_manager',
                'link' => 'admin_students',
                'icon' => '<i class="fa fa-id-card" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
        ),
    ),
    array(
        'title' => 'lang:adminmenu_title_comparation',
        'pagetag' => 'comparation_tree',
        'link' => 'external:javascript:void(0);',
        'icon' => '<i class="fa fa-balance-scale" aria-hidden="true"></i>',
        'class' => 'inactive',
        'sub' => array(
            array(
                'title' => 'lang:adminmenu_title_moss_comparator',
                'pagetag' => 'moss',
                'link' => 'admin_moss',
                'icon' => '<i class="fa fa-columns" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_java_comparator',
                'pagetag' => 'comparator',
                'link' => 'admin_comparator',
                'icon' => '<i class="fa fa-columns" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
        ),
    ),
    array(
        'title' => 'lang:adminmenu_title_application',
        'pagetag' => 'application_tree',
        'link' => 'external:javascript:void(0);',
        'icon' => '<i class="fa fa-cogs" aria-hidden="true"></i>',
        'class' => 'inactive',
        'sub' => array(
            array(
                'title' => 'lang:adminmenu_title_translations_editor',
                'pagetag' => 'translations_editor',
                'link' => 'admin_translationseditor',
                'icon' => '<i class="fa fa-language" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_application_settings',
                'pagetag' => 'settings',
                'link' => 'admin_settings',
                'icon' => '<i class="fa fa-cog" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_teachers_list',
                'pagetag' => 'teachers_list',
                'link' => 'admin_teachers/list_index',
                'icon' => '<i class="fa fa-id-badge" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_activity_logs',
                'pagetag' => 'logs',
                'link' => 'admin_logs',
                'icon' => '<i class="fa fa-bar-chart" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_application_changelog',
                'pagetag' => 'settings_changelog',
                'link' => 'admin_settings/changelog',
                'icon' => '<i class="fa fa-truck" aria-hidden="true"></i>',
                'class' => '',
                'sub' => NULL,
            ),
        ),
    ),
    array(
        'title' => 'lang:adminmenu_title_manual_index',
        'pagetag' => 'manual_index',
        'link' => 'help/backend',
        'icon' => '<i class="fa fa-file-text" aria-hidden="true"></i>',
        'class' => 'manual_index',
        'sub' => NULL,
    ),
);

?>
