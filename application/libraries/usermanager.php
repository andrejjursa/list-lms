<?php

define('SESSION_AUTH_LOGIN_STUDENT', 'SESSION_STUDENT_DATA');
define('SESSION_AUTH_LOGIN_TEACHER', 'SESSION_TEACHER_DATA');

/**
 * User management library for LIST. This library can handle students and teachers.
 *
 * @package LIST_Libraries
 * @author  Andrej Jursa
 */
class Usermanager
{
    
    public const ACCOUNT_TYPE_TEACHER = 'teacher';
    public const ACCOUNT_TYPE_STUDENT = 'student';
    
    /**
     * @var object $CI CodeIgniter.
     */
    private $CI = null;
    /**
     * @var boolean $student_login_verified contains information about student account authentication in script runtime.
     */
    private $student_login_verified = null;
    /**
     * @var boolean $teacher_login_verified contains information about teacher account authentication in script runtime.
     */
    private $teacher_login_verified = null;
    
    /**
     * Constructor ...
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }
    
    /**
     * Performs one-time check if student account is authenticated, then returns this value as boolean.
     *
     * @return boolean TRUE, if student account is authenticated, FALSE otherwise.
     */
    public function is_student_session_valid(): ?bool
    {
        if (is_null($this->student_login_verified)) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            if (isset($userdata['id']) && (int)$userdata['id'] > 0) {
                $student = new Student();
                $student->get_by_id((int)$userdata['id']);
                if ($student->exists()) {
                    $this->validate_student_login_verification(true);
                } else {
                    $this->validate_student_login_verification(false);
                }
            } else {
                $this->validate_student_login_verification(false);
            }
        }
        return $this->student_login_verified;
    }
    
    /**
     * Performs one-time check if teacher account is authenticated, then returns this value as boolean.
     *
     * @return boolean TRUE, if teacher account is authenticated, FALSE otherwise.
     */
    public function is_teacher_session_valid(): ?bool
    {
        if (is_null($this->teacher_login_verified)) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
            if (isset($userdata['id']) && (int)$userdata['id'] > 0) {
                $teacher = new Teacher();
                $teacher->get_by_id((int)$userdata['id']);
                if ($teacher->exists()) {
                    $this->validate_teacher_login_verification(true);
                } else {
                    $this->validate_teacher_login_verification(false);
                }
            } else {
                $this->validate_teacher_login_verification(false);
            }
        }
        return $this->teacher_login_verified;
    }
    
    /**
     * Performs student account authentification and returns boolean information about success.
     *
     * @param string $eamil    student account e-mail address.
     * @param string $password student account password in plain text form.
     *
     * @return boolean TRUE, if student authentification is successful, FALSE otherwise (i.e. bad e-mail of password).
     */
    public function authenticate_student_login($email, $password): bool
    {
        $student = new Student();
        $student->where('email', $email);
        $student->where('password', sha1($password));
        $student->get();
        if ($student->exists()) {
            $userdata = $student->to_array();
            unset($userdata['password'], $userdata['created'], $userdata['updated']);
            $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, $userdata);
            $this->validate_student_login_verification(true);
            $log = new Log();
            $log->add_student_login_log(
                $this->CI->lang->line('students_login_successful_log_message'),
                null,
                $this->get_student_id()
            );
            return true;
        }
        
        $this->validate_student_login_verification(false);
        $this->add_login_failed_record($email, self::ACCOUNT_TYPE_STUDENT);
        return false;
    }
    
    /**
     * Enforce student login.
     *
     * @param Student $student student model with loaded student account.
     *
     * @return boolean TRUE, if student authentification is successful, FALSE otherwise (i.e. $student is not loaded).
     */
    public function force_student_login(Student $student): bool
    {
        if ($student->exists()) {
            $userdata = $student->to_array();
            unset($userdata['password'], $userdata['created'], $userdata['updated']);
            $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, $userdata);
            $this->validate_student_login_verification(true);
            $log = new Log();
            $log->add_student_login_log(
                $this->CI->lang->line('students_login_forced_log_message'),
                $this->get_teacher_id(),
                $this->get_student_id()
            );
            return true;
        }
        
        return false;
    }
    
    /**
     * Performs teacher account authentification and returns boolean information about success.
     *
     * @param string $eamil    teacher account e-mail address.
     * @param string $password teacher account password in plain text form.
     *
     * @return boolean TRUE, if teacher authentification is successful, FALSE otherwise (i.e. bad e-mail of password).
     */
    public function authenticate_teacher_login($email, $password): bool
    {
        $teacher = new Teacher();
        $teacher->where('email', $email);
        $teacher->where('password', sha1($password));
        $teacher->get();
        if ($teacher->exists()) {
            $userdata = $teacher->to_array();
            unset($userdata['password'], $userdata['created'], $userdata['updated']);
            $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_TEACHER, $userdata);
            $this->validate_teacher_login_verification(true);
            return true;
        }
        
        $this->validate_teacher_login_verification(false);
        $this->add_login_failed_record($email, self::ACCOUNT_TYPE_TEACHER);
        return false;
    }
    
    /**
     * Check if login attemts exceed allowed number of attempts.
     *
     * @param string $email    e-mail address of account.
     * @param string $acc_type type of account (ACCOUNT_TYPE_* constant).
     *
     * @return boolean TRUE, if number of login attempts exceed allowed, FALSE otherwise.
     */
    public function is_login_attempts_exceeded($email, $acc_type = self::ACCOUNT_TYPE_TEACHER): bool
    {
        $account_type = $acc_type == self::ACCOUNT_TYPE_TEACHER ? 'teacher' : 'student';
        $this->CI->config->load('datamapper');
        $datamapper = $this->CI->config->item('datamapper');
        $browser = str_replace(["\n", "\r"], ['', ''], substr($_SERVER['HTTP_USER_AGENT'], 0, 255));
        $security = new Security();
        $security->where('account_type', $account_type);
        $security->where('account_email', $email);
        $security->where('login_ip_address', getenv('REMOTE_ADDR'));
        $security->where('login_browser', $browser);
        $security->where(
            'login_failed_time >=',
            date(
                $datamapper['timestamp_format'],
                strtotime(
                    'now - ' . $this->CI->config->item($account_type . '_login_security_timeout') . ' minutes'
                )
            )
        );
        $failed_times = $security->count();
        return $failed_times > $this->CI->config->item($account_type . '_login_security_allowed_attempts');
    }
    
    /**
     * Save record of failed login attempt.
     *
     * @param string $email    e-mail address of account.
     * @param string $acc_type type of account (ACCOUNT_TYPE_* constant).
     */
    public function add_login_failed_record($email, $acc_type = self::ACCOUNT_TYPE_TEACHER): void
    {
        $account_type = $acc_type == self::ACCOUNT_TYPE_TEACHER ? 'teacher' : 'student';
        $this->CI->config->load('datamapper');
        $datamapper = $this->CI->config->item('datamapper');
        $browser = str_replace(["\n", "\r"], ['', ''], substr($_SERVER['HTTP_USER_AGENT'], 0, 255));
        $security = new Security();
        $security->account_type = $account_type;
        $security->account_email = $email;
        $security->login_ip_address = getenv('REMOTE_ADDR');
        $security->login_browser = $browser;
        $security->login_failed_time = date($datamapper['timestamp_format']);
        $security->save();
    }
    
    /**
     * Reloads student data from database to session.
     */
    public function refresh_student_userdata(): void
    {
        if ($this->is_student_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            $student = new Student();
            $student->get_by_id(@$userdata['id']);
            if ($student->exists()) {
                $userdata = $student->to_array();
                unset($userdata['password'], $userdata['created'], $userdata['updated']);
                $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, $userdata);
            }
        }
    }
    
    /**
     * Reloads teacher data from database to session.
     */
    public function refresh_teacher_userdata(): void
    {
        if ($this->is_teacher_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
            $teacher = new Teacher();
            $teacher->get_by_id(@$userdata['id']);
            if ($teacher->exists()) {
                $userdata = $teacher->to_array();
                unset($userdata['password'], $userdata['created'], $userdata['updated']);
                $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_TEACHER, $userdata);
            }
        }
    }
    
    /**
     * Set new default teacher language idiom to database and session. Teacher must be loged in!
     *
     * @param string $language the language idiom to be set as default for teacher account.
     *
     * @return boolean TRUE, if language is set and session stored, FALSE otherwise.
     */
    public function set_teacher_language($language): bool
    {
        if ($this->is_teacher_session_valid()) {
            $all_langs = $this->CI->lang->get_list_of_languages();
            if (array_key_exists($language, $all_langs)) {
                $teacher = new Teacher();
                $teacher->get_by_id($this->get_teacher_id());
                $teacher->language = $language;
                if ($teacher->save()) {
                    $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
                    $userdata['language'] = $language;
                    $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_TEACHER, $userdata);
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * This function will redirects browser to login page for student when no student is authentificated.
     *
     * @param boolean $send_current_url if this is set to TRUE (default), current url will be encoded and sent to login
     *                                  page, so user will be redirected back to it after successful login.
     */
    public function student_login_protected_redirect($send_current_url = true): void
    {
        if (!$this->is_student_session_valid()) {
            $current_url = encode_for_url($this->clear_current_url());
            $redirects = $this->CI->config->item('login_redirects');
            $redirect_student = $send_current_url
                ? ('/' . trim($redirects['student'], '/') . '/current_url/' . $current_url . '/')
                : '/' . trim($redirects['student'], '/') . '/';
            $this->CI->messages->keep_messages();
            redirect(create_internal_url($redirect_student));
            die();
        }
    }
    
    /**
     * This function will redirects browser to login page for teacher when no teacher is authentificated.
     *
     * @param boolean $send_current_url if this is set to TRUE (default), current url will be encoded and sent to login
     *                                  page, so user will be redirected back to it after successful login.
     */
    public function teacher_login_protected_redirect($send_current_url = true): void
    {
        if (!$this->is_teacher_session_valid()) {
            $current_url = encode_for_url($this->clear_current_url());
            $redirects = $this->CI->config->item('login_redirects');
            $redirect_student = $send_current_url
                ? ('/' . trim($redirects['teacher'], '/') . '/current_url/' . $current_url . '/')
                : '/' . trim($redirects['teacher'], '/') . '/';
            $this->CI->messages->keep_messages();
            redirect(create_internal_url($redirect_student));
            die();
        }
    }
    
    /**
     * Returns language selected by authenticated student, or default language, if no student is authenticated.
     *
     * @return string language code.
     */
    public function get_student_language(): string
    {
        if ($this->is_student_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            return $userdata['language'];
        }
        return $this->CI->config->item('language');
    }
    
    /**
     * Returns language selected by authenticated teacher, or default language, if no teacher is authenticated.
     *
     * @return string language code.
     */
    public function get_teacher_language(): string
    {
        if ($this->is_teacher_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
            return $userdata['language'];
        }
        return $this->CI->config->item('language');
    }
    
    /**
     * Returns currently authenticated student id or zero if student is not loged in.
     *
     * @return int student id.
     */
    public function get_student_id(): int
    {
        if ($this->is_student_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT);
            return (int)@$userdata['id'];
        }
        return 0;
    }
    
    /**
     * Returns currently authenticated teacher id or zero if teacher is not loged in.
     *
     * @return int teacher id.
     */
    public function get_teacher_id(): int
    {
        if ($this->is_teacher_session_valid()) {
            $userdata = $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER);
            return (int)@$userdata['id'];
        }
        return 0;
    }
    
    /**
     * This function will remove student session data and log him off.
     */
    public function do_student_logout(): void
    {
        $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_STUDENT, []);
        $this->validate_student_login_verification(false);
    }
    
    /**
     * This function will remove teacher session data and log him off.
     */
    public function do_teacher_logout(): void
    {
        $this->CI->session->set_userdata(SESSION_AUTH_LOGIN_TEACHER, []);
        $this->validate_teacher_login_verification(false);
    }
    
    /**
     * This function will send data from student session to smarty template variable called 'list_student_account' and
     * create variable 'list_student_account_model' with student model.
     */
    public function set_student_data_to_smarty(): void
    {
        $this->CI->load->library('parser');
        $student = new Student();
        if ($this->is_student_session_valid()) {
            $student->get_by_id($this->get_student_id());
            $this->CI->parser->assign(
                'list_student_account',
                $this->CI->session->userdata(SESSION_AUTH_LOGIN_STUDENT)
            );
        } else {
            $this->CI->parser->assign('list_student_account', []);
        }
        $this->CI->parser->assign('list_student_account_model', $student);
    }
    
    /**
     * This function will send data from teacher session to smarty template variable called 'list_teacher_account'.
     */
    public function set_teacher_data_to_smarty(): void
    {
        $this->CI->load->library('parser');
        if ($this->is_teacher_session_valid()) {
            $this->CI->parser->assign(
                'list_teacher_account',
                $this->CI->session->userdata(SESSION_AUTH_LOGIN_TEACHER)
            );
        } else {
            $this->CI->parser->assign('list_teacher_account', []);
        }
    }
    
    /**
     * This function returns current url with respect to rewrite engien setting.
     * I.E. it clears $config['index_page'] from current url.
     *
     * @return string current url.
     */
    public function clear_current_url(): string
    {
        $current_url = current_url();
        if ($this->CI->config->item('rewrite_engine_enabled') && is_mod_rewrite_enabled()) {
            $current_url = str_replace(
                [
                    $this->CI->config->item('index_page') . '/',
                    $this->CI->config->item('index_page'),
                ],
                ['', ''],
                $current_url
            );
        }
        return $current_url;
    }
    
    /**
     * Construct student cache id.
     *
     * @param mixed* zero or more parameters of cache id.
     *
     * @return string cache id.
     */
    public function get_student_cache_id(): string
    {
        $args = func_get_args();
        $cache_id = 'student_' . $this->get_student_id() . '|lang_' . $this->get_student_language();
        if (count($args)) {
            foreach ($args as $arg) {
                if ($arg != '') {
                    $cache_id .= '|' . $arg;
                }
            }
        }
        return $cache_id;
    }
    
    /**
     * Construct simple student cache id, based only at student id.
     *
     * @return string cache id.
     */
    public function get_student_simple_cache_id($student_id = null): string
    {
        return 'student_' . (is_null($student_id) ? $this->get_student_id() : $student_id);
    }
    
    
    /**
     * This internal function will set the verification status of student.
     *
     * @param boolean $status status can be TRUE, FALSE or NULL.
     */
    protected function validate_student_login_verification($status = null): void
    {
        if ($status === null || $status === true || $status === false) {
            $this->student_login_verified = $status;
        }
    }
    
    /**
     * This internal function will set the verification status of teacher.
     *
     * @param boolean $status status can be TRUE, FALSE or NULL.
     */
    protected function validate_teacher_login_verification($status = null): void
    {
        if ($status === null || $status === true || $status === false) {
            $this->teacher_login_verified = $status;
        }
    }
    
}