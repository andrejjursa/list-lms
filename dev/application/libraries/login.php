<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('SESSION_AUTH_LOGIN_STUDENT', 'SESSION_STUDENT_DATA');
define('SESSION_AUTH_LOGIN_TEACHER', 'SESSION_TEACHER_DATA');

class Login {
    
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
            $userdata = array(
                'id' => $student->id,
                'email' => $student->email,
                'fullname' => $student->fullname,
            );
            $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, $userdata);
            $this->validate_student_login_verification(TRUE);
            return true;
        } else {
            $this->validate_student_login_verification(FALSE);
            return false;
        }
    }
    
    public function student_login_protected_redirect() {
        if (!$this->is_student_session_valid()) {
            $this->CI->load->helper('url');
            $redirects = $this->CI->config->item('login_redirects');
            redirect($redirects['student']);
            die();
        }
    }
    
    private function validate_student_login_verification($status = NULL) {
        if ($status === NULL || $status === TRUE || $status === FALSE) {
            $this->student_login_verified = $status;
        }
    }
    
}