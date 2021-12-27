<?php

/**
 * Scans directory and add all files with specified extensions to compare by MOSS.
 *
 * @param string        $cd_path    directory where the source directory is located.
 * @param string        $directory  directory name or file name.
 * @param array<string> $extensions lower case extension names without dot.
 */
function moss_add_all_files($cd_path, $directory, $extensions = [])
{
    $ci =& get_instance();
    $ci->load->library('mosslib');
    if (file_exists($cd_path . $directory) && is_array($extensions) && count($extensions)) {
        if (is_file($cd_path . $directory)) {
            $path_info = pathinfo($cd_path . $directory);
            if (in_array(strtolower($path_info['extension']), $extensions, true)) {
                $current_path = getcwd();
                chdir($cd_path);
                $ci->mosslib->addFile($directory);
                chdir($current_path);
            }
        } else {
            $dirs = scandir($cd_path . $directory);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $dir_path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $dir;
                    moss_add_all_files(
                        $cd_path,
                        $dir_path . (is_dir($dir_path) ? DIRECTORY_SEPARATOR : ''),
                        $extensions
                    );
                }
            }
        }
    }
}

/**
 * Scans directory and add all files with specified extensions as MOSS base file for comparation.
 *
 * @param string        $cd_path    directory where the base directory is located.
 * @param string        $directory  directory name or file name.
 * @param array<string> $extensions lower case extension names without dot.
 */
function moss_add_all_base_files($cd_path, $directory, $extensions = [])
{
    $ci =& get_instance();
    $ci->load->library('mosslib');
    if (file_exists($cd_path . $directory) && is_array($extensions) && count($extensions)) {
        if (is_file($cd_path . $directory)) {
            $path_info = pathinfo($cd_path . $directory);
            if (in_array(strtolower($path_info['extension']), $extensions, true)) {
                $current_path = getcwd();
                chdir($cd_path);
                $ci->mosslib->addBaseFile($directory);
                chdir($current_path);
            }
        } else {
            $dirs = scandir($cd_path . $directory);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..') {
                    $dir_path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $dir;
                    moss_add_all_base_files(
                        $cd_path,
                        $dir_path . (is_dir($dir_path) ? DIRECTORY_SEPARATOR : ''),
                        $extensions
                    );
                }
            }
        }
    }
}