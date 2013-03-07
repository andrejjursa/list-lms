<?php

$config['adminmenu'] = array(
    array(
        'title' => 'lang:adminmenu_title_dashboard',
        'pagetag' => 'dashboard',
        'link' => 'admin_dashboard',
        'sub' => NULL,
    ),
    array(
        'title' => 'TEST',
        'pagetag' => 'test',
        'link' => 'EXTERNAL:javascript:void(0);',
        'sub' => array(
            array(
                'title' => 'TEST2',
                'pagetag' => 'test2',
                'link' => 'EXTERNAL:javascript:void(0);',
                'sub' => array(
                    array(
                        'title' => 'TEST3',
                        'pagetag' => 'test3',
                        'link' => 'EXTERNAL:javascript:void(0);',
                        'sub' => NULL,
                    ),
                ),
            ),
        ),
    ),
);

?>
