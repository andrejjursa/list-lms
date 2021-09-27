<?php

/**
 * Last changes widget.
 *
 * @author Andrej
 */
class Last_changes extends abstract_admin_widget {
    
    public function getContentTypeName(): string
    {
        return $this->lang->line('widget_admin_last_changes_widget_type_name');
    }

    public function mergeConfiguration($old_configuration, $new_configuration): array
    {
        return $old_configuration ?? [];
    }

    public function preConfigureForm():void {}

    public function render(): void
    {
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

    public function validateConfiguration($configuration): bool
    {
        return TRUE;
    }    
}
