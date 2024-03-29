<?php

/**
 * Aplication helper functions.
 *
 * @package LIST_Helpers
 * @author  Andrej Jursa
 */

include_once(APPPATH . 'third_party/simplehtmldom/simple_html_dom.php');

/**
 * This function will check if there is server variable MOD_REWRITE_ENABLED, which is created in .htaccess.
 *
 * @return boolean TRUE, if this variable exists and is set to yes, FALSE otherwise.
 */
function is_mod_rewrite_enabled(): bool
{
    return isset($_SERVER['MOD_REWRITE_ENABLED']) && $_SERVER['MOD_REWRITE_ENABLED'] === 'yes';
}

/**
 * Creates internal url from relative url.
 * Function respects setting of rewrite engine from application/config/config.php.
 *
 * @param string  $relative_url      relative url as controller/action/[parameters].
 * @param boolean $force_simple_link if set to TRUE, link will not use rewrite engine and url suffix, even if they are
 *                                   configured.
 *
 * @return string internal url.
 */
function create_internal_url(string $relative_url, bool $force_simple_link = false): string
{
    $CI =& get_instance();
    if (!$force_simple_link && $CI->config->item('rewrite_engine_enabled') && is_mod_rewrite_enabled()) {
        if (trim(trim($relative_url, '\\/')) === '') {
            return base_url('/');
        }
        return base_url('/' . trim($relative_url, '/')) . $CI->config->item('url_suffix');
    }
    
    return base_url($CI->config->item('index_page') . '/' . trim($relative_url, '/'))
        . (!$force_simple_link ? $CI->config->item('url_suffix') : '');
}

/**
 * Adds new part of url at the end of internal url.
 *
 * @param string  $old_url         old url with base url.
 * @param string  $url_part_to_add additional part of url added in the process.
 * @param boolean $can_add         condition, which must be satisfied to add $url_part_to_add to the $old_url, default
 *                                 is TRUE.
 *
 * @return string modified url.
 */
function add_to_internal_url($old_url, $url_part_to_add, $can_add = true): string
{
    if (!$can_add) {
        return $old_url;
    }
    $CI =& get_instance();
    $suffix = $CI->config->item('url_suffix');
    $base_url = base_url();
    $index_page = $CI->config->item('index_page');
    if (strpos($old_url, $base_url) === 0) {
        $new_url = $old_url;
        $add_suffix = false;
        if (substr($new_url, -strlen($index_page)) !== $index_page && substr($new_url, -strlen($suffix)) === $suffix) {
            $new_url = substr($new_url, 0, strlen($new_url) - strlen($suffix));
            $add_suffix = true;
        }
        return rtrim(
                rtrim($new_url, '\\/') . '/' . trim(trim($url_part_to_add), '\\/'), '\\/'
            ) . ($add_suffix ? $suffix : '');
    }
    
    return $old_url;
}

/**
 * Encodes string using base64 algorithm and replace some url invalid characters like / and = to - and _ .
 *
 * @param string $string string to encode.
 *
 * @return string encoded string.
 */
function encode_for_url($string): string
{
    $encoded64 = base64_encode($string);
    return str_replace(['/', '='], ['-', '_'], $encoded64);
}

/**
 * Decodes string encoded by function {@link encode_for_url()}.
 *
 * @param string $string string to decode.
 *
 * @return string decoded string.
 */
function decode_from_url($string): string
{
    $decode64 = str_replace(['-', '_'], ['/', '='], $string);
    return base64_decode($decode64);
}

/**
 * Translates url parameters to url segments string.
 *
 * @param array $params array of parameters (only non-array and non-object values will be added to output).
 *
 * @return string segmented url of name => value pairs.
 */
function implode_uri_params($params): string
{
    if (is_array($params) && count($params) > 0) {
        $output = '';
        foreach ($params as $name => $value) {
            if (!is_array($value) && !is_object($value)) {
                $pair = rawurlencode($name) . '/' . rawurlencode($value);
                $output .= $output !== '' ? '/' . $pair : $pair;
            }
        }
        return $output;
    }
    return '';
}

/**
 * Checks if database driver uses mysql, mysqli or pdo mysql.
 *
 * @return boolean TRUE if uses one of this drivers.
 */
