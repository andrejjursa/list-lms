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
        ),
    ),
    /*array(
        'title' => 'lang:adminmenu_title_teacher_account',
        'pagetag' => 'teacher_account',
        'link' => 'admin_teachers/my_account',
        'class' => '',
        'sub' => array(
            array(
                'title' => 'lang:adminmenu_title_logout',
                'pagetag' => 'logout',
                'link' => 'admin_teachers/logout',
                'class' => 'adminmenu_logout',
                'sub' => NULL,
            ),
        ),
    ),*/
);

?>
