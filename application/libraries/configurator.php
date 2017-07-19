<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Config file editor.
 *
 * @author Andrej Jursa
 * @version 1.0
 * @package LIST_Libraries
 */
class Configurator {

    /**
     * Returns configuration array for given config file.
     *
     * @param string $config name of config file without extension.
     * @return array<mixed> values of config file.
     */
    public function get_config_array($config) {
        if (file_exists(APPPATH . 'config/' . $config . '.php') || file_exists(APPPATH . 'config/' . ENVIRONMENT . '/' . $config . '.php')) {
            $configObject = new CI_Config();
            $configObject->load($config, TRUE);
            return $configObject->config[$config];
        }
        return NULL;
    }

    /**
     * Returns content of config file by config variable.
     * @param string $__config name of config file without extension.
     * @param string $__config_variable name of configuration array (variable name with dollar sign).
     * @param boolean $__base_file determines if content will be read for base file or file in environment.
     * @return array<mixed> config array.
     */
    public function get_config_array_custom($__config, $__config_variable = '$config', $__base_file = FALSE) {
        $__file = APPPATH . 'config/' . (!$__base_file ? (ENVIRONMENT . '/') : '') . $__config . '.php';
        if (!file_exists($__file)) {
            $__file = APPPATH . 'config/' . $__config . '.php';
        }
        if (file_exists($__file)) {
            include $__file;
            $__output = array();
            eval('$__output = isset(' . $__config_variable . ') ? ' . $__config_variable . ' : array();');
            return $__output;
        }
        return array();
    }


    /**
     * Saves new data array to given config file and inject them to active configuration.
     *
     * @param string $config name of config file without extension.
     * @param array<mixed> $data new values for config items.
     * @param boolean $inject if set to TRUE, it will inject data to active configuration.
     * @param boolean $independent if set to TRUE, it will inject data to $config subarray.
     * @return boolean returns TRUE if file is writen, FALSE otherwise.
     */
    public function set_config_array($config, $data, $inject = TRUE, $independent = FALSE) {
        $original_config_options = $this->get_config_array($config);
        if (!is_null($original_config_options)) {
            $config_data = $this->merge_array($original_config_options, $data);

            $file = APPPATH . 'config/' . ENVIRONMENT . '/' . $config . '.php';
            if (!file_exists($file)) {
                $file = APPPATH . 'config/' . $config . '.php';
            }
            $tokens = $this->get_config_file_tokens($file);
            if (is_null($tokens)) { return FALSE; }
            $arangement = $this->get_config_file_arangement_from_tokens($tokens);

            try {
                $content = $this->make_config_file_content($config_data, $arangement);
                $f = fopen($file, 'w');
                fputs($f, $content);
                fclose($f);
                if ($inject) { $this->inject_config_array($config, $data, $independent); }
                return TRUE;
            } catch (exception $e) {
                return FALSE;
            }
        }
        return FALSE;
    }

    /**
     * Determines and return config file arangement.
     * @param string $config name of config file without extension.
     * @param string $config_variable name of configuration array (variable name with dollar sign).
     * @param boolean $base_file determines if arangement will be read for base file or file in environment.
     * @return array<mixed> determined arangement of config file.
     */
    public function get_config_file_arangement($config, $config_variable = '$config', $base_file = FALSE) {
        $file = APPPATH . 'config/' . (!$base_file ? (ENVIRONMENT . '/') : '') . $config . '.php';
        if (!file_exists($file)) {
            $file = APPPATH . 'config/' . $config . '.php';
        }
        $tokens = $this->get_config_file_tokens($file);
        if (is_null($tokens)) { return FALSE; }
        $arangement = $this->get_config_file_arangement_from_tokens($tokens, $config_variable);
        return $arangement;
    }

