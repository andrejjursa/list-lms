<?php

class Translations extends CI_Model {
    
    const CONSTANT_VALIDATION_REGEXP = '/^[a-z]+[a-z0-9]*(\_[a-z0-9]+)*$/i';
    
    public function get_translations_for_idiom($idiom, $prefix = 'user_custom_') {
        if (is_string($idiom)) {
            $this->db->select('constant, text')->from('translations')->where('idiom', $idiom);
            $query = $this->db->get();
            $output = array();
            if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
                $output[$prefix . $row->constant] = $row->text;
            }}
            return $output;
        } else {
            return array();
        }
    }
    
    public function get_all_for_editing() {
        $query = $this->db->select('*')->from('translations')->order_by('constant')->get();
        $output = array();
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $output[$row->constant][$row->idiom] = $row->text;
        }}
        return $output;
    }
    
    public function get_all_for_idiom($idiom) {
        $query = $this->db->select('*')->from('translations')->where('idiom', $idiom)->order_by('constant')->get();
        $output = array();
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $output[] = $row;
        }}
        return $output;
    }
    
    public function get_constant_for_editing($constant) {
        if (is_string($constant) && trim($constant) && preg_match(self::CONSTANT_VALIDATION_REGEXP, $constant)) {
            $query = $this->db->select('*')->from('translations')->order_by('constant')->where('constant', $constant)->get();
            $output = array();
            if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
                $output[$row->constant][$row->idiom] = $row->text;
            }}
            return $output;
        }
        return array();
    }
    
    public function save_translation($constant, $idiom, $text) {
        if (is_string($constant) && trim($constant) != '' && preg_match(self::CONSTANT_VALIDATION_REGEXP, $constant) && is_string($idiom) && trim($idiom) != '') {
            if ($this->db->select('*')->from('translations')->where('constant', $constant)->where('idiom', $idiom)->get()->num_rows() == 0) {
                $this->db->set('constant', $constant)->set('idiom', $idiom)->set('text', $text);
                $this->db->insert('translations');
                return $this->db->affected_rows() == 1;
            } else {
                $this->db->set('constant', $constant)->set('idiom', $idiom)->set('text', $text);
                $this->db->where('constant', $constant)->where('idiom', $idiom);
                $this->db->update('translations');
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function delete_translations($constant) {
        if (is_string($constant) && trim($constant) && preg_match(self::CONSTANT_VALIDATION_REGEXP, $constant)) {
            $this->db->where('constant', $constant);
            $this->db->delete('translations');
            return $this->db->affected_rows() > 0;
        }
        return FALSE;
    }
    
    public function is_constant_free($constant) {
        if (is_string($constant) && trim($constant) && preg_match(self::CONSTANT_VALIDATION_REGEXP, $constant)) {
            $this->db->where('constant', $constant);
            $query = $this->db->get('translations');
            return $query->num_rows() == 0;
        }
        return FALSE;
    }
    
}