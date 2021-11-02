<?php

/**
 * Plupload library.
 *
 * @package LIST_Libraries
 * @author  Andrej Jursa
 */
class Plupload
{
    
    /**
     * @var object codeIgniter object.
     */
    private $CI;
    
    /**
     * Main constructor, will loads codeigniter instance and langfile for plupload.
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->lang->load('plupload');
    }
    
    /**
     * Realises upload procedure by plupload.
     * WARNING: calling this method will brake code execution and return JSON response in all cases.
     *
     * @param string $targetDir target directory.
     */
    public function do_upload($targetDir)
    {
        $this->CI->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        $this->CI->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->CI->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->CI->output->set_header('Cache-Control: post-check=0, pre-check=0');
        $this->CI->output->set_header('Pragma: no-cache');
        
        $cleanupTargetDir = true; // Remove old files
        
        @set_time_limit(5 * 60);
        
        $chunk = isset($_REQUEST["chunk"]) ? (int)$_REQUEST["chunk"] : 0;
        $chunks = isset($_REQUEST["chunks"]) ? (int)$_REQUEST["chunks"] : 0;
        $fileName = preg_replace('/[^\w\._]+/', '_', ($_REQUEST["name"] ?? ''));
        
        $filePath = rtrim($targetDir, '\\/') . DIRECTORY_SEPARATOR . $fileName;
        
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, DIR_READ_MODE);
        }
        
        if ($cleanupTargetDir && !$this->remove_temporary_files($targetDir, $filePath)) {
            die(
                '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "' .
                htmlspecialchars(
                    $this->CI->lang->line('plupload_error_message_temp_directory_open_failed'),
                    ENT_QUOTES
                ) . '"}, "id" : "id"}'
            );
        }
        
        $contentType = '';
        if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];
        }
        if (isset($_SERVER["CONTENT_TYPE"])) {
            $contentType = $_SERVER["CONTENT_TYPE"];
        }
        
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = @fopen("{$filePath}.upload_part", $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = @fopen($_FILES['file']['tmp_name'], "rb");
                    
                    if ($in) {
                        while ($buff = fread($in, 4096)) {
                            fwrite($out, $buff);
                        }
                    } else {
                        die(
                            '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "'
                            . htmlspecialchars(
                                $this->CI->lang->line('plupload_error_message_input_stream_open_failed'),
                                ENT_QUOTES
                            ) . '"}, "id" : "id"}'
                        );
                    }
                    @fclose($in);
                    @fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else {
                    die(
                        '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "'
                        . htmlspecialchars(
                            $this->CI->lang->line('plupload_error_message_output_stream_open_failed'),
                            ENT_QUOTES
                        ) . '"}, "id" : "id"}'
                    );
                }
            } else {
                die(
                    '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "'
                    . htmlspecialchars(
                        $this->CI->lang->line('plupload_error_message_move_uploaded_file_failed'),
                        ENT_QUOTES
                    ) . '"}, "id" : "id"}'
                );
            }
        } else {
            // Open temp file
            $out = @fopen("{$filePath}.upload_part", $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = @fopen("php://input", "rb");
                
                if ($in) {
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                } else {
                    die(
                        '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "'
                        . htmlspecialchars(
                            $this->CI->lang->line('plupload_error_message_input_stream_open_failed'),
                            ENT_QUOTES
                        ) . '"}, "id" : "id"}'
                    );
                }
                @fclose($in);
                @fclose($out);
            } else {
                die(
                    '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "'
                    . htmlspecialchars(
                        $this->CI->lang->line('plupload_error_message_output_stream_open_failed'),
                        ENT_QUOTES
                    ) . '"}, "id" : "id"}'
                );
            }
        }
        
        if (!$chunks || $chunk == $chunks - 1) {
            $oldFilePath = $filePath;
            if (file_exists($filePath)) {
                $filePath = $targetDir . DIRECTORY_SEPARATOR . $this->get_new_filename($fileName, $targetDir);
            }
            rename("{$oldFilePath}.upload_part", $filePath);
        }
        
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }
    
    /**
     * Clear target directory for all old temporary files.
     *
     * @param string $targetDir target directory to be clear.
     *
     * @return boolean information about successfullnes of operation.
     */
    public function clear_temporary_files($targetDir)
    {
        return $this->remove_temporary_files($targetDir);
    }
    
    /**
     * Return new filename if provided one is already taken by another file in terget directory.
     *
     * @param string $fileName  current file name.
     * @param string $targetDir target directory.
     *
     * @return string new name.
     */
    private function get_new_filename($fileName, $targetDir)
    {
        $ext = strrpos($fileName, '.');
        $fileName_a = substr($fileName, 0, $ext);
        $fileName_b = substr($fileName, $ext);
        
        $count = 1;
        while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b)) {
            $count++;
        }
        return $fileName_a . '_' . $count . $fileName_b;
    }
    
    /**
     * Remove all temporary files from target directory, except the one specified in $filePath.
     *
     * @param string $targetDir target directory.
     * @param string $filePath  path to file which is not to be deleted (i.e. it is currently uploaded file).
     *
     * @return boolean TRUE, when directory is scaned and files are removed, FALSE when target directory is not
     *                 directory or can't be opened.
     */
    private function remove_temporary_files($targetDir, $filePath = '')
    {
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        
        if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                
                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.upload_part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)
                    && ($tmpfilePath !== "{$filePath}.upload_part")
                ) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
            return true;
        }
    
        return false;
    }
    
}