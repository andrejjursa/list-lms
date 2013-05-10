<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tasks controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Tasks extends LIST_Controller {
    
    public function index() {
        $this->load->library('migration');
        $this->migration->version(8);
        show_error($this->migration->error_string());
    }
    
    public function download_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . intval($task_id) . '/' . $filename;
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($filepath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            ob_clean();
            flush();
            readfile($filepath);
            exit;
        } else {
            $this->output->set_status_header(404, 'Not found');
        }
    }
    
    public function download_hidden_file($task_id, $file) {
        $filename = decode_from_url($file);
        $filepath = 'private/uploads/task_files/task_' . intval($task_id) . '/hidden/' . $filename;
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($filepath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            ob_clean();
            flush();
            readfile($filepath);
            exit;
        } else {
            $this->output->set_status_header(404, 'Not found');
        }
    }
    
}