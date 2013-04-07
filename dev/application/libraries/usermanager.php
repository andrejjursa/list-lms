<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('SESSION_AUTH_LOGIN_STUDENT', 'SESSION_STUDENT_DATA');
define('SESSION_AUTH_LOGIN_TEACHER', 'SESSION_TEACHER_DATA');
/**
 * User management library for LIST. This library can handle students and teachers.
 * @package LIST_Libraries
 * @author Andrej Jursa
 */
class Usermanager {
    
    /**
     * @var object $CI CodeIgniter.
     */
    private $CI = NULL;
    /**
     * @var boolean $student_login_verified contains information about student account authentication in script runtime.
     */
    private $student_login_verified = NULL;
    /**
     * @var boolean $teacher_login_verified contains information about teacher account authentication in script runtime.
     */
    private $teacher_login_verified = NULL;
    
    /**
     * Constructor ...
     */
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }
    
    /**
     * Performs one-time check if student account is authenticated, then returns this value as boolean.
     * @return boolean TRUE, if student account is authenticated, FALSE otherwise.
     */
    public function is_student_session_valid() {
        if (is_null($this->student_login_verified)) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            if (isset($userdata['id']) && intval($userdata['id']) > 0) {
                $student = new Student();
                $student->get_by_id(intval($userdata['id']));
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
    
    /**
     * Performs one-time check if teacher account is authenticated, then returns this value as boolean.
     * @return boolean TRUE, if teacher account is authenticated, FALSE otherwise.
     */
    public function is_teacher_session_valid() {
        if (is_null($this->teacher_login_verified)) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
            if (isset($userdata['id']) && intval($userdata['id']) > 0) {
                $teacher = new Teacher();
                $teacher->get_by_id(intval($userdata['id']));
                if ($teacher->exists()) {
                    $this->validate_teacher_login_verification(TRUE);
                } else {
                    $this->validate_teacher_login_verification(FALSE);
                }
            } else {
                $this->validate_teacher_login_verification(FALSE);
            }
        }
        return $this->teacher_login_verified;
    }
    
    /**
     * Performs student account authentification and returns boolean information about success.
     * @param string $eamil student account e-mail address.
     * @param string $password student account password in plain text form.
     * @return boolean TRUE, if student authentification is successful, FALSE otherwise (i.e. bad e-mail of password).
     */
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
            return TRUE;
        } else {
            $this->validate_student_login_verification(FALSE);
            return FALSE;
        }
    }
    
    /**
     * Performs teacher account authentification and returns boolean information about success.
     * @param string $eamil teacher account e-mail address.
     * @param string $password teacher account password in plain text form.
     * @return boolean TRUE, if teacher authentification is successful, FALSE otherwise (i.e. bad e-mail of password).
     */
    public function authenticate_teacher_login($email, $password) {
        $teacher = new Teacher();
        $teacher->where('email', $email);
        $teacher->where('password', sha1($password));
        $teacher->get();
        if ($teacher->exists()) {
            $userdata = $teacher->to_array();
            unset($userdata['password']);
            unset($userdata['created']);
            unset($userdata['updated']);
            $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_TEACHER, $userdata);
            $this->validate_teacher_login_verification(TRUE);
            return TRUE;
        } else {
            $this->validate_teacher_login_verification(FALSE);
            return FALSE;
        }
    }
    
    /**
     * Reloads student data from database to session.
     */
    public function refresh_student_userdata() {
        if ($this->is_student_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            $student = new Teacher();
            $student->get_by_id(@$userdata['id']);
            if ($student->exists()) {
                $userdata = $student->to_array();
                unset($userdata['password']);
                unset($userdata['created']);
                unset($userdata['updated']);
                $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, $userdata);
            }
        }
    }
    
    /**
     * Reloads teacher data from database to session.
     */
    public function refresh_teacher_userdata() {
        if ($this->is_teacher_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
            $teacher = new Teacher();
            $teacher->get_by_id(@$userdata['id']);
            if ($teacher->exists()) {
                $userdata = $teacher->to_array();
                unset($userdata['password']);
                unset($userdata['created']);
                unset($userdata['updated']);
                $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_TEACHER, $userdata);
            }
        }
    }
    
    /**
     * This function will redirects browser to login page for student when no student is authentificated.
     * @param boolean $send_current_url if this is set to TRUE (default), current url will be encoded and sent to login page, so user will be redirected back to it after successful login.
     */
    public function student_login_protected_redirect($send_current_url = TRUE) {
        if (!$this->is_student_session_valid()) {
            $current_url = encode_for_url($this->clear_current_url());
            $redirects = $this->CI->config->item('login_redirects');
            $redirect_student = $send_current_url ? ('/' . trim($redirects['student'], '/') . '/current_url/' . $current_url . '/') : '/' . trim($redirects['student'], '/') . '/';
            redirect(create_internal_url($redirect_student));
            die();
        }
    }
    
    /**
     * This function will redirects browser to login page for teacher when no teacher is authentificated.
     * @param boolean $send_current_url if this is set to TRUE (default), current url will be encoded and sent to login page, so user will be redirected back to it after successful login.
     */
    public function teacher_login_protected_redirect($send_current_url = TRUE) {
        if (!$this->is_teacher_session_valid()) {
            $current_url = encode_for_url($this->clear_current_url());
            $redirects = $this->CI->config->item('login_redirects');
            $redirect_student = $send_current_url ? ('/' . trim($redirects['teacher'], '/') . '/current_url/' . $current_url . '/') : '/' . trim($redirects['teacher'], '/') . '/';
            redirect(create_internal_url($redirect_student));
            die();
        }
    }
    
    /**
     * Returns language selected by authenticated student, or default language, if no student is authenticated.
     * @return string language code.
     */
    public function get_student_language() {
        if ($this->is_student_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            return $userdata['language'];
        }
        return $this->CI->config->item('language');
    }
    
    /**
     * Returns language selected by authenticated teacher, or default language, if no teacher is authenticated.
     * @return string language code.
     */
    public function get_teacher_language() {
        if ($this->is_teacher_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
            return $userdata['language'];
        }
        return $this->CI->config->item('language');
    }
    
    /**
     * Returns currently authenticated student id or zero if student is not loged in.
     * @return integet student id.
     */
    public function get_student_id() {
        if ($this->is_student_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            return intval(@$userdata['id']);
        }
        return 0;
    }
    
    /**
     * Returns currently authenticated teacher id or zero if teacher is not loged in.
     * @return integet teacher id.
     */
    public function get_teacher_id() {
        if ($this->is_teacher_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
            return intval(@$userdata['id']);
        }
        return 0;
    }
    
    /**
     * This function will remove student session data and log him off.
     */
    public function do_student_logout() {
        $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, array());
        $this->validate_student_login_verification(FALSE);
    }
    
    /**
     * This function will remove teacher session data and log him off.
     */
    public function do_teacher_logout() {
        $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_TEACHER, array());
        $this->validate_teacher_login_verification(FALSE);
    }
    
    /**
     * This function will send data from student session to smarty template variable called 'list_student_account'.
     */
    public function set_student_data_to_smarty() {
        $this->CI->load->library('parser');
        if ($this->is_student_session_valid()) {
            $this->CI->parser->assign('list_student_account', $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT));
        } else {
            $this->CI->parser->assign('list_student_account', array());
        }
    }
    
    /**
     * This function will send data from teacher session to smarty template variable called 'list_teacher_account'.
     */
    public function set_teacher_data_to_smarty() {
        $this->CI->load->library('parser');
        if ($this->is_teacher_session_valid()) {
            $this->CI->parser->assign('list_teacher_account', $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER));
        } else {
            $this->CI->parser->assign('list_teacher_account', array());
        }
    }
    
    /**
     * This internal function will set the verification status of student.
     * @param boolean $status status can be TRUE, FALSE or NULL.
     */
    protected function validate_student_login_verification($status = NULL) {
        if ($status === NULL || $status === TRUE || $status === FALSE) {
            $this->student_login_verified = $status;
        }
    }
    
    /**
     * This internal function will set the verification status of teacher.
     * @param boolean $status status can be TRUE, FALSE or NULL.
     */
    protected function validate_teacher_login_verification($status = NULL) {
        if ($status === NULL || $status === TRUE || $status === FALSE) {
            $this->teacher_login_verified = $status;
        }
    }
    
    /**
     * This function returns current url with respect to rewrite engien setting.
     * I.E. it clears $config['index_page'] from current url.
     * @return string current url.
     */
    protected function clear_current_url() {
        $current_url = current_url();
        if ($this->CI->config->item('rewrite_engine_enabled')) {
            $current_url = str_replace(array($this->CI->config->item('index_page') . '/', $this->CI->config->item('index_page')), array('', ''), $current_url);
        }
        return $current_url;
    }
    
}