    /**
     * Merges base config file with actual environment config file.
     * @param string $config name of config file without extension.
     * @param string $config_variable name of configuration array (variable name with dollar sign).
     * @return bool returns TRUE if files are merged successfully.
     */
    public function merge_config_files($config, $config_variable = '$config') {
        $file_env = APPPATH . 'config/' . ENVIRONMENT . '/' . $config . '.php';
        $file_orig = APPPATH . 'config/' . $config . '.php';
        if (file_exists($file_env) && file_exists($file_orig)) {
            $orig_data = $this->get_config_array_custom($config, $config_variable, TRUE);
            $env_data = $this->get_config_array_custom($config, $config_variable, FALSE);
            $data_to_save = $this->merge_array($orig_data, $env_data);
            $orig_arrangement = $this->get_config_file_arangement($config, $config_variable, TRUE);
            try {
                $content = $this->make_config_file_content($data_to_save, $orig_arrangement, $config_variable);
                $f = fopen($file_env, 'w');
                fputs($f, $content);
                fclose($f);
                return TRUE;
            } catch (Exception $e) {
                return FALSE;
            }
        }
        return TRUE;
    }


    /**
     * Inject config data to active codeigniter configuration.
     *
     * @param string $config name of config file without extension.
     * @param array<mixed> $data new values for config items.
     * @param boolean $independent if set to true, it will inject data to $config subarray.
     */
    private function inject_config_array($config, $data, $independent = FALSE) {
        $CI =& get_instance();
        $CI->config->config;
        if ($config == 'config') { $independent = FALSE; }
        if ($independent) {
            if (isset($CI->config->config[$config])) {
                $CI->config->config[$config] = $this->merge_array($CI->config->config[$config], $data);
            } else {
                $CI->config->config[$config] = $this->merge_array($this->get_config_array($config), $data);
            }
        } else {
            $CI->config->config = $this->merge_array($CI->config->config, $data);
        }
        if ($config == 'config') {
            if (isset($CI->config->config[$config])) {
                $CI->config->config[$config] = $this->merge_array($CI->config->config[$config], $data);
            } else {
                $CI->config->config[$config] = $this->merge_array($this->get_config_array($config), $data);
            }
        }
    }

    /**
     * Saves new data array to given config file with custom arangement and custom config variable name.
     *
     * @param string $config name of config file without extension.
     * @param array<mixed> $data new values for config items.
     * @param array<mixed> $arangement custom arangement of file content.
     * @param string $config_variable name of configuration array (variable name with dollar sign).
     * @return boolean returns TRUE if file is writen, FALSE otherwise.
     */
    public function set_config_array_custom($config, $data, $arangement, $config_variable = '$config') {
        $file = APPPATH . 'config/' . ENVIRONMENT . '/' . $config . '.php';
        if (!file_exists($file)) {
            $file = APPPATH . 'config/' . $config . '.php';
        }
        if (file_exists($file)) {
            try {
                $content = $this->make_config_file_content($data, $arangement, $config_variable);
                $f = fopen($file, 'w');
                fputs($f, $content);
                fclose($f);
                return TRUE;
            } catch (exception $e) {
                return FALSE;
            }
        }
        return FALSE;
    }

    /**
     * Recursively merge two arrays.
     *
     * @param array<mixed> $array1 first array.
     * @param array<mixed> $array2 second array.
     * @return array<mixed> merged array.
     */
    public function merge_array($array1, $array2) {
        $output = array();
        if (count($array1)) {
            foreach($array1 as $key => $value) {
                if (isset($array2[$key])) {
                    if (is_array($value) && is_array($array2[$key])) {
                        $output[$key] = $this->merge_array($value, $array2[$key]);
                    } else {
                        $output[$key] = $array2[$key];
                    }
                } else {
                    $output[$key] = $value;
                }
            }
        }
        if (count($array2)) {
            foreach($array2 as $key => $value) {
                if (!isset($output[$key])) {
                    $output[$key] = $value;
                }
            }
        }
        return $output;
    }

    /**
     * For given data and arangement creates content of config file.
     *
     * @param array<mixed> $data values of config.
     * @param array<mixed> $arangement arangement of config file content.
     * @param string $config_variable name of configuration array (variable name with dollar sign).
     * @return string config file content.
     */
    private function make_config_file_content($data, $arangement, $config_variable = '$config') {
        $content = '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . "\n";
        foreach($arangement as $item) {
            if ($item['type'] == 'comment') {
                $content .= "\n" . $item['value'] . "\n";
            } elseif ($item['type'] == 'config') {
                $value_to_write = $this->config_item_value_by_path($data, $item['value']);
                $content .= $this->config_item_by_path($item['value'], $config_variable) . ' = ' . $this->var_export($value_to_write) . ';' . "\n";
            } elseif ($item['type'] == 'custom') {
                $content .= $item['value'] . "\n";
            }
        }
        return trim($content);
    }

