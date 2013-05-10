<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Overriden language class with support for adding custom language tags and language overlays.
 * @package LIST_Core
 * @author Andrej Jursa
 */ 
class LIST_Lang extends CI_Lang {
    
    /**
     * var string current language idiom.
     */
    protected $lang_idiom = '';
    /**
     * var array<mixed> loaded language overlays.
     */
    protected $lang_overlays = array();
    
    /**
     * Constructor, creates object and load default idiom from config file.
     */
    public function __construct() {
        parent::__construct();
        $this->load_default_lang_idiom();
    }
    
    /**
     * Returns current language idiom.
     * @return string language idiom.
     */
    public function get_current_idiom() {
        return $this->lang_idiom;
    }
    
    /**
     * Load language file.
     * @param string $langfile language file without suffix _lang.php.
     * @param string $idiom language idiom, if empty, default idiom will be used.
     * @param boolean $return flag for returning lang file content from this method as array.
     * @param boolean $add_sufix add suffix _lang to $langfile.
     * @param string $alt_path alternative path to look for lang file.
     * @return mixed
     */ 
    public function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
        if ($idiom == '') {
            $idiom = $this->lang_idiom;
        }  
        return parent::load($langfile, $idiom, $return, $add_suffix, $alt_path); 
    }
    
    /**
     * Performs language idiom change and reload all previously loaded language files.
     * @param string $idiom language idiom to switch to.
     * @return boolean returns TRUE, when switch to $idiom was made, FALSE otherwise (i.e. given idiom was already set).
     */
    public function reinitialize_for_idiom($idiom) {
        if (is_string($idiom) && $this->lang_idiom != $idiom) {
            $this->lang_idiom = $idiom;
            $old_loaded = $this->is_loaded;
            $this->language = array();
            $this->is_loaded = array();
            if (count($old_loaded) > 0) { foreach($old_loaded as $load_file) {
                if (preg_match('/^(?P<langfile>.+)_lang\.php$/i', $load_file, $matches)) {
                    $this->load($matches['langfile']);
                }
            }}
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Scans the language folder in APPPATH for config.php files, reads these files and outputs array of possible languages.
     * @return array<mixed> possible languages in system.
     */
    public function get_list_of_languages() {
        $languages = scandir(APPPATH . 'language');
        $langs = array();
        if (count($languages) > 0) { foreach($languages as $language) {
            if (file_exists(APPPATH . 'language/' . $language . '/config.php')) {
                include(APPPATH . 'language/' . $language . '/config.php');
                if (isset($lang_config['idiom']) && isset($lang_config['title'])) {
                    $langs[$lang_config['idiom']] = $lang_config['title'];
                }
            }
        }}
        return $langs;
    }
    
    /**
     * Add custom translations to languages.
     * @param array<sting> language translations to add to existing loaded translations.
     */
    public function add_custom_translations($translations) {
        $this->language = array_merge($translations, $this->language);
    }
    
    /**
     * Parse text as language line, if is prepended with lang: prefix.
     * @param string $text text to parse.
     * @param string $default default text to return if output of parsed $text is empty.
     * @return string parsed text.
     */
    public function text($text, $default = '') {
        $output = '';
        if (strtolower(substr($text, 0, 5)) == 'lang:') {
            $line = substr($text, 5);
            $output = $this->line($line);
        } else {
            $output = $text;
        }
        if (!$output) {
            $output = $default;
        }
        return $output;
    }
    
    /**
     * Load all overlays for given table name and table id if provided.
     * @param string $table name of table.
     * @param integer $table_id table id or NULL (NULL is default).
     */
    public function load_all_overlays($table, $table_id = NULL) {
        $this->load_overlays($table, $table_id);
    }
    
    /**
     * Returns overlay text for given table name, table id, column and idiom (if provided).
     * @param string $table name of table.
     * @param integer $table_id table id.
     * @param string $column column name in given table.
     * @param string $idiom language idiom or NULL to use default.
     * @return string language overlay text.
     */
    public function get_overlay($table, $table_id, $column, $idiom = NULL) {
        return $this->replace_null_overlay_text($this->get_overlay_if_exists($table, $table_id, $column, is_null($idiom) ? $this->lang_idiom : $idiom));
    }
    
    /**
     * Saves language overlay to database.
     * @param string $table name of table.
     * @param integer $table_id table id.
     * @param string $column column name in given table.
     * @param string $idiom language idiom.
     * @param string $text language overlay text.
     * @return boolean returns TRUE if overlay is saved, FALSE otherwise.
     */
    public function save_overlay($table, $table_id, $column, $idiom, $text) {
        $CI =& get_instance();
        
        $update = !is_null($this->get_overlay_if_exists($table, $table_id, $column, $idiom));
        
        if ($update) {
            $CI->db->set('table', $table);
            $CI->db->set('table_id', intval($table_id));
            $CI->db->set('column', $column);
            $CI->db->set('idiom', $idiom);
            $CI->db->set('text', $text);
            $CI->db->where('table', $table);
            $CI->db->where('table_id', intval($table_id));
            $CI->db->where('column', $column);
            $CI->db->where('idiom', $idiom);
            $CI->db->update('lang_overlays');
        } else {
            $CI->db->set('table', $table);
            $CI->db->set('table_id', intval($table_id));
            $CI->db->set('column', $column);
            $CI->db->set('idiom', $idiom);
            $CI->db->set('text', $text);
            $CI->db->insert('lang_overlays');
        }
        
        if ($update || $CI->db->affected_rows() > 0) {
            $this->lang_overlays[$idiom][$table][intval($table_id)][$column] = $text;
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Performs save operation on given four dimensional associative array.
     * First dimension is language idiom.
     * Second dimension is table name.
     * Third dimension is table id.
     * Fourth dimension is column name.
     * Value of array is language overlay text.
     * @param array<mixed> $array language overlays array.
     */
    public function save_overlay_array($array) {
        $all_ok = TRUE;
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $idiom => $tables) {
                if (is_array($tables) && count($tables) > 0) {
                    foreach ($tables as $table => $table_content) {
                        if (is_array($table_content) && count($table_content) > 0) {
                            foreach ($table_content as $table_id => $columns) {
                                if (is_array($columns) && count($columns) > 0) {
                                    foreach ($columns as $column => $text) {
                                        if (is_string($text)) {
                                            $output = $this->save_overlay($table, $table_id, $column, $idiom, $text);
                                            $all_ok = $output && $all_ok;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $all_ok;
    }
    
    /**
     * Initialize langauge overlay for given table, real table rows and array of fields.
     * @param string $table name of table.
     * @param array<mixed> $rows real rows obtained from database table.
     * @param array<string> $fields array of fields (column names) in table for which overlays may be presented in overlay array.
     */
    public function init_overlays($table, $rows, $fields) {
        if (count($rows) > 0) {
            $ids = array();
            foreach ($rows as $row) {
                $ids[] = intval($row['id']);
            }
            $this->load_overlays($table, $ids);
        } else {
            return;
        }
        if (count($rows) > 0 && count($fields) > 0) {
            foreach ($rows as $row) {
                foreach ($fields as $field) {
                    if (!$this->overlay_exists($table, $row['id'], $field, $this->lang_idiom)) {
                        $this->no_more_load_overlay($table, $row['id'], $field, $this->lang_idiom);
                    }
                }
            }
        }
    }
    
    /**
     * Deletes overlay from database, but not from loaded overlays.
     * @param string $table table name for which overlays will be deleted.
     * @param integer $table_id table id or NULL, if id is provided, only overlays for this id will be deleted.
     * @param string $column name of column to delete or NULL, if provided, only overlay for this column will be deleted.
     */
    public function delete_overlays($table, $table_id = NULL, $column = NULL) {
        $CI =& get_instance();
        $CI->db->from('lang_overlays')->where('table', $table);
        if (!is_null($table_id)) {
            $CI->db->where('table_id', intval($table_id));
        }
        if (!is_null($column)) {
            $CI->db->where('column', intval($column));
        }
        $CI->db->delete();
    }
    
    /**
     * Performs cloning of all overlay data from table and old table id to the same table and new table id.
     * @param string $table table name.
     * @param integer $old_table_id old table id, source of cloning.
     * @param integer $new_table_id new table id, destination of cloning.
     * @return boolean TRUE, if all loaded data are successfully saved with new table id, FALSE otherwise.
     */
    public function clone_overlays($table, $old_table_id, $new_table_id) {
        $data = $this->get_overlays_for_cloning($table, $old_table_id, $new_table_id);
        
        return $this->save_overlay_array($data);
    }
    
    /**
     * Loads all data belonging to table and old table id and prepare array of new data to be saved. This new array will have the same keys, except the table id will be the new table id.
     * @param string $table table name.
     * @param integer $old_table_id old table id, the source.
     * @param integer $new_table_id new table id, the destination.
     * @return array<mixed> new data to be saved as cloned data for old table id.
     */
    protected function get_overlays_for_cloning($table, $old_table_id, $new_table_id) {
        $this->load_overlays($table, $old_table_id);
        
        $output = array();
        
        if (count($this->lang_overlays) > 0) { foreach ($this->lang_overlays as $idiom => $table_data) {
            if (count($table_data) > 0) { foreach ($table_data as $a_table => $table_id_data) {
                if ($a_table == $table && count($table_id_data) > 0) { foreach ($table_id_data as $a_table_id => $column_data) {
                    if ($a_table_id == $old_table_id && count($column_data) > 0) { foreach ($column_data as $column => $text) {
                        $output[$idiom][$table][intval($new_table_id)][$column] = $text;
                    }}
                }}
            }}
        }}
        
        return $output;
    }
    
    /**
     * Check text and returns empty string, if text value is NULL.
     * @param string $text text.
     * @return string replaced text.
     */
    protected function replace_null_overlay_text($text) {
        if (is_null($text)) { return ''; }
        return $text;
    }
    
    /**
     * Returns overlay text for given parameters if this overlay exists.
     * If overlay is not loaded, it will be.
     * @param string $table name of table.
     * @param integer $table_id table id.
     * @param string $column column name in given table.
     * @param string $idiom language idiom.
     * @param mixed can return overlay text if overlay exists or NULL value of not exists.
     */
    protected function get_overlay_if_exists($table, $table_id, $column, $idiom) {
        if ($this->overlay_exists($table, $table_id, $column, $idiom)) {
            return $this->lang_overlays[$idiom][$table][intval($table_id)][$column];
        } else {
            $this->load_overlays($table, $table_id, $column);
            if (!$this->overlay_exists($table, $table_id, $column, $idiom)) {
                $this->no_more_load_overlay($table, $table_id, $column, $idiom);
            }
            return $this->lang_overlays[$idiom][$table][intval($table_id)][$column];
        }
    }
    
    /**
     * Checks if overlay for given parameters is loaded.
     * @param string $table name of table.
     * @param integer $table_id table id.
     * @param string $column column name in given table.
     * @param string $idiom language idiom.
     * @return boolean returns TRUE if exists, FALSE otherwise.
     */ 
    protected function overlay_exists($table, $table_id, $column, $idiom) {
        if (isset($this->lang_overlays[$idiom][$table][intval($table_id)])) {
            $columns = $this->lang_overlays[$idiom][$table][intval($table_id)];
            return array_key_exists($column, $columns);
        }
        return FALSE; 
    }
    
    /**
     * Set overlay for given parameters to NULL value.
     * @param string $table name of table.
     * @param integer $table_id table id.
     * @param string $column column name in given table.
     * @param string $idiom language idiom.
     */
    protected function no_more_load_overlay($table, $table_id, $column, $idiom) {
        $this->lang_overlays[$idiom][$table][intval($table_id)][$column] = NULL;
    }
    
    /**
     * Loads overlays from database for given parameters.
     * @param string $table table name for which overlays will be loaded.
     * @param integer|array<integer> $table_id table id, array of table ids or NULL, if id is provided, only overlays for this id will be loaded, if array of ids is provited, loaded overlays will be restricted to these ids.
     * @param string $column name of column to load or NULL, if provided, only overlay for this column will be loaded.
     */
    protected function load_overlays($table, $table_id = NULL, $column = NULL) {
        $CI =& get_instance();
        $CI->db->from('lang_overlays')->where('table', $table);
        if (!is_null($table_id)) {
            if (is_numeric($table_id)) {
                $CI->db->where('table_id', intval($table_id));
            } elseif (is_array($table_id) && count($table_id) > 0) {
                $CI->db->where_in('table_id', $table_id);
            }
        }
        if (!is_null($column)) {
            $CI->db->where('column', intval($column));
        }
        $query = $CI->db->get();
        if ($query->num_rows()) { foreach ($query->result() as $row) {
            $this->lang_overlays[$row->idiom][$row->table][intval($row->table_id)][$row->column] = $row->text;
        }}
        $query->free_result();
    }
    
    /**
     * Loads default lang idiom from config file.
     */
    protected function load_default_lang_idiom() {
        $deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
        $this->lang_idiom = ($deft_lang == '') ? 'english' : $deft_lang;
    }
    
}