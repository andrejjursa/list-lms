<?php

$config['adminmenu'] = array(
    array(
        'title' => 'lang:adminmenu_title_dashboard',
        'pagetag' => 'dashboard',
        'link' => 'admin_dashboard',
        'class' => '',
        'sub' => NULL,
    ),
    array(
        'title' => 'lang:adminmenu_title_teaching',
        'pagetag' => 'teaching_tree',
        'link' => 'external:javascript:void(0);',
        'class' => 'inactive',
        'sub' => array(
            array(
                'title' => 'lang:adminmenu_title_organisation',
                'pagetag' => 'organisation_tree',
                'link' => 'external:javascript:void(0);',
                'class' => 'inactive',
                'sub' => array(
                    array(
                        'title' => 'lang:adminmenu_title_periods',
                        'pagetag' => 'periods',
                        'link' => 'admin_periods',
                        'class' => '',
                        'sub' => NULL,
                    ),
                    array(
                        'title' => 'lang:adminmenu_title_courses',
                        'pagetag' => 'courses',
                        'link' => 'admin_courses',
                        'class' => '',
                        'sub' => NULL,
                    ),
                    array(
                        'title' => 'lang:adminmenu_title_groups',
                        'pagetag' => 'groups',
                        'link' => 'admin_groups',
                        'class' => '',
                        'sub' => NULL,
                    ),
                ),
            ),
            array(
                'title' => 'lang:adminmenu_title_content',
                'pagetag' => 'content_tree',
                'link' => 'external:javascript:void(0);',
                'class' => 'inactive',
                'sub' => array(
                    array(
                        'title' => 'lang:adminmenu_title_tasks',
                        'pagetag' => 'tasks',
                        'link' => 'admin_tasks',
                        'class' => '',
                        'sub' => NULL,
                    ),
                    array(
                        'title' => 'lang:adminmenu_title_categories',
                        'pagetag' => 'categories',
                        'link' => 'admin_categories',
                        'class' => '',
                        'sub' => NULL,
                    ),
                    array(
                        'title' => 'lang:adminmenu_title_task_set_types',
                        'pagetag' => 'task_set_types',
                        'link' => 'admin_task_set_types',
                        'class' => '',
                        'sub' => NULL,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'lang:adminmenu_title_application',
        'pagetag' => 'application_tree',
        'link' => 'external:javascript:void(0);',
        'class' => 'inactive',
        'sub' => array(
            array(
                'title' => 'lang:adminmenu_title_translations_editor',
                'pagetag' => 'translations_editor',
                'link' => 'admin_translationseditor/index',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_application_settings',
                'pagetag' => 'settings',
                'link' => 'admin_settings',
                'class' => '',
                'sub' => NULL,
            ),
            array(
                'title' => 'lang:adminmenu_title_teachers_list',
                'pagetag' => 'teachers_list',
                'link' => 'admin_teachers/list_index',
                'class' => '',
                'sub' => NULL,
            ),
        ),
    ),
);

?>