    /**
     * Does variable content export with respect to TRUE, FALSE and NULL in uppercase.
     * @param mixed $var variable to be exported.
     * @return string exported variable.
     */
    private function var_export(&$var) {
        $exported = var_export($var, TRUE);
        $exported_lower = strtolower($exported);
        if ($exported_lower == 'false' || $exported_lower == 'true' || $exported_lower == 'null') {
            $exported = strtoupper($exported);
        }
        return $exported;
    }

    /**
     * Returns php parser tokens for given config file.
     *
     * @param string $file path to config file.
     * @return array<mixed>|NULL array of tokens or NULL if file not found.
     */
    private function get_config_file_tokens($file) {
        if (file_exists($file)) {
            $f = fopen($file, 'r');
            ob_start();
            fpassthru($f);
            $filecontent = ob_get_clean();
            fclose($f);

            $tokens = token_get_all($filecontent);

            return $tokens;
        } else {
            return NULL;
        }
    }

    /**
     * Parses tokens to produce arangement array.
     *
     * @param array<mixed> $tokens php parser tokens.
     * @param string $config_variable name of configuration array (variable name with dollar sign).
     * @return array<mixed> arangement of config file content.
     */
    private function get_config_file_arangement_from_tokens($tokens, $config_variable = '$config') {
        $arangement = array();
        for($i=0;$i<count($tokens);$i++) {
            $token = $tokens[$i];
            if (is_array($token)) {
                $type = $token[0];
                $value = $token[1];
                if ($type == T_COMMENT || $type == T_DOC_COMMENT) {
                    $arangement[] = array('type' => 'comment', 'value' => trim($value));
                } elseif ($type == T_VARIABLE) {
                    if ($value == $config_variable) {
                        $path = $this->get_config_variable_path($tokens, $i);
                        if (count($path)) {
                            $arangement[] = array('type' => 'config', 'value' => $path);
                        }
                    }
                }
            }
        }
        return $arangement;
    }

    /**
     * Returns the path of found config variable at given token position in tokens array.
     *
     * @param array<mixed> $tokens php parser tokens.
     * @param integer $at position where config variable is found.
     * @return array<string> path for array segments.
     */
    private function get_config_variable_path($tokens, $at) {
        $path = array();
        $pos = $at + 1;
        $good = TRUE;
        while ($good) {
            if ($tokens[$pos] == '[') {
                $pos++;
                if (is_array($tokens[$pos]) && $tokens[$pos][0] == T_CONSTANT_ENCAPSED_STRING) {
                    if ($tokens[$pos+1] == ']') {
                        $path[] = trim($tokens[$pos][1], '\'"');
                        $pos++;
                    } else {
                        $good = FALSE;
                    }
                } else {
                    $good = FALSE;
                }
            } else {
                $good = FALSE;
            }
            $pos++;
        }

        return $path;
    }

    /**
     * Creates $config variable for config file content.
     *
     * @param array<string> $path path of array segments.
     * @param string $config_variable name of configuration array (variable name with dollar sign).
     * @return string config variable like array.
     */
    private function config_item_by_path($path, $config_variable = '$config') {
        $output = $config_variable;
        if (count($path)) {
            foreach($path as $segment) {
                $output .= '[' . var_export($segment, TRUE) . ']';
            }
        }
        return $output;
    }

    /**
     * Returns value of config item by path.
     *
     * @param array<mixed> $data configuration data.
     * @param array<string> $path path of array segments.
     * @return mixed value of config item defined by path.
     */
    private function config_item_value_by_path($data, $path) {
        if (count($path) == 1) {
            if (!isset($data[$path[0]])) { throw new Exception('NO SUCH PATH IN DATA ARRAY'); }
            return $data[$path[0]];
        } else {
            if (!isset($data[$path[0]])) { throw new Exception('NO SUCH PATH IN DATA ARRAY'); }
            $new_path = array();
            for($i=1;$i<count($path);$i++) {
                $new_path[] = $path[$i];
            }
            return $this->config_item_value_by_path($data[$path[0]], $new_path);
        }
    }
}
?>