function db_is_mysql(): bool
{
    $CI =& get_instance();
    $provider = strtolower($CI->db->dbdriver);
    return $provider === 'mysql' || $provider === 'mysqli' ||
        ($provider === 'pdo' && strpos($CI->db->hostname, 'mysql') !== false);
}

/**
 * If database uses mysql, it changes table to InnoDB engine (to support transactions).
 *
 * @param string $table table name.
 */
function change_mysql_table_to_InnoDB($table)
{
    if (db_is_mysql()) {
        $CI =& get_instance();
        $CI->db->query('ALTER TABLE `' . $table . '` ENGINE = INNODB');
    }
}

/**
 * Returns array of days.
 *
 * @return array<string> array of days.
 */
function get_days(): array
{
    $CI =& get_instance();
    return [
        1 => $CI->lang->line('common_day_monday'),
        2 => $CI->lang->line('common_day_tuesday'),
        3 => $CI->lang->line('common_day_wednesday'),
        4 => $CI->lang->line('common_day_thursday'),
        5 => $CI->lang->line('common_day_friday'),
        6 => $CI->lang->line('common_day_saturday'),
        7 => $CI->lang->line('common_day_sunday'),
    ];
}

/**
 * This function will construct days array and assign them to template parser.
 * Days array is called $list_days inside template.
 */
function smarty_inject_days()
{
    $CI =& get_instance();
    $CI->parser->assign('list_days', get_days());
}

/**
 * Recursively delete a directory.
 *
 * @param string  $dir             directory name.
 * @param boolean $delete_root_too delete specified top-level directory as well.
 */
function unlink_recursive($dir, $delete_root_too)
{
    if (!$dh = @opendir($dir)) {
        return;
    }
    
    while (false !== ($obj = readdir($dh))) {
        if ($obj === '.' || $obj === '..') {
            continue;
        }
        
        if (!@unlink($dir . '/' . $obj)) {
            unlink_recursive($dir . '/' . $obj, true);
        }
    }
    
    closedir($dh);
    
    if ($delete_root_too) {
        @rmdir($dir);
    }
}

/**
 * Normalizes characters in string.
 *
 * @param string $string to normalize.
 *
 * @return string string with normalized characters.
 */
function normalize($string): string
{
    $table = [
        'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c',
        'Ć' => 'C', 'ć' => 'c', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
        'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I',
        'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
        'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r',
        'ď' => 'd', 'ň' => 'n', 'ľ' => 'l', 'ĺ' => 'l', 'ť' => 't', 'ř' => 'r', 'ź' => 'z', 'ń' => 'n',
        'Ď' => 'D', 'Ň' => 'N', 'Ľ' => 'L', 'Ĺ' => 'L', 'Ť' => 'T', 'Ř' => 'R', 'Ś' => 'S', 'ś' => 's',
    ];
    
    return strtr($string, $table);
}

/**
 * Normalizes characters in string for file system.
 *
 * @param string $string to normalize.
 *
 * @return string string with normalized characters.
 */
