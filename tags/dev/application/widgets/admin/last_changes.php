<?php

/**
 * Last changes widget.
 *
 * @author Andrej
 */
class Last_changes extends abstract_admin_widget {
    
    public function getContentTypeName() {
        return $this->lang->line('widget_admin_last_changes_widget_type_name');
    }

    public function mergeConfiguration($old_configuration, $new_configuration) {
        if (!is_array($old_configuration)) {
            return $new_configuration;
        }
        return array_merge($old_configuration, $new_configuration);
    }

    public function preConfigureForm() {
        
    }

    public function render() {
        $CI = & get_instance();
        $this->load->config('list');
        $version = $CI->config->item('list_version');
        $this->lang->load('admin/settings');
        $this->load->helper('changelog');
        $this->load->library('changelog');
        try {
            $this->changelog->read(FCPATH . 'changelog.txt');
            $this->changelog->parse();
        } catch (Exception $error) {
            $this->parser->assign('error', $error->getMessage());
        }
        $this->parser->add_css_file('admin_settings.css');
        $this->parser->parse('widgets/admin/last_changes/main.tpl', array('content' => $this->changelog->get($version), 'version' => $version));
    }

    public function validateConfiguration($configuration) {
        return TRUE;
    }    
}
