<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Students controller for backend.
 * @package LIST_BE_Controllers
 * @author Andrej Jursa
 */
class Students extends LIST_Controller {
    
    const STORED_FILTER_SESSION_NAME = 'admin_students_filter_data';
    
    public function __construct() {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index() {
        $this->_select_teacher_menu_pagetag('students_manager');
        $this->parser->add_js_file('admin_students/list.js');
        $this->parser->add_css_file('admin_students.css');
        $this->inject_stored_filter();
        $this->parser->parse('backend/students/index.tpl');
    }
    
    public function new_student_form() {
        $this->parser->parse('backend/students/new_student_form.tpl');
    }
    
    public function table_content() {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $students = new Student();
        if (isset($filter['fullname']) && trim($filter['fullname']) != '') {
            $students->like('fullname', trim($filter['fullname']));
        }
        if (isset($filter['email']) && trim($filter['email']) != '') {
            $students->like('email', trim($filter['email']));
        }
        $order_by_direction = $filter['order_by_direction'] == 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] == 'fullname') {
            $students->order_by_as_fullname('fullname', $order_by_direction);
        } elseif ($filter['order_by_field'] == 'email') {
            $students->order_by('email', $order_by_direction);
        }
        $students->get_paged_iterated(isset($filter['page']) ? intval($filter['page']) : 1, isset($filter['rows_per_page']) ? intval($filter['rows_per_page']) : 25);
        $this->parser->parse('backend/students/table_content.tpl', array('students' => $students));
    }
    
    public function create() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('student[fullname]', 'lang:admin_students_form_field_fullname', 'required|max_length[255]');
        $this->form_validation->set_rules('student[email]', 'lang:admin_students_form_field_email', 'required|valid_email|is_unique[students.email]');
        $this->form_validation->set_rules('student[password]', 'lang:admin_students_form_field_password', 'required|min_length[6]|max_length[20]');
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $student_data = $this->input->post('student');
            $student = new Student();
            $student->from_array($student_data, array('fullname', 'email'));
            $student->password = sha1($student_data['password']);
            $student->language = $this->config->item('language');
            if ($student->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message('lang:admin_students_account_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_students_account_save_fail', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_students/new_student_form'));
        } else {
            $this->new_student_form();
        }
    }
    
    public function edit() {
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->_select_teacher_menu_pagetag('students_manager');
        $url = $this->uri->ruri_to_assoc(3);
        $student_id = isset($url['student_id']) ? intval($url['student_id']) : 0;
        $student = new Student();
        $student->get_by_id($student_id);
        $this->parser->parse('backend/students/edit.tpl', array('student' => $student));
    }
    
    public function update() {
        $this->usermanager->teacher_login_protected_redirect();
        
        $this->load->library('form_validation');
        
        $student_id = intval($this->input->post('student_id'));
        
        $this->form_validation->set_rules('student[fullname]', 'lang:admin_students_form_field_fullname', 'required|max_length[255]');
        $this->form_validation->set_rules('student[email]', 'lang:admin_students_form_field_email', 'required|valid_email|callback__email_available[' . $student_id . ']');
        $this->form_validation->set_rules('student[password]', 'lang:admin_students_form_field_password', 'min_length_optional[6]|max_length_optional[20]');
        $this->form_validation->set_message('_email_available', $this->lang->line('admin_students_form_error_email_not_available'));
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $student = new Student();
            $student->get_by_id($student_id);
            if ($student->exists()) {
                $student_data = $this->input->post('student');
                $student->from_array($student_data, array('fullname', 'email'));
                if (isset($student_data['password']) && !empty($student_data['password'])) {
                    $student->password = sha1($student_data['password']);
                }
                if ($student->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message('lang:admin_students_account_save_successful', Messages::MESSAGE_TYPE_SUCCESS);
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message('lang:admin_students_account_save_fail', Messages::MESSAGE_TYPE_ERROR);
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message('lang:admin_students_student_not_found', Messages::MESSAGE_TYPE_ERROR);
            }
            redirect(create_internal_url('admin_students'));
        } else {
            $this->edit();
        }
    }
    
    public function _email_available($str, $student_id) {
        $student = new Student();
        $student->where('email', $str)->where('id !=', $student_id);
        $count = $student->count();
        return $count == 0;
    }
    
    public function delete() {
        $this->output->set_content_type('application/json');
        $this->usermanager->teacher_login_protected_redirect();
        $url = $this->uri->ruri_to_assoc(3);
        $student_id = isset($url['student_id']) ? intval($url['student_id']) : 0;
        if ($student_id != 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $student = new Student();
            $student->get_by_id($student_id);
            $student->delete();
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $this->output->set_output(json_encode(TRUE));    
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(FALSE));                
            }
        } else {
            $this->output->set_output(json_encode(FALSE));
        }
    }
    
    public function csv_import() {
        $this->_select_teacher_menu_pagetag('students_manager');
        $this->parser->parse('backend/students/csv_import.tpl');
    }
    
    public function upload_csv_file() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('csv_data[delimiter]', 'lang:admin_students_csv_import_form_field_delimiter', 'required|exact_length[1]');
        $this->form_validation->set_rules('csv_data[enclosure]', 'lang:admin_students_csv_import_form_field_enclosure', 'required|exact_length[1]');
        $this->form_validation->set_rules('csv_data[escape]', 'lang:admin_students_csv_import_form_field_escape', 'required|exact_length[1]');
        if ($this->form_validation->run()) {
            $config = array(
                'upload_path' => './private/uploads/csv_imports',
                'file_name' => 'students_import_' . date('U') . '_' . rand(10000, 99999) . '.csv',
                'allowed_types' => 'csv',
                'overwrite' => FALSE,
            );
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('csv_file')) {
                $upload_data = $this->upload->data();
                $post_data = $this->input->post('csv_data');
                $data = array(
                    'f' => $upload_data['file_name'],
                    'd' => $post_data['delimiter'],
                    'c' => $post_data['enclosure'],
                    'e' => $post_data['escape'],
                );
                redirect(create_internal_url('admin_students/show_csv_content/' . encode_for_url(serialize($data))));
            } else {
                $this->parser->assign('error_message', $this->upload->display_errors('', ''));
                $this->csv_import();
            }
        } else {
            $this->csv_import();
        }
    }
    
    public function show_csv_content($config) {
        $this->_select_teacher_menu_pagetag('students_manager');
        $this->parser->assign('url_config', $config);
        $csv_data = unserialize(decode_from_url($config));
        $file_path = './private/uploads/csv_imports/' . $csv_data['f'];
        if (is_readable($file_path)) {
            $f = fopen($file_path, 'r');
            $csv_array = array();
            $csv_cols = 0;
            while (($line_data = fgetcsv($f, 0, $csv_data['d'], $csv_data['c'], $csv_data['e'])) !== FALSE) {
                $csv_array[] = $line_data;
                $csv_cols = max(array($csv_cols, count($line_data)));
            }
            fclose($f);
            $this->inject_courses();
            $this->parser->add_css_file('admin_students.css');
            $this->parser->add_js_file('admin_students/csv_content_list.js');
            $this->parser->parse('backend/students/show_csv_content.tpl', array('csv_array' => $csv_array, 'csv_cols' => $csv_cols));
        } else {
            $this->messages->add_message('lang:admin_students_csv_import_error_file_not_exist_or_is_unreadable', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_students/csv_import'));
        }
    }
    
    public function csv_import_screen($config) {
        $this->_select_teacher_menu_pagetag('students_manager');
        $this->parser->assign('url_config', $config);
        $csv_data = unserialize(decode_from_url($config));
        $cols = $this->input->post('col');
        $rows = $this->input->post('row');
        $file_path = './private/uploads/csv_imports/' . $csv_data['f'];
        if (is_readable($file_path)) {
            if ($this->test_csv_import_cols($cols)) {
                $this->parser->assign('password_type', $this->input->post('password_type'));
                $this->parser->assign('send_mail', (bool)$this->input->post('send_mail'));
                $this->parser->assign('assign_to_course', $this->input->post('assign_to_course'));
                $this->parser->add_css_file('admin_students.css');
                $this->parser->add_js_file('admin_students/csv_import.js');
                $this->parser->assign('csv_content', $this->get_csv_content($csv_data, $cols, $rows));
                $this->parser->parse('backend/students/csv_import_screen.tpl');
            } else {
                $this->parser->assign('error_message', 'lang:admin_students_csv_import_error_invalid_cols_config');
                $this->show_csv_content($config);
            }
        } else {
            $this->messages->add_message('lang:admin_students_csv_import_error_file_not_exist_or_is_unreadable', Messages::MESSAGE_TYPE_ERROR);
            redirect(create_internal_url('admin_students/csv_import'));
        }
    }
    
    public function import_single_line() {
        $this->output->set_content_type('application/json');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $fullname = $this->input->post('fullname');
        $email = $this->input->post('email');
        $options = $this->input->post('options');
        $this->parser->assign('firstname', $firstname);
        $this->parser->assign('lastname', $lastname);
        $this->parser->assign('fullname', $fullname);
        $this->parser->assign('email', $email);
        if (((trim($firstname) != '' && trim($lastname) != '') || trim($fullname) != '') && trim($email) != '') {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $student = new Student();
            $student->where('email', trim($email));
            $student->get();
            if ($student->exists()) {
                $this->db->trans_rollback();
                $this->parser->assign('error_message', 'lang:admin_students_csv_import_error_message_student_exists');
            } else {
                $this->load->library('form_validation');
                if ($this->form_validation->valid_email(trim($email))) {
                    $student->email = trim($email);
                    $student->fullname = (trim($fullname) != '') ? trim($fullname) : trim($firstname) . ' ' . trim($lastname);
                    $password = '';
                    if ($options['password_type'] == 'default') {
                        $password = $this->config->item('student_import_default_password');
                    } elseif ($options['password_type'] == 'random') {
                        $password = md5(base64_encode(rand(0, 99999999999) . time() . $student->fullname . $student->email) . $this->config->item('encryption_key'));
                        $password = substr($password, 0, rand(6,20));
                    }
                    $student->password = $password != '' ? sha1($password) : '';
                    $student->language = $this->config->item('language');
                    if ($student->save()) {
                        $this->parser->assign('password', $password);
                        $this->db->trans_commit();
                        $this->parser->assign('success_message', 'lang:admin_students_csv_import_successfully_imported');
                        
                        if ((bool)$options['send_mail']) {
                            if ($password == '') {
                                $this->_transaction_isolation();
                                $this->db->trans_begin();
                                $student->generate_random_password_token();
                                $this->db->trans_commit();
                            }

                            $this->_init_language_for_student($student);
                            $this->load->library('email');
                            $this->email->from_system();
                            $this->email->to($student->email);
                            $this->email->subject($this->lang->line('admin_students_csv_import_email_subject'));
                            $this->email->build_message_body('file:emails/backend/students/csv_import_email.tpl', array(
                                'student' => $student,
                                'password' => $password,
                            ));
                            $sent = $this->email->send();
                            $this->_init_language_for_teacher();
                            if ($sent) {
                                $this->parser->assign('email_success_message', 'lang:admin_students_csv_import_email_sent_successfully');
                            } else {
                                $this->parser->assign('email_error_message', 'lang:admin_students_csv_import_email_sent_failed');
                            }
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->parser->assign('error_message', 'lang:admin_students_csv_import_error_message_student_save_error');
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->parser->assign('error_message', 'lang:admin_students_csv_import_error_message_student_email_invalid');
                }
            }
            if ($student->exists()) {
                $this->parser->assign('student_id', $student->id);
                if (intval($options['assign_to_course']) > 0) {
                    $this->_transaction_isolation();
                    $this->db->trans_begin();
                    $course = new Course();
                    $course->get_by_id(intval($options['assign_to_course']));
                    if ($course->exists()) {
                        $participant = new Participant();
                        $participant->where_related('student', 'id', $student->id);
                        $participant->where_related('course', 'id', $course->id);
                        $participant->get();
                        if (!$participant->exists()) {
                            $participant->allowed = 0;
                            if ($participant->save(array('student' => $student, 'course' => $course))) {
                                $this->db->trans_commit();
                                $this->parser->assign('course_assignment_success_message', 'lang:admin_students_csv_import_successfully_added_course_participation');
                            } else {
                                $this->db->trans_rollback();
                                $this->parser->assign('course_assignment_error_message', 'lang:admin_students_csv_import_error_message_participation_save_failed');
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->parser->assign('course_assignment_error_message', 'lang:admin_students_csv_import_error_message_already_in_course');
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->parser->assign('course_assignment_error_message', 'lang:admin_students_csv_import_error_message_course_not_found');
                    }
                }
            }
        } else {
            $this->parser->assign('error_message', 'lang:admin_students_csv_import_error_message_nothing_to_import');
        }
        $html = $this->parser->parse('backend/students/import_single_line.tpl', array(), TRUE);
        $this->output->set_output(json_encode($html));
    }
    
    public function delete_csv_file($config) {
        $csv_data = unserialize(decode_from_url($config));
        $file_path = './private/uploads/csv_imports/' . $csv_data['f'];
        @unlink($file_path);
    }
    
    private function test_csv_import_cols($cols) {
        $is_firstname = 0;
        $is_lastname = 0;
        $is_fullname = 0;
        $is_email = 0;
        
        if (is_array($cols) && count($cols) > 0) { foreach ($cols as $col) {
            if ($col == 'is_firstname') { $is_firstname++; }
            if ($col == 'is_lastname') { $is_lastname++; }
            if ($col == 'is_fullname') { $is_fullname++; }
            if ($col == 'is_email') { $is_email++; }
        }}
        
        return (($is_firstname == 1 && $is_lastname == 1) || $is_fullname == 1) && $is_email == 1;
    }
    
    private function convert_csv_import_cols_config($cols) {
        $config = array(
            'firstname' => NULL,
            'lastname' => NULL,
            'fullname' => NULL,
            'email' => NULL,
        );
        if ($this->test_csv_import_cols($cols)) {
            if (is_array($cols) && count($cols) > 0) { foreach ($cols as $key => $col) {
                if ($col == 'is_firstname') { $config['firstname'] = $key-1; }
                if ($col == 'is_lastname') { $config['lastname'] = $key-1; }
                if ($col == 'is_fullname') { $config['fullname'] = $key-1; }
                if ($col == 'is_email') { $config['email'] = $key-1; }
            }}
        }
        return $config;
    }
    
    private function get_csv_content($config, $cols, $rows) {
        $output = array();
        $file_path = './private/uploads/csv_imports/' . $config['f'];
        if (is_readable($file_path)) {
            $cols_config = $this->convert_csv_import_cols_config($cols);
            if (is_array($rows) && count($rows) > 0 && array_sum($rows) > 0) {
                $f = fopen($file_path, 'r');
                $line = 0;
                while (($line_data = fgetcsv($f, 0, $config['d'], $config['c'], $config['e'])) !== FALSE) {
                    if (isset($rows[$line++])) {
                        $output[] = array(
                            'firstname' => isset($line_data[$cols_config['firstname']]) ? $line_data[$cols_config['firstname']] : '',
                            'lastname' => isset($line_data[$cols_config['lastname']]) ? $line_data[$cols_config['lastname']] : '',
                            'fullname' => isset($line_data[$cols_config['fullname']]) ? $line_data[$cols_config['fullname']] : '',
                            'email' => isset($line_data[$cols_config['email']]) ? $line_data[$cols_config['email']] : '',
                        );
                    }
                }
                fclose($f);
            } 
        }
        return $output;
    }
    
    private function inject_courses() {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $periods->get_iterated();
        $data = array();
        if ($periods->exists()) { foreach ($periods as $period) {
            $period->course->order_by_with_constant('name', 'asc')->get_iterated();
            if ($period->course->exists() > 0) { foreach ($period->course as $course) {
                $data[$period->name][$course->id] = $course->name;
            }}
        }}
        $this->parser->assign('courses', $data);
    }

    private function store_filter($filter) {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
        }
    }
    
    private function inject_stored_filter() {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
        $this->parser->assign('filter', $filter);
    }
}