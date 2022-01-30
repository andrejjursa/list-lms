<?php

/**
 * Controller for parallel moss implementation.
 */
class parallel_moss extends LIST_Controller
{
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
    
    public function index()
    {
        $this->_select_teacher_menu_pagetag('parallel_moss');
        
        $this->parser->assign('moss_enabled', $this->is_moss_user_id_set());
        
        $this->parser->parse('backend/parallel_moss/index.tpl');
    }
    
    public function get_comparisons()
    {
        $comparisons = new Parallel_moss_comparison();
        $comparisons->include_related('teacher', 'fullname');
        $comparisons->get_paged_iterated(1, 10);
        
        $output = [
            'data' => [],
        ];
        
        /** @var Parallel_moss_comparison $comparison */
        foreach ($comparisons as $comparison) {
            $output['data'][] = [
                'id'                => $comparison->id,
                'created'           => $comparison->created,
                'updated'           => $comparison->updated,
                'teacher'           => isset($comparison->teacher_id)
                    ? [
                        'id'        => (int)$comparison->teacher_id,
                        'full_name' => $comparison->teacher_fullname,
                    ]
                    : null,
                'status'            => $comparison->status,
                'processing_start'  => $comparison->processing_start,
                'processing_finish' => $comparison->processing_finish,
                'result_link'       => $comparison->result_link,
                'configuration'     => $comparison->configuration,
            ];
        }
        $output['pagination'] = (array)$comparisons->paged;
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    protected function is_moss_user_id_set(): bool
    {
        $this->load->config('moss');
        return preg_match(
                '/^\d+$/',
                $this->config->item('moss_user_id')
            ) && (int)$this->config->item('moss_user_id') > 0;
    }
}