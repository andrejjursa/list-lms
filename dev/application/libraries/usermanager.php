<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('SESSION_AUTH_LOGIN_STUDENT', 'SESSION_STUDENT_DATA');
define('SESSION_AUTH_LOGIN_TEACHER', 'SESSION_TEACHER_DATA');

class Usermanager {
    
    private $CI = NULL;
    private $student_login_verified = NULL;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }
    
    public function is_student_session_valid() {
        if (is_null($this->student_login_verified)) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            if (isset($userdata['id']) && intval($userdata['id']) > 0) {
                $student = new Student();
                $student->get_where(array('id' => intval($userdata['id'])));
                if ($student->exists()) {
                    $this->validate_student_login_verification(TRUE);
                } else {
                    $this->validate_student_login_verification(FALSE);
                }
            } else {
                $this->validate_student_login_verification(FALSE);
            }
        }
        return $this->student_login_verified;
    }
    
    public function authenticate_student_login($email, $password) {
        $student = new Student();
        $student->where('email', $email);
        $student->where('password', sha1($password));
        $student->get();
        if ($student->exists()) {
            $userdata = $student->to_array();
            unset($userdata['password']);
            unset($userdata['created']);
            unset($userdata['updated']);
            $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, $userdata);
            $this->validate_student_login_verification(TRUE);
            return true;
        } else {
            $this->validate_student_login_verification(FALSE);
            return false;
        }
    }
    
    public function student_login_protected_redirect($send_current_url = TRUE) {
        if (!$this->is_student_session_valid()) {
            $current_url = encode_for_url(current_url());
            $this->CI->load->helper('url');
            $redirects = $this->CI->config->item('login_redirects');
            $redirect_student = $send_current_url ? ('/' . trim($redirects['student'], '/') . '/current_url/' . $current_url . '/') : '/' . trim($redirects['student'], '/') . '/';
            redirect(create_internal_url($redirect_student));
            die();
        }
    }
    
    public function get_student_language() {
        if ($this->is_student_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            return $userdata['language'];
        }
        return $this->CI->config->item('language');
    }
    
    public function do_student_logout() {
        if ($this->is_student_session_valid()) {
            $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, array());
            $this->validate_student_login_verification(FALSE);
        }
    }
    
    private function validate_student_login_verification($status = NULL) {
        if ($status === NULL || $status === TRUE || $status === FALSE) {
            $this->student_login_verified = $status;
        }
    }
    
}