<?php

/**
 * Scans directory and add all files with specified extensions to compare by MOSS.
 * @param string $directory directory name or file name.
 * @param array<string> $extensions lower case extension names without dot.
 */
function moss_add_all_files($directory, $extensions = array()) {
    $ci =& get_instance();
    $ci->load->library('mosslib');
    if (file_exists($directory) && is_array($extensions) && count($extensions)) {
        if (is_file($directory)) {
            $path_info = pathinfo($directory);
            if (in_array(strtolower($path_info['extension']), $extensions)) {
                $ci->mosslib->addFile($directory);
            }
        } else {
            $dirs = scandir($directory);
            foreach ($dirs as $dir) {
                if ($dir != '.' && $dir != '..') {
                    $dir_path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $dir;
                    moss_add_all_files($dir_path . (is_dir($dir_path) ? DIRECTORY_SEPARATOR : ''), $extensions);
                }
            }
        }
    }   
}

/**
 * Scans directory and add all files with specified extensions as MOSS base file for comparation.
 * @param string $directory directory name or file name.
 * @param array<string> $extensions lower case extension names without dot.
 */
function moss_add_all_base_files($directory, $extensions = array()) {
    $ci =& get_instance();
    $ci->load->library('mosslib');
    if (file_exists($directory) && is_array($extensions) && count($extensions)) {
        if (is_file($directory)) {
            $path_info = pathinfo($directory);
            if (in_array(strtolower($path_info['extension']), $extensions)) {
                $ci->mosslib->addBaseFile($directory);
            }
        } else {
            $dirs = scandir($directory);
            foreach ($dirs as $dir) {
                if ($dir != '.' && $dir != '..') {
                    $dir_path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $dir;
                    moss_add_all_files($dir_path . (is_dir($dir_path) ? DIRECTORY_SEPARATOR : ''), $extensions);
                }
            }
        }
    }   
}