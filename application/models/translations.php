<?php

/**
 * Translations model.
 * @package LIST_CI_Models
 * @author Andrej Jursa
 */
class Translations extends CI_Model {
    
    const CONSTANT_VALIDATION_REGEXP = '/^[a-z]+[a-z0-9]*(\_[a-z0-9]+)*$/i';
    
    /**
     * Returns array of all translations constants and texts from database prepended with defined prefix.
     * @param string $idiom language idiom for which the translations will be loaded.
     * @param string $prefix constant prefix for separation from another constants, default is 'user_custom_'.
     * @return array array of constants.
     */
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
    
    /**
     * Return all constants from database as two dimensional associative array.
     * First dimension is constant name, second dimension is language idiom and the value is text.
     * @return array translations array.
     */ 
    public function get_all_for_editing() {
        $query = $this->db->select('*')->from('translations')->order_by('constant')->get();
        $output = array();
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $output[$row->constant][$row->idiom] = $row->text;
        }}
        return $output;
    }
    
    /**
     * Returns all translations table rows for given idiom.
     * @param string $idiom language idiom.
     * @return array translations table rows.
     */
    public function get_all_for_idiom($idiom) {
        $query = $this->db->select('*')->from('translations')->where('idiom', $idiom)->order_by('constant')->get();
        $output = array();
        if ($query->num_rows() > 0) { foreach ($query->result() as $row) {
            $output[] = $row;
        }}
        return $output;
    }
    
    /**
     * Returns two dimensional array of language constants texts.
     * First dimension is constant name, second dimension is language idiom and the value is text.
     * @param string $constant name of constant for which the array have to be obtained from database.
     * @return array array for given constant name.
     */ 
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
    
    /**
     * Insert new or update existing translation for given constant, idiom and text.
     * This is not done in transaction!
     * @param string $constant name of constant.
     * @param string $idiom language idiom.
     * @param string $text language translation text.
     * @return boolean status of save operation, TRUE on success, FALSE otherwise.
     */
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
    
    /**
     * Delete all translations for given constant name.
     * @param string $constant name of constant.
     * @return boolean status of operation, TRUE when anything was deleted, FALSE otherwise.
     */
    public function delete_translations($constant) {
        if (is_string($constant) && trim($constant) && preg_match(self::CONSTANT_VALIDATION_REGEXP, $constant)) {
            $this->db->where('constant', $constant);
            $this->db->delete('translations');
            return $this->db->affected_rows() > 0;
        }
        return FALSE;
    }
    
    /**
     * Check if there is no record in database table for given constant name.
     * @param string $constant name of constant to check.
     * @return boolean returns TRUE, if there is no record with given constant name, FALSE otherwise.
     */
    public function is_constant_free($constant) {
        if (is_string($constant) && trim($constant) && preg_match(self::CONSTANT_VALIDATION_REGEXP, $constant)) {
            $this->db->where('constant', $constant);
            $query = $this->db->get('translations');
            return $query->num_rows() == 0;
        }
        return FALSE;
    }
    
}