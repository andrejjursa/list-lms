<?php

require_once APPPATH . 'core/abstract_admin_widget.php';

function get_admin_widget_list(): array
{
    $widget_files = scandir(APPPATH . 'widgets/admin');
    
    $list = [];
    
    $CI =& get_instance();
    
    foreach ($widget_files as $file) {
        if (is_file(APPPATH . 'widgets/admin/' . $file)) {
            $ext_pos = strrpos($file, '.');
            if ($ext_pos !== false) {
                $ext = substr($file, $ext_pos + 1);
                $widget_type = substr($file, 0, $ext_pos);
                if ($ext === 'php') {
                    try {
                        $wgt = $CI->load->admin_widget($widget_type, 0, []);
                        $list[$widget_type] = $wgt->getContentTypeName();
                        unset($wgt);
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
    }
    
    return $list;
}