function normalizeForFilesystem($string): string
{
    $normalized = normalize($string);
    
    $output = '';
    for ($i = 0, $iMax = mb_strlen($normalized); $i < $iMax; $i++) {
        $char = mb_substr($normalized, $i, 1);
        if (($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z') || ($char >= '0' && $char <= '9')) {
            $output .= $char;
        } else if ($char === ' ') {
            $output .= '_';
        } else if ($char === '-' || $char === '_' || $char === ':' || $char === '.') {
            $output .= '-';
        }
    }
    return $output;
}

/**
 * Compute file capacity and return value with unit.
 *
 * @param string $filename path and file name.
 *
 * @return string capacity of file in bytes, KiB, MiB or GiB.
 */
function get_file_size($filename): string
{
    $size_bytes = @filesize($filename);
    if ($size_bytes === false || $size_bytes === 0) {
        return '0 B';
    }
    return compute_size_with_unit($size_bytes);
}

/**
 * Compute size with units from given value in bytes.
 *
 * @param integer $size_bytes size in bytes.
 *
 * @return string size with unit.
 */
function compute_size_with_unit($size_bytes): string
{
    $size = $size_bytes;
    $unit = 'B';
    if ($size > 1023) {
        $size /= 1024;
        $unit = 'KiB';
    }
    if ($size > 1023) {
        $size /= 1024;
        $unit = 'MiB';
    }
    if ($size > 1023) {
        $size /= 1024;
        $unit = 'GiB';
    }
    return number_format($size, 2, '.', ' ') . ' ' . $unit;
}

/**
 * This function will remove all base url from img and a tags.
 *
 * @param string $string html content.
 *
 * @return string updated html content.
 */
function remove_base_url($string): string
{
    if (!is_string($string) || empty($string)) {
        return $string;
    }
    $CI =& get_instance();
    $base_url = $CI->config->item('base_url');
    
    $html = str_get_html($string, true, true, DEFAULT_TARGET_CHARSET, false);
    
    foreach ($html->find('img, a') as $element) {
        if ($element->tag === 'img' && mb_strpos(trim($element->src), $base_url) === 0) {
            $element->src = ltrim(mb_substr(trim($element->src), mb_strlen($base_url)), '\\/');
        } else if ($element->tag === 'a' && mb_strpos(trim($element->href), $base_url) === 0) {
            $element->href = ltrim(mb_substr(trim($element->href), mb_strlen($base_url)), '\\/');
        }
    }
    
    return $html->__toString();
}

/**
 * Apply remove_base_url() to overlay array.
 *
 * @param array<string>        $overlay_array overlay array.
 * @param string|array<string> $column_name   name of column with html or array of columns with html.
 *
 * @return array<string> updated overlay array.
 */
function remove_base_url_from_overlay_array($overlay_array, $column_name): array
{
    if (is_array($overlay_array) && count($overlay_array)) {
        foreach ($overlay_array as $idiom => $idiom_array) {
            if (is_array($idiom_array) && count($idiom_array)) {
                foreach ($idiom_array as $table => $table_array) {
                    if (is_array($table_array) && count($table_array)) {
                        foreach ($table_array as $table_id => $table_id_array) {
                            if (is_array($table_id_array) && count($table_id_array)) {
                                foreach ($table_id_array as $column => $content) {
                                    if ((is_string($column_name) && $column === $column_name)
                                        || (is_array($column_name) && in_array($column, $column_name, true))
                                    ) {
                                        if (!empty($content)) {
                                            $overlay_array[$idiom][$table][$table_id][$column] = remove_base_url($content);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $overlay_array;
}

/**
 * Copy all files and folders from one directory to another. Target directory will be created if it is not exist yet.
 *
 * @param string $from source directory.
 * @param string $to   target directory.
 *
 * @return boolean TRUE, if proccess of copying was completted successfully, FALSE otherwise.
 */
function clone_directory($from, $to): bool
{
    set_time_limit(1200);
    if ($from === $to) {
        return false;
    }
    if (!file_exists($from)) {
        return false;
    }
    if (!file_exists($to)) {
        @mkdir($to, DIR_READ_MODE, true);
    }
    $files_dirs = scandir($from);
    foreach ($files_dirs as $file_dir) {
        if (is_file(ltrim($from, '/\\') . '/' . $file_dir)) {
            $result = @copy(
                ltrim($from, '/\\') . '/' . $file_dir,
                ltrim($to, '/\\') . '/' . $file_dir
            );
            if ($result === false) {
                return false;
            }
        } else if ($file_dir !== '.' && $file_dir !== '..') {
            $result = clone_directory(
                ltrim($from, '/\\') . '/' . $file_dir,
                ltrim($to, '/\\') . '/' . $file_dir
            );
            if ($result === false) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Converts number to time. Must be lower than 86400 to be converted.
 *
 * @param integer $number number.
 *
 * @return string|integer returns string representation of time from given number or input number if it is greater or
 *                        equal than 86400.
 */
function is_time($number)
{
    if (is_numeric($number) && (int)$number >= 0 && (int)$number < 86400) {
        $time = (int)$number;
        $seconds = $time % 60;
        $minutes = (($time - $seconds) / 60) % 60;
        $hours = ((($time - $seconds) / 60) - $minutes) / 60;
        return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }
    return $number;
}

/**
 * Returns abbreviation of input text.
 *
 * @param string $input input text.
 *
 * @return string abbreviation.
 */
function abbreviation($input): string
{
    $letters = [];
    $words = explode(' ', $input);
    foreach ($words as $word) {
        $first_letter = (mb_substr($word, 0, 1));
        $second_letter = (mb_substr($word, 1, 1));
        if ($first_letter >= '0' && $first_letter <= '9') {
            $letters[] = $word;
        } else if (mb_strtolower($first_letter) === 'c' && mb_strtolower($second_letter) === 'h') {
            $letters[] = mb_strtoupper($first_letter) . 'h';
        } else if (mb_strtolower($first_letter) === 'd' && (mb_strtolower($second_letter) === 'z'
                || mb_strtolower($second_letter) === 'ž')
        ) {
            $letters[] = mb_strtoupper($first_letter) . mb_strtolower($second_letter);
        } else {
            $letters[] = mb_strtoupper($first_letter);
        }
    }
    return strtoupper(implode($letters));
}

/**
 * Converts all space characters to &nbsp; entity.
 *
 * @param string $input input text.
 *
 * @return string output text with converted spaces.
 */
function space_to_nbsp($input): string
{
    return preg_replace('/\s+/', '&nbsp;', $input);
}

/**
 * Returns id from url part. ID must be first number before underscore character.
 *
 * @param string $url_part url part.
 *
 * @return integer|null integer value of ID or NULL if nothing is found.
 */
function url_get_id($url_part): ?int
{
    if (is_null($url_part) || trim($url_part) === '') {
        return null;
    }
    $id = '';
    for ($i = 0, $iMax = mb_strlen($url_part); $i < $iMax; $i++) {
        $char = mb_substr($url_part, $i, 1);
        if ($char === '0' || $char === '1' || $char === '2' || $char === '3'
            || $char === '4' || $char === '5' || $char === '6' || $char === '7'
            || $char === '8' || $char === '9') {
            $id .= $char;
        } else {
            break;
        }
    }
    if ($id !== '') {
        return (int)$id;
    }
    return null;
}

/**
 * Normalizes text to be used in url.
 *
 * @param string $text                text to normalize for url.
 * @param string $prepend_if_possible optional string to prepend.
 *
 * @return string normalized text.
 */
function text_convert_for_url($text, $prepend_if_possible = '_'): string
{
    $text1 = mb_strtolower($text);
    $text2 = normalize($text1);
    $text3 = '';
    for ($i = 0, $iMax = mb_strlen($text2); $i < $iMax; $i++) {
        $char = mb_substr($text2, $i, 1);
        if (preg_match('/^[a-z0-9_\- ]$/', $char)) {
            $text3 .= str_replace(['-', ' '], '_', $char);
        }
    }
    $text4 = preg_replace('/[_]{2,}/', '_', $text3);
    return $text4 !== '' ? $prepend_if_possible . $text4 : '';
}

/**
 * Converts first letter in text to upper case and the rest to lower case.
 *
 * @param string $text text to convert.
 *
 * @return string converted text.
 */
function str_to_first_upper($text): string
{
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_strtolower(mb_substr($text, 1));
}

/**
 * Returns first name from full name.
 *
 * @param string $fullname full name.
 *
 * @return string first name.
 */
function get_firstname_from_fullname($fullname): string
{
    $end = mb_strrpos($fullname, ' ');
    if ($end !== false) {
        return mb_substr($fullname, 0, $end);
    }
    return $fullname;
}

/**
 * Returns last name from full name.
 *
 * @param string $fullname full name.
 *
 * @return string last name.
 */
function get_lastname_from_fullname($fullname): string
{
    $end = mb_strrpos($fullname, ' ');
    if ($end !== false) {
        return mb_substr($fullname, $end + 1);
    }
    return '';
}

/**
 * This function will replace some characters in evaluation table for numeric value.
 *
 * @param string|double $points numeric points or string chars.
 *
 * @return double numeric value.
 */
function valuation_table_col_points_to_data_order($points)
{
    if ($points === '*') {
        return -1000000;
    }
    if ($points === 'x') {
        return -999999;
    }
    if ($points === '-') {
        return -999998;
    }
    if ($points === '!') {
        return -999997;
    }
    return (double)$points;
}

/**
 * This function will indent text by given number of space characters.
 *
 * @param string  $text input text.
 * @param integer $for  number of space characters.
 *
 * @return string indented text.
 */
function text_indent($text, $for = 2): string
{
    $lines = explode("\n", $text);
    $output = '';
    for ($i = 0, $iMax = count($lines); $i < $iMax; $i++) {
        $output .= str_repeat(' ', $for) . rtrim($lines[$i]);
        if ($i < count($lines) - 1) {
            $output .= PHP_EOL;
        }
    }
    return $output;
}