<?php

include_once APPPATH . 'controllers/admin/course_content.php';

/**
 * Course content controller for frontend.
 *
 * @package LIST_FE_Controllers
 * @author  Andrej Jursa
 */
class Content extends LIST_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        
        if ($this->router->method !== 'download_file'
            && $this->router->method !== 'show_content' && $this->router->method !== 'page'
        ) {
            $this->usermanager->student_login_protected_redirect();
        }
        $this->_init_language_for_student();
        $this->_load_student_langfile();
    }
    
    public function index(): void
    {
        $this->_initialize_student_menu();
        $this->_select_student_menu_pagetag('course_content');
        
        $this->_add_prettify();
        $this->_add_mathjax();
        
        $this->parser->add_js_file('content/content.js');
        $this->parser->add_css_file('frontend_content.css');
        
        $student = new Student();
        $student->get_by_id($this->usermanager->get_student_id());
        
        $course_id = $student->active_course_id ?? 'none';
        $cache_id = 'student_' . $student->id . '|course_' . $course_id . '|lang_' . $this->lang->get_current_idiom();
        if (!$this->_is_cache_enabled()
            || !$this->parser->isCached($this->parser->find_view('frontend/content/index.tpl'), $cache_id)
        ) {
            $this->_transaction_isolation();
            $this->db->trans_start();
            
            $course = new Course();
            $course->include_related('period', 'name');
            $course->get_by_id((int)$course_id);
            smarty_inject_days();
            $this->parser->assign(['course' => $course]);
            
            $student = new Student();
            $student->include_related('participant');
            $student->where_related('participant/course', 'id', $course->id);
            $student->where_related('participant', 'allowed', true);
            $student->get_by_id($this->usermanager->get_student_id());
            
            $public_only = true;
            
            if ($student->exists()) {
                $public_only = false;
            }
            
            $content = $this->get_content($course, $public_only);
            $content_groups = $this->get_content_groups($course);
            $top_level_order = $this->get_top_level_sorting_order($course, $public_only);
            $cache_lifetime = $this->get_cache_lifetime($course, $public_only);
            
            $this->smarty->setCacheLifetime($cache_lifetime + 1);
            $this->parser->setCacheLifetimeForTemplateObject(
                'frontend/content/index.tpl',
                $cache_lifetime + 1
            );
            
            $this->parser->assign(['content' => $content]);
            $this->parser->assign(['content_groups' => $content_groups]);
            $this->parser->assign(['top_level_order' => $top_level_order]);
            
            $this->db->trans_complete();
        }
        $this->parser->parse(
            'frontend/content/index.tpl',
            [],
            false,
            $this->_is_cache_enabled() ? Smarty::CACHING_LIFETIME_SAVED : false,
            $cache_id
        );
    }
    
    public function show_content($course_id, $lang = null): void
    {
        $this->parser->add_css_file('frontend_content.css');
        if (!is_null($lang)) {
            $this->_init_specific_language($lang);
        }
        $student_id = $this->usermanager->is_student_session_valid() ? $this->usermanager->get_student_id() : 0;
        $cache_id = 'course_' . $course_id . '|student_' . $student_id . '|lang_' . $this->lang->get_current_idiom();
        if (!$this->_is_cache_enabled()
            || !$this->parser->isCached($this->parser->find_view('frontend/content/show_content.tpl'), $cache_id)
        ) {
            $course = new Course();
            $course->include_related('period');
            $course->get_by_id($course_id);
            smarty_inject_days();
            $this->parser->assign(['course' => $course]);
            
            $student = new Student();
            $student->include_related('participant');
            $student->where_related('participant/course', 'id', $course->id);
            $student->where_related('participant', 'allowed', true);
            $student->get_by_id($student_id);
            
            $public_only = true;
            
            if ($student->exists()) {
                $public_only = false;
            }
            
            $content = $this->get_content($course, $public_only);
            $content_groups = $this->get_content_groups($course);
            $top_level_order = $this->get_top_level_sorting_order($course, $public_only);
            $cache_lifetime = $this->get_cache_lifetime($course, $public_only);
            
            $this->smarty->setCacheLifetime($cache_lifetime + 1);
            $this->parser->setCacheLifetimeForTemplateObject(
                'frontend/content/index.tpl',
                $cache_lifetime + 1
            );
            
            $this->parser->assign(['content' => $content]);
            $this->parser->assign(['content_groups' => $content_groups]);
            $this->parser->assign(['top_level_order' => $top_level_order]);
        }
        $this->parser->parse(
            'frontend/content/show_content.tpl',
            [],
            false,
            $this->_is_cache_enabled() ? Smarty::CACHING_LIFETIME_SAVED : false,
            $cache_id
        );
    }
    
    public function page($course_id, $lang = null): void
    {
        $course = new Course();
        $course->include_related('period', 'name');
        $course->get_by_id((int)$course_id);
        
        if (!is_null($lang)) {
            $this->_init_specific_language($lang);
        }
        
        $student = new Student();
        if ($this->usermanager->is_student_session_valid()) {
            $student->get_by_id((int)$this->usermanager->get_student_id());
        }
        
        $this->parser->assign('course', $course);
        $this->parser->assign('lang', $this->lang->get_current_idiom());
        $this->parser->assign('student', $student);
        
        $this->inject_languages();
        
        $this->parser->parse('frontend/content/page.tpl');
    }
    
    public function download_file($path, $language, $file): void
    {
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
                    
                    $student = new Student();
                    $student->where_related('participant/course', 'id', $course_content->course_id);
                    $student->where_related('participant', 'allowed', true);
                    $student->get_by_id($this->usermanager->get_student_id());
                    
                    if (!$student->exists()) {
                        $this->file_not_found();
                        return;
                    }
                }
                
                if (!$course_content->get_is_published()) {
                    $this->file_not_found();
                    return;
                }
            }
        } else {
            $this->usermanager->teacher_login_protected_redirect();
            
        }
        $this->do_download_file($path, $language, decode_from_url($file));
    }
    
    private function do_download_file($path, $language, $file): void
    {
        $languages = $this->lang->get_list_of_languages();
        
        if (!array_key_exists($language, $languages)) {
            $language = '';
        }
        
        $filepath = realpath(Course_Content::COURSE_CONTENT_MASTER_FILE_STORAGE) . DIRECTORY_SEPARATOR . $path .
            ($language != '' ? DIRECTORY_SEPARATOR . $language : '') . DIRECTORY_SEPARATOR . $file;
        
        if (file_exists($filepath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $filepath);
            finfo_close($finfo);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename=' . basename($filepath));
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
        }
        
        $this->file_not_found();
    }
    
    private function file_not_found(): void
    {
        $code = "
        \$heading = 'File not found';
        \$message = 'Requested file was not found. It may be moved, deleted or may be hidden from public.';
        ";
        $file = file_get_contents(APPPATH . 'errors/error_404.php');
        ob_start();
        eval($code . '?>' . $file);
        $output = ob_get_clean();
        $this->output->set_status_header(404, 'Not found');
        $this->output->set_output($output);
    }
    
    private function get_content(Course $course, $public_only = false): \Course_content_model
    {
        $content = new Course_content_model();
        
        if ($course->exists()) {
            $date = date('Y-m-d H:i:s');
            
            $content->include_related('creator');
            $content->include_related('updator');
            
            $content->where_related('course', $course);
            
            if ($public_only) {
                $content->where('public', true);
            }
            $content->where('published', true);
            
            $content->group_start();
            $content->or_where('published_from', null);
            $content->or_where('published_from <=', $date);
            $content->group_end();
            
            $content->group_start();
            $content->or_where('published_to', null);
            $content->or_where('published_to >=', $date);
            $content->group_end();
            
            $content->order_by('sorting', 'asc');
            
            $content->get_iterated();
        }
        
        return $content;
    }
    
    private function get_content_groups(Course $course): \Course_content_group
    {
        $content_groups = new Course_content_group();
        
        if ($course->exists()) {
            $content_groups->where_related('course', $course);
            
            $content_groups->order_by('sorting', 'asc');
            
            $content_groups->get_iterated();
        }
        
        return $content_groups;
    }
    
    private function get_top_level_sorting_order(Course $course, $public_only = false): array
    {
        $order = [];
        
        if ($course->exists()) {
            $date = date('Y-m-d H:i:s');
            
            $query1 = new Course_content_model();
            $query1->select('id, sorting');
            $query1->select_func("", ['content'], 'type');
            $query1->where_related('course', $course);
            
            
            if ($public_only) {
                $query1->where('public', true);
            }
            $query1->where('published', true);
            
            $query1->group_start();
            $query1->or_where('published_from', null);
            $query1->or_where('published_from <=', $date);
            $query1->group_end();
            
            $query1->group_start();
            $query1->or_where('published_to', null);
            $query1->or_where('published_to >=', $date);
            $query1->group_end();
            
            $query1->where('course_content_group_id', null);
            
            $query1->order_by('sorting', 'asc');
            
            $query2 = new Course_content_group();
            $query2->select('id, sorting');
            $query2->select_func("", ['group'], 'type');
            
            $query2->where_related('course', $course);
            
            $query2->order_by('sorting', 'asc');
            
            $query1->union_iterated($query2, true, 'sorting ASC');
            
            foreach ($query1 as $row) {
                $order[] = [$row->type, $row->id];
            }
        }
        
        return $order;
    }
    
    private function get_cache_lifetime(Course $course, $public_only = false)
    {
        $lifetime = $this->smarty->cache_lifetime;
        
        if ($course->exists()) {
            $date = date('Y-m-d H:i:s');
            
            $content1 = new Course_content_model();
            $content1->where_related('course', $course);
            
            $content1->select_func('', ['@published_from'], 'time');
            
            if ($public_only) {
                $content1->where('public', true);
            }
            $content1->where('published', true);
            
            $content1->where('published_from >', $date);
            
            $content2 = new Course_content_model();
            $content2->where_related('course', $course);
            
            $content2->select_func('', ['@published_to'], 'time');
            
            if ($public_only) {
                $content2->where('public', true);
            }
            $content2->where('published', true);
            
            $content2->where('published_to <', $date);
            
            $content1->union($content2, true, 'time ASC', 1);
            
            if ($content1->exists()) {
                $nextTime = strtotime($content1->time);
                $currentTime = strtotime($date);
                $timeDiff = $nextTime - $currentTime;
                
                $lifetime = $timeDiff < $lifetime ? $timeDiff : $lifetime;
            }
        }
        
        return $lifetime;
    }
    
    private function inject_languages(): void
    {
        $languages = $this->lang->get_list_of_languages();
        $this->parser->assign('languages', $languages);
    }
}