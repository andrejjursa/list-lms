<?php

/**
 * Students controller for backend.
 *
 * @package LIST_BE_Controllers
 * @author  Andrej Jursa
 */
class Students extends LIST_Controller
{
    public const STORED_FILTER_SESSION_NAME = 'admin_students_filter_data';
    
    public function __construct()
    {
        parent::__construct();
        $this->_init_language_for_teacher();
        $this->_load_teacher_langfile();
        $this->_initialize_teacher_menu();
        $this->_initialize_open_task_set();
        $this->_init_teacher_quick_prefered_course_menu();
        $this->usermanager->teacher_login_protected_redirect();
    }
    
    public function index(): void
    {
        $this->_select_teacher_menu_pagetag('students_manager');
        $this->parser->add_js_file('admin_students/list.js');
        $this->parser->add_css_file('admin_students.css');
        $this->inject_stored_filter();
        $this->inject_courses();
        $this->parser->parse('backend/students/index.tpl');
    }
    
    public function new_student_form(): void
    {
        $this->parser->parse('backend/students/new_student_form.tpl');
    }
    
    public function table_content(): void
    {
        $filter = $this->input->post('filter');
        $this->store_filter($filter);
        $students = new Student();
        if (isset($filter['fullname']) && trim($filter['fullname']) !== '') {
            $students->like('fullname', trim($filter['fullname']));
        }
        if (isset($filter['email']) && trim($filter['email']) !== '') {
            $students->like('email', trim($filter['email']));
        }
        if (isset($filter['course']) && $filter['course'] !== '') {
            $students->where_related('participant/course', 'id', (int)$filter['course']);
            $students->where_related('participant', 'allowed', 1);
        }
        $order_by_direction = $filter['order_by_direction'] === 'desc' ? 'desc' : 'asc';
        if ($filter['order_by_field'] === 'fullname') {
            $students->order_by_as_fullname('fullname', $order_by_direction);
        } else if ($filter['order_by_field'] === 'email') {
            $students->order_by('email', $order_by_direction);
        }
        $students->get_paged_iterated(
            isset($filter['page']) ? (int)$filter['page'] : 1,
            isset($filter['rows_per_page']) ? (int)$filter['rows_per_page'] : 25
        );
        $this->parser->parse('backend/students/table_content.tpl', ['students' => $students]);
    }
    
