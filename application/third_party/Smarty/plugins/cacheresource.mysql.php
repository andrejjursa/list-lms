<?php

/**
 * MySQL CacheResource
 *
 * CacheResource Implementation based on the Custom API to use
 * MySQL as the storage resource for Smarty's output caching.
 *
 * Table definition:
 * <pre>CREATE TABLE IF NOT EXISTS `output_cache` (
 *   `id` CHAR(40) NOT NULL COMMENT 'sha1 hash',
 *   `name` VARCHAR(250) NOT NULL,
 *   `cache_id` VARCHAR(250) NULL DEFAULT NULL,
 *   `compile_id` VARCHAR(250) NULL DEFAULT NULL,
 *   `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 *   `content` LONGTEXT NOT NULL,
 *   PRIMARY KEY (`id`),
 *   INDEX(`name`),
 *   INDEX(`cache_id`),
 *   INDEX(`compile_id`),
 *   INDEX(`modified`)
 * ) ENGINE = InnoDB;</pre>
 *
 * @package CacheResource
 * @author Rodney Rehm, Andrej Jursa
 */
class Smarty_CacheResource_Mysql extends Smarty_CacheResource_Custom {
    protected $list_version;
    
    public function __construct() {
        $ci =& get_instance();
        
        $ci->config->load('list');
        
        $this->list_version = $ci->config->item('list_version');
    }

    /**
     * fetch cached content and its modification time from data source
     *
     * @param string $id unique cache content identifier
     * @param string $name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @param string $content cached content
     * @param integer $mtime cache modification timestamp (epoch)
     * @return void
     */
    protected function fetch($id, $name, $cache_id, $compile_id, &$content, &$mtime)
    {
        $ci =& get_instance();
        $ci->db->select('modified, content')->from('output_cache')->where('id', $id);
        if ($name) {
            $ci->db->where('name', $name);
        }
        if ($cache_id) {
            $ci->db->where('cache_id', $cache_id);
        }
        if ($compile_id) {
            $ci->db->where('compile_id', $compile_id);
        }
        $ci->db->where('list_version', $this->list_version);
        $query = $ci->db->get();
        $row = $query->row();
        $query->free_result();
        if ($row) {
            $content = $row->content;
            $mtime = strtotime($row->modified);
        } else {
            $content = null;
            $mtime = null;
        }
    }
    
    /**
     * Fetch cached content's modification timestamp from data source
     *
     * @note implementing this method is optional. Only implement it if modification times can be accessed faster than loading the complete cached content.
     * @param string $id unique cache content identifier
     * @param string $name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @return integer|boolean timestamp (epoch) the template was modified, or false if not found
     */
    protected function fetchTimestamp($id, $name, $cache_id, $compile_id)
    {
        $ci =& get_instance();
        $ci->db->select('modified')->from('output_cache')->where('id', $id);
        if ($name) {
            $ci->db->where('name', $name);
        }
        if ($cache_id) {
            $ci->db->where('cache_id', $cache_id);
        }
        if ($compile_id) {
            $ci->db->where('compile_id', $compile_id);
        }
        $ci->db->where('list_version', $this->list_version);
        $query = $ci->db->get();
        $row = $query->row();
        $query->free_result();
        return $row ? strtotime($row->modified) : 0;
    }
    
    /**
     * Save content to cache
     *
     * @param string $id unique cache content identifier
     * @param string $name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @param integer|null $exp_time seconds till expiration time in seconds or null
     * @param string $content content to cache
     * @return boolean success
     */
    protected function save($id, $name, $cache_id, $compile_id, $exp_time, $content)
    {
        $ci =& get_instance();
        $ci->db->where('id', $id);
        $ci->db->delete('output_cache');
        $ci->db->set('id', $id);
        $ci->db->set('name', $name);
        $ci->db->set('cache_id', $cache_id);
        $ci->db->set('compile_id', $compile_id);
        $ci->db->set('content', $content);
        $ci->db->set('list_version', $this->list_version);
        $ci->db->insert('output_cache');
        return $ci->db->affected_rows() == 1;
    }
    
    /**
     * Delete content from cache
     *
     * @param string $name template name
     * @param string $cache_id cache id
     * @param string $compile_id compile id
     * @param integer|null $exp_time seconds till expiration or null
     * @return integer number of deleted caches
     */
    protected function delete($name, $cache_id, $compile_id, $exp_time)
    {
        $ci =& get_instance();
        if ($name === null && $cache_id === null && $compile_id === null && $exp_time === null) {
            // returning the number of deleted caches would require a second query to count them
            $ci->db->truncate('output_cache');
            return -1;
        }
        if ($name !== null) {
            $ci->db->where('`name` LIKE \'' . str_replace('*', '%', $ci->db->escape_str(APPPATH . 'views/' . $name)) . '\'');
        }
        if ($compile_id !== null) {
            $ci->db->where('compile_id', $compile_id);
        }
        if ($exp_time !== null) {
            $ci->db->where('modified <', 'DATE_SUB(NOW(), INTERVAL ' . intval($exp_time) . ' SECOND)', FALSE);
        }
        if ($cache_id !== null) {
            $ci->db->where($this->build_cache_id_where_clause($cache_id));
        }
        $ci->db->delete('output_cache');
        //echo($ci->db->last_query() . ';'. "\n");
        return $ci->db->affected_rows();
    }
    
    /**
     * builds where clause for cache_id
     * 
     * @param string $cache_id query cache id
     * @return string|null where clause for cache_id query
     */
    private function build_cache_id_where_clause($cache_id) {
        if ($cache_id) {
            $regexp = '';
            $parts = explode('|', $cache_id);
            if (count($parts)) { foreach ($parts as $part) {
                if ($regexp !== '') { $regexp .= '|'; }
                $realpart = preg_replace('/[\*]+/', '*', $part);
                $regexp .= str_replace('*', '%', $realpart);
            }}
            return '(`cache_id` = \'' . $regexp . '\' OR `cache_id` LIKE \'' . $regexp . '|%\' OR `cache_id` LIKE \'%|' . $regexp . '\' OR `cache_id` LIKE \'%|' . $regexp . '|%\')';
        }
        return null;
    }
}