<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH . 'controllers/admin/course_content.php';

/**
 * Course content controller for frontend.
 * @package LIST_FE_Controllers
 * @author Andrej Jursa
 */
class Content extends LIST_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_student();
        $this->_load_student_langfile();
    }
    
    public function index() {
    
    }
    
    public function download_file($path, $language, $file) {
        if (is_numeric($path)) {
            $course_content = new Course_content_model();
            $course_content->get_by_id($path);
            
            if (!$course_content->exists()) {
                $this->file_not_found();
                return;
            }
            
            if (!$this->usermanager->is_teacher_session_valid()) {
                if (!$course_content->public) {
                    $this->usermanager->student_login_protected_redirect();
                }
                
                if (!$course_content->get_is_published()) {
                    $this->file_not_found();
                    return;
                }
            }
            
            $this->do_download_file($path, $language, decode_from_url($file));
        } else {
            $this->usermanager->teacher_login_protected_redirect();
            
            $this->do_download_file($path, $language, decode_from_url($file));
        }
    }
    
    private function do_download_file($path, $language, $file) {
        $languages = $this->lang->get_list_of_languages();
        
        if (!array_key_exists($language, $languages)) {
            $language = '';
        }
    
        $filepath = realpath(Course_Content::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $path . ($language != '' ? DIRECTORY_SEPARATOR . $language : '') . DIRECTORY_SEPARATOR . $file;
    
        if (file_exists($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename='.basename($filepath));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            ob_clean();
            flush();
            $f = fopen($filepath, 'r');
            while (!feof($f)) {
                echo fread($f, 1024);
            }
            fclose($f);
            exit;
        } else {
            $this->file_not_found();
        }
    }
    
    private function file_not_found() {
        $heading = 'File not found';
        $message = 'Requested file was not found. It may be moved, deleted or may be hidden from public.';
        $file = file_get_contents(APPPATH . 'errors/error_404.php');
        ob_start();
        eval('?>' . $file);
        $output = ob_get_clean();
        $this->output->set_status_header(404, 'Not found');
        $this->output->set_output($output);
    }
}