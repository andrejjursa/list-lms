<?php

class DB_Fix {

  public function do_fix() {
    $CI =& get_instance();

    if (!isset($CI->db) || is_null($CI->db)) {
      return;
    }

    $query = $CI->db->query('SHOW GLOBAL VARIABLES LIKE "sql_mode"');

    if ($query->num_rows() == 1) {
      $sql_mode = $query->row();
      if ($sql_mode->Variable_name == 'sql_mode') {
        $sql_mode_value = $sql_mode->Value;
        if (trim($sql_mode_value) != '') {
          $sql_mode_options = explode(',', $sql_mode_value);
          $found = false;
          for ($i=0;$i<count($sql_mode_options);$i++) {
            if (strtoupper($sql_mode_options[$i]) == 'ONLY_FULL_GROUP_BY') {
              unset($sql_mode_options[$i]);
              $found = true;
              break;
            }
          }
          if ($found) {
            $new_sql_mode_value = implode(',', $sql_mode_options);
            $CI->db->query('SET SESSION sql_mode = "' . $new_sql_mode_value . '"');
          }
        }
      }
    }
  }

}