    public function create(): void
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'student[fullname]',
            'lang:admin_students_form_field_fullname',
            'required|max_length[255]'
        );
        $this->form_validation->set_rules(
            'student[email]',
            'lang:admin_students_form_field_email',
            'required|valid_email|is_unique[students.email]'
        );
        $this->form_validation->set_rules(
            'student[password]',
            'lang:admin_students_form_field_password',
            'required|min_length[6]|max_length[20]'
        );
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $student_data = $this->input->post('student');
            $student = new Student();
            $student->from_array($student_data, ['fullname', 'email']);
            $student->password = sha1($student_data['password']);
            $student->language = $this->config->item('language');
            if ($student->save() && $this->db->trans_status()) {
                $this->db->trans_commit();
                $this->messages->add_message(
                    'lang:admin_students_account_save_successful',
                    Messages::MESSAGE_TYPE_SUCCESS
                );
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_students_account_save_fail',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(create_internal_url('admin_students/new_student_form'));
        } else {
            $this->new_student_form();
        }
    }
    
    public function edit(): void
    {
        $this->_initialize_teacher_menu();
        $this->usermanager->teacher_login_protected_redirect();
        $this->_select_teacher_menu_pagetag('students_manager');
        $url = $this->uri->ruri_to_assoc(3);
        $student_id = isset($url['student_id']) ? (int)$url['student_id'] : 0;
        $student = new Student();
        $student->get_by_id($student_id);
        $this->parser->add_js_file('admin_students/edit.js');
        $this->parser->parse('backend/students/edit.tpl', ['student' => $student]);
    }
    
    public function update(): void
    {
        $this->usermanager->teacher_login_protected_redirect();
        
        $this->load->library('form_validation');
        
        $student_id = (int)$this->input->post('student_id');
        
        $this->form_validation->set_rules(
            'student[fullname]',
            'lang:admin_students_form_field_fullname',
            'required|max_length[255]'
        );
        $this->form_validation->set_rules(
            'student[email]',
            'lang:admin_students_form_field_email',
            'required|valid_email|callback__email_available[' . $student_id . ']'
        );
        $this->form_validation->set_rules(
            'student[password]',
            'lang:admin_students_form_field_password',
            'min_length_optional[6]|max_length_optional[20]'
        );
        $this->form_validation->set_message(
            '_email_available',
            $this->lang->line('admin_students_form_error_email_not_available')
        );
        
        $this->_transaction_isolation();
        $this->db->trans_begin();
        if ($this->form_validation->run()) {
            $student = new Student();
            $student->get_by_id($student_id);
            if ($student->exists()) {
                $student_data = $this->input->post('student');
                $student->from_array($student_data, ['fullname', 'email']);
                if (isset($student_data['password']) && !empty($student_data['password'])) {
                    $student->password = sha1($student_data['password']);
                }
                if ($student->save() && $this->db->trans_status()) {
                    $this->db->trans_commit();
                    $this->messages->add_message(
                        'lang:admin_students_account_save_successful',
                        Messages::MESSAGE_TYPE_SUCCESS
                    );
                } else {
                    $this->db->trans_rollback();
                    $this->messages->add_message(
                        'lang:admin_students_account_save_fail',
                        Messages::MESSAGE_TYPE_ERROR
                    );
                }
            } else {
                $this->db->trans_rollback();
                $this->messages->add_message(
                    'lang:admin_students_student_not_found',
                    Messages::MESSAGE_TYPE_ERROR
                );
            }
            redirect(create_internal_url('admin_students'));
        } else {
            $this->edit();
        }
    }
    
    public function _email_available($str, $student_id): bool
    {
        $student = new Student();
        $student->where('email', $str)->where('id !=', $student_id);
        $count = $student->count();
        return $count === 0;
    }
    
    public function delete(): void
    {
        $this->output->set_content_type('application/json');
        $this->usermanager->teacher_login_protected_redirect();
        $url = $this->uri->ruri_to_assoc(3);
        $student_id = isset($url['student_id']) ? (int)$url['student_id'] : 0;
        if ($student_id !== 0) {
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $student = new Student();
            $student->get_by_id($student_id);
            $student->delete();
            if ($this->db->trans_status()) {
                $this->db->trans_commit();
                $this->output->set_output(json_encode(true));
            } else {
                $this->db->trans_rollback();
                $this->output->set_output(json_encode(false));
            }
        } else {
            $this->output->set_output(json_encode(false));
        }
    }
    
    public function csv_import(): void
    {
        $this->_select_teacher_menu_pagetag('students_manager');
        $this->parser->add_js_file('admin_students/csv_import_form.js');
        $this->parser->parse('backend/students/csv_import.tpl');
    }
    
    public function upload_csv_file(): void
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules(
            'csv_data[delimiter]',
            'lang:admin_students_csv_import_form_field_delimiter',
            'required|exact_length[1]'
        );
        $this->form_validation->set_rules(
            'csv_data[enclosure]',
            'lang:admin_students_csv_import_form_field_enclosure',
            'required|exact_length[1]'
        );
        $this->form_validation->set_rules(
            'csv_data[escape]',
            'lang:admin_students_csv_import_form_field_escape',
            'required|exact_length[1]'
        );
        if ($this->form_validation->run()) {
            $config = [
                'upload_path'   => './private/uploads/csv_imports',
                'file_name'     => 'students_import_' . date('U') . '_' . rand(10000, 99999) . '.csv',
                'allowed_types' => 'csv',
                'overwrite'     => false,
            ];
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('csv_file')) {
                $upload_data = $this->upload->data();
                $post_data = $this->input->post('csv_data');
                $data = [
                    'f' => $upload_data['file_name'],
                    'd' => $post_data['delimiter'],
                    'c' => $post_data['enclosure'],
                    'e' => $post_data['escape'],
                ];
                redirect(
                    create_internal_url('admin_students/show_csv_content/' . encode_for_url(serialize($data)))
                );
            } else {
                $this->parser->assign('error_message', $this->upload->display_errors('', ''));
                $this->csv_import();
            }
        } else {
            $this->csv_import();
        }
    }
    
    public function show_csv_content($config): void
    {
        $this->_select_teacher_menu_pagetag('students_manager');
        $this->parser->assign('url_config', $config);
        $csv_data = unserialize(decode_from_url($config));
        $file_path = './private/uploads/csv_imports/' . $csv_data['f'];
        if (is_readable($file_path)) {
            $f = fopen($file_path, 'r');
            $csv_array = [];
            $csv_cols = 0;
            while (($line_data = fgetcsv($f, 0, $csv_data['d'], $csv_data['c'], $csv_data['e'])) !== false) {
                $csv_array[] = $line_data;
                $csv_cols = max([$csv_cols, count($line_data)]);
            }
            fclose($f);
            $this->inject_courses();
            $this->parser->add_css_file('admin_students.css');
            $this->parser->add_js_file('admin_students/csv_content_list.js');
            $this->parser->parse(
                'backend/students/show_csv_content.tpl',
                [
                    'csv_array' => $csv_array,
                    'csv_cols'  => $csv_cols,
                ]
            );
        } else {
            $this->messages->add_message(
                'lang:admin_students_csv_import_error_file_not_exist_or_is_unreadable',
                Messages::MESSAGE_TYPE_ERROR
            );
            redirect(create_internal_url('admin_students/csv_import'));
        }
    }
    
    public function csv_import_screen($config): void
    {
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
                $this->parser->assign(
                    'error_message',
                    'lang:admin_students_csv_import_error_invalid_cols_config'
                );
                $this->show_csv_content($config);
            }
        } else {
            $this->messages->add_message(
                'lang:admin_students_csv_import_error_file_not_exist_or_is_unreadable',
                Messages::MESSAGE_TYPE_ERROR
            );
            redirect(create_internal_url('admin_students/csv_import'));
        }
    }
    
    public function import_single_line(): void
    {
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
        if (((trim($firstname) !== '' && trim($lastname) !== '') || trim($fullname) !== '') && trim($email) !== '') {
            $student_fullname = (trim($fullname) !== '') ? trim($fullname) : trim($firstname) . ' ' . trim($lastname);
            $this->_transaction_isolation();
            $this->db->trans_begin();
            $student = new Student();
            $student->where('email', trim($email));
            $student->get();
            if ($student->exists()) {
                if ($student->fullname !== $student_fullname) {
                    $student->fullname = $student_fullname;
                    $student->save();
                    $this->db->trans_commit();
                } else {
                    $this->db->trans_rollback();
                }
                $this->parser->assign(
                    'error_message',
                    'lang:admin_students_csv_import_error_message_student_exists'
                );
            } else {
                $this->load->library('form_validation');
                if ($this->form_validation->valid_email(trim($email))) {
                    $student->email = trim($email);
                    $student->fullname = $student_fullname;
                    $password = '';
                    if ($options['password_type'] === 'default') {
                        $password = $this->config->item('student_import_default_password');
                    } else if ($options['password_type'] === 'random') {
                        $password = md5(
                            base64_encode(rand(0, 99999999999) . time() . $student->fullname
                                . $student->email) . $this->config->item('encryption_key')
                        );
                        $password = substr($password, 0, rand(6, 20));
                    }
                    $student->password = $password !== '' ? sha1($password) : '';
                    $student->language = $this->config->item('language');
                    if ($student->save()) {
                        $this->parser->assign('password', $password);
                        $this->db->trans_commit();
                        $this->parser->assign(
                            'success_message',
                            'lang:admin_students_csv_import_successfully_imported'
                        );
                        
                        if ((bool)$options['send_mail']) {
                            if ($password === '') {
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
                            $this->email->build_message_body(
                                'file:emails/backend/students/csv_import_email.tpl',
                                [
                                    'student'  => $student,
                                    'password' => $password,
                                ]
                            );
                            $sent = $this->email->send();
                            $this->_init_language_for_teacher();
                            if ($sent) {
                                $this->parser->assign(
                                    'email_success_message',
                                    'lang:admin_students_csv_import_email_sent_successfully'
                                );
                            } else {
                                $this->parser->assign(
                                    'email_error_message',
                                    'lang:admin_students_csv_import_email_sent_failed'
                                );
                            }
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->parser->assign(
                            'error_message',
                            'lang:admin_students_csv_import_error_message_student_save_error'
                        );
                    }
                } else {
                    $this->db->trans_rollback();
                    $this->parser->assign(
                        'error_message',
                        'lang:admin_students_csv_import_error_message_student_email_invalid'
                    );
                }
            }
            if ($student->exists()) {
                $this->parser->assign('student_id', $student->id);
                if ((int)$options['assign_to_course'] > 0) {
                    $this->_transaction_isolation();
                    $this->db->trans_begin();
                    $course = new Course();
                    $course->get_by_id((int)$options['assign_to_course']);
                    if ($course->exists()) {
                        $participant = new Participant();
                        $participant->where_related('student', 'id', $student->id);
                        $participant->where_related('course', 'id', $course->id);
                        $participant->get();
                        if (!$participant->exists()) {
                            $participant->allowed = 0;
                            if ($participant->save(['student' => $student, 'course' => $course])) {
                                $this->db->trans_commit();
                                $this->parser->assign(
                                    'course_assignment_success_message',
                                    'lang:admin_students_csv_import_successfully_added_course_participation'
                                );
                                $this->db->trans_begin();
                                $course = new Course();
                                $course->get_by_id((int)$options['assign_to_course']);
                                $participant->allowed = 1;
                                $participant->save();
                                $participants = new Participant();
                                $participants->where_related($course);
                                $participants->where('allowed', 1);
                                $participants_count = $participants->count();
                                if ($participants_count <= $course->capacity) {
                                    $this->db->trans_commit();
                                    $this->parser->assign(
                                        'course_assignment_approwal_success_message',
                                        'lang:admin_students_csv_import_successfully_added_course_participation_approwal'
                                    );
                                } else {
                                    $this->db->trans_rollback();
                                    $this->parser->assign(
                                        'course_assignment_approwal_error_message',
                                        'lang:admin_students_csv_import_error_message_added_course_participation_approwal'
                                    );
                                }
                            } else {
                                $this->db->trans_rollback();
                                $this->parser->assign(
                                    'course_assignment_error_message',
                                    'lang:admin_students_csv_import_error_message_participation_save_failed'
                                );
                            }
                        } else {
                            $this->db->trans_rollback();
                            $this->parser->assign(
                                'course_assignment_error_message',
                                'lang:admin_students_csv_import_error_message_already_in_course'
                            );
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->parser->assign(
                            'course_assignment_error_message',
                            'lang:admin_students_csv_import_error_message_course_not_found'
                        );
                    }
                }
            }
        } else {
            $this->parser->assign(
                'error_message',
                'lang:admin_students_csv_import_error_message_nothing_to_import'
            );
        }
        $html = $this->parser->parse('backend/students/import_single_line.tpl', [], true);
        $this->output->set_output(json_encode($html));
    }
    
    public function delete_csv_file($config): void
    {
        $csv_data = unserialize(decode_from_url($config));
        $file_path = './private/uploads/csv_imports/' . $csv_data['f'];
        @unlink($file_path);
    }
    
    public function log_in_as_student(): void
    {
        $uri_data = $this->uri->ruri_to_assoc(3);
        if (isset($uri_data['student_id'])) {
            $student = new Student();
            $student->get_by_id((int)$uri_data['student_id']);
            if ($this->usermanager->force_student_login($student)) {
                $this->messages->add_message(
                    'lang:students_force_loged_in',
                    Messages::MESSAGE_TYPE_SUCCESS
                );
                redirect(create_internal_url('/'));
            }
        }
        $this->messages->add_message(
            'lang:admin_students_failed_to_force_login',
            Messages::MESSAGE_TYPE_ERROR
        );
        redirect(create_internal_url('admin_students'));
    }
    
    private function test_csv_import_cols($cols): bool
    {
        $is_firstname = 0;
        $is_lastname = 0;
        $is_fullname = 0;
        $is_email = 0;
        
        if (is_array($cols) && count($cols) > 0) {
            foreach ($cols as $col) {
                if ($col === 'is_firstname') {
                    $is_firstname++;
                }
                if ($col === 'is_lastname') {
                    $is_lastname++;
                }
                if ($col === 'is_fullname') {
                    $is_fullname++;
                }
                if ($col === 'is_email') {
                    $is_email++;
                }
            }
        }
        
        return (($is_firstname === 1 && $is_lastname === 1) || $is_fullname === 1) && $is_email === 1;
    }
    
    private function convert_csv_import_cols_config($cols): array
    {
        $config = [
            'firstname' => null,
            'lastname'  => null,
            'fullname'  => null,
            'email'     => null,
        ];
        if ($this->test_csv_import_cols($cols)) {
            if (is_array($cols) && count($cols) > 0) {
                foreach ($cols as $key => $col) {
                    if ($col === 'is_firstname') {
                        $config['firstname'] = $key - 1;
                    }
                    if ($col === 'is_lastname') {
                        $config['lastname'] = $key - 1;
                    }
                    if ($col === 'is_fullname') {
                        $config['fullname'] = $key - 1;
                    }
                    if ($col === 'is_email') {
                        $config['email'] = $key - 1;
                    }
                }
            }
        }
        return $config;
    }
    
    private function get_csv_content($config, $cols, $rows): array
    {
        $output = [];
        $file_path = './private/uploads/csv_imports/' . $config['f'];
        if (is_readable($file_path)) {
            $cols_config = $this->convert_csv_import_cols_config($cols);
            if (is_array($rows) && count($rows) > 0 && array_sum($rows) > 0) {
                $f = fopen($file_path, 'r');
                $line = 0;
                while (($line_data = fgetcsv($f, 0, $config['d'], $config['c'], $config['e'])) !== false) {
                    if (isset($rows[$line++])) {
                        $output[] = [
                            'firstname' => $line_data[$cols_config['firstname']] ?? '',
                            'lastname'  => $line_data[$cols_config['lastname']] ?? '',
                            'fullname'  => $line_data[$cols_config['fullname']] ?? '',
                            'email'     => $line_data[$cols_config['email']] ?? '',
                        ];
                    }
                }
                fclose($f);
            }
        }
        return $output;
    }
    
    private function inject_courses(): void
    {
        $periods = new Period();
        $periods->order_by('sorting', 'asc');
        $periods->get_iterated();
        $data = [];
        if ($periods->exists()) {
            foreach ($periods as $period) {
                $period->course->order_by_with_constant('name', 'asc')->get_iterated();
                if ($period->course->exists() > 0) {
                    foreach ($period->course as $course) {
                        $data[$period->name][$course->id] = $course->name;
                    }
                }
            }
        }
        $this->parser->assign('courses', $data);
    }
    
    private function store_filter($filter): void
    {
        if (is_array($filter)) {
            $this->load->library('filter');
            $old_filter = $this->filter->restore_filter(self::STORED_FILTER_SESSION_NAME);
            $new_filter = is_array($old_filter) ? array_merge($old_filter, $filter) : $filter;
            $this->filter->store_filter(self::STORED_FILTER_SESSION_NAME, $new_filter);
            $this->filter->set_filter_course_name_field(
                self::STORED_FILTER_SESSION_NAME,
                'course'
            );
        }
    }
    
    private function inject_stored_filter(): void
    {
        $this->load->library('filter');
        $filter = $this->filter->restore_filter(
            self::STORED_FILTER_SESSION_NAME,
            $this->usermanager->get_teacher_id(),
            'course'
        );
        $this->parser->assign('filter', $filter);
    }
}