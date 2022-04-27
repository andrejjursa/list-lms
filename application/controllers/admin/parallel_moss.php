<?php

use Application\Services\AMQP\Factory\PublisherFactory;
use Application\Services\AMQP\Messages\Moss\StartComparisonMessage;
use Application\Services\DependencyInjection\ContainerFactory;
use Application\Services\Moss\RequestFactory;
use Application\Services\Moss\Service\ConfigurationBuilder;

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
        
        $this->parser->add_css_file('admin_parallel_moss.css');
        $this->parser->add_js_file('admin_parallel_moss/parallel_moss.js');
        
        $this->parser->assign('moss_enabled', $this->is_moss_user_id_set());
        
        $this->parser->parse('backend/parallel_moss/index.tpl');
    }
    
    public function get_comparisons()
    {
        $container = ContainerFactory::getContainer();
        /** @var RequestFactory $requestFactory */
        $requestFactory = $container->get(RequestFactory::class);
        $request = $requestFactory->constructGetComparisonsRequest($this->input->get());
        
        $comparisons = new Parallel_moss_comparison();
        $comparisons->include_related('teacher', 'fullname');
        $comparisons->order_by('created', 'desc');
        $comparisons->get_paged_iterated($request->getPage(), $request->getPageSize());
        
        $output = [
            'data' => [],
        ];
        
        /** @var Parallel_moss_comparison $comparison */
        foreach ($comparisons as $comparison) {
            $output['data'][] = [
                'id'                => $comparison->id,
                'comparison_name'   => $comparison->comparison_name,
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
                'failure_message'   => $comparison->failure_message,
                'configuration'     => $comparison->configuration,
            ];
        }
        $output['pagination'] = (array)$comparisons->paged;
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function get_courses()
    {
        $courses = new Course();
        $courses->include_related('period', '*');
        $courses->order_by_related_with_constant('period', 'sorting', 'asc');
        $courses->order_by_with_constant('name', 'asc');
        $courses->get_iterated();
        
        $output = [
            'data' => [],
        ];
        
        /** @var Course $course */
        foreach ($courses as $course) {
            $output['data'][$this->lang->text($course->period_name)][] = [
                'id'   => $course->id,
                'name' => $this->lang->text($course->name),
            ];
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function get_task_sets(int $courseId)
    {
        $taskSets = new Task_set();
        $taskSets->include_related('course', 'id');
        $taskSets->where_related('course', 'id', $courseId);
        $taskSets->order_by('sorting', 'asc');
        $taskSets->get_iterated();
        
        $output = [
            'data' => [
                'task_set' => [],
                'project'  => [],
            ],
        ];
        
        /** @var Task_set $taskSet */
        foreach ($taskSets as $taskSet) {
            $output['data'][$taskSet->content_type][] = [
                'id'   => $taskSet->id,
                'name' => $this->lang->get_overlay_with_default(
                    'task_sets',
                    $taskSet->id,
                    'name',
                    $taskSet->name
                ),
            ];
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function get_solutions(int $taskSetId)
    {
        $taskSet = new Task_set();
        $taskSet->get_by_id($taskSetId);
        
        $solutions = new Solution();
        if ($taskSet->exists()) {
            $solutions->include_related('student');
            $solutions->include_related('task_set', 'id');
            $solutions->where_related($taskSet);
            $solutions->order_by_related_as_fullname('student', 'fullname', 'asc');
            $solutions->get_iterated();
        }
        
        $output = [
            'data' => [
                'solutions'  => [],
                'base_files' => [],
            ],
        ];
        
        /** @var Solution $solution */
        foreach ($solutions as $solution) {
            $studentFiles = $taskSet->get_student_files($solution->student_id);
            $versions = [];
            foreach ($studentFiles as $studentFile) {
                $versions[] = [
                    'version'     => $studentFile['version'],
                    'file'        => $studentFile['file'],
                    'file_name'   => $studentFile['file_name'],
                    'random_hash' => $studentFile['random_hash'],
                ];
            }
            $bestVersion = is_int($solution->best_version) && (int)$solution->best_version > 0
                ? (int)$solution->best_version
                : null;
            $output['data']['solutions'][] = [
                'id'           => $solution->id,
                'student'      => [
                    'id'       => $solution->student_id,
                    'fullname' => $solution->student_fullname,
                ],
                'best_version' => $bestVersion,
                'versions'     => $versions,
            ];
        }
        
        $tasks = new Task();
        if ($taskSet->exists()) {
            $tasks->where_related($taskSet);
            $tasks->get_iterated();
        }
        
        /** @var Task $task */
        foreach ($tasks as $task) {
            $output['data']['base_files'][] = [
                'task_id'   => $task->id,
                'task_name' => $this->lang->get_overlay_with_default(
                    'tasks',
                    $task->id,
                    'name',
                    $task->name
                ),
                'files'     => $this->construct_base_files_for_task($task->id),
            ];
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function new_comparison()
    {
        $this->_select_teacher_menu_pagetag('parallel_moss');
        $this->parser->add_css_file('admin_parallel_moss.css');
        $this->parser->add_js_file('admin_parallel_moss/new_comparison.js');
        $this->parser->parse('backend/parallel_moss/new_comparison.tpl');
    }
    
    public function create_comparison(): void
    {
        $container = ContainerFactory::getContainer();
        
        /** @var ConfigurationBuilder $configurationBuilder */
        $configurationBuilder = $container->get(ConfigurationBuilder::class);
        
        $mossConfig = $configurationBuilder->fromPostData($this->input->post());
        
        $comparisonName = $this->input->post('comparison_name');
        if (!is_string($comparisonName) || trim($comparisonName) === '') {
            $comparisonName = null;
        } else {
            $comparisonName = mb_substr(trim($comparisonName), 0, 255);
        }
        
        $mossTable = new Parallel_moss_comparison();
        $mossTable->status = Parallel_moss_comparison::STATUS_QUEUED;
        $mossTable->configuration = $mossConfig->toArray();
        $mossTable->teacher_id = $this->usermanager->get_teacher_id();
        $mossTable->comparison_name = $comparisonName;
        $mossTable->save();
        
        /** @var PublisherFactory $publisherFactory */
        $publisherFactory = $container->get(PublisherFactory::class);
        
        $publisher = $publisherFactory->getComparisonQueuePublisher();
        
        $message = new StartComparisonMessage();
        $message->setParallelMossComparisonID($mossTable->id);
        
        $publisher->publishMessage($message);
        
        redirect(create_internal_url('admin_parallel_moss/index'));
    }
    
    public function requeue_comparison(int $id): void
    {
        $container = ContainerFactory::getContainer();
        
        $mossTable = new Parallel_moss_comparison();
        $mossTable->get_by_id($id);
        
        $output = new stdClass();
        
        if (!$mossTable->exists()) {
            $output->status = 'notFound';
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($output));
            return;
        }
        
        if (in_array($mossTable->status, [Parallel_moss_comparison::STATUS_FAILED, Parallel_moss_comparison::STATUS_FINISHED], true)) {
            $output->status = 'invalidStatus';
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($output));
            return;
        }
        
        /** @var PublisherFactory $publisherFactory */
        $publisherFactory = $container->get(PublisherFactory::class);
        
        $publisher = $publisherFactory->getComparisonQueuePublisher();
        
        $message = new StartComparisonMessage();
        $message->setParallelMossComparisonID($mossTable->id);
        
        for ($i = 0; $i < 1000; $i++) {
            $publisher->publishMessage($message);
        }
        
        $output->status = 'queued';
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($output));
    }
    
    public function get_settings()
    {
        $this->config->load('moss');
        
        $output = [
            'data' => [
                'languages' => $this->config->item('moss_langs_for_list'),
            ],
        ];
        
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
    
    private function construct_base_files_for_task($task_id): array
    {
        $output = [];
        
        $base_path = 'private/uploads/task_files/task_' . (int)$task_id . '/';
        
        $this->load->config('moss');
        $ext_lists = $this->config->item('moss_langs_file_extensions');
        $extensions = [];
        if (is_array($ext_lists) && count($ext_lists)) {
            foreach ($ext_lists as $ext_list) {
                if (is_array($ext_list) && count($ext_list)) {
                    foreach ($ext_list as $ext) {
                        $extensions[] = strtolower($ext);
                    }
                }
            }
        }
        
        $this->recursive_build_task_base_files($base_path, $base_path, $extensions, $output);
        
        return $output;
    }
    
    private function recursive_build_task_base_files($path, $base_path, $extensions, &$output): void
    {
        $base_path_length = strlen($base_path);
        if (file_exists($path)) {
            if (is_dir($path)) {
                $dir_content = scandir($path);
                foreach ($dir_content as $dir_or_file) {
                    if ($dir_or_file !== '.' && $dir_or_file !== '..') {
                        $new_path = rtrim($path, '\\/') . DIRECTORY_SEPARATOR . $dir_or_file;
                        $this->recursive_build_task_base_files($new_path
                            . (is_dir($new_path) ? DIRECTORY_SEPARATOR : ''), $base_path, $extensions, $output);
                    }
                }
            } else {
                $path_info = pathinfo($path);
                if (strtolower($path_info['extension']) === 'zip') {
                    $zip_archive = new ZipArchive();
                    if ($zip_archive->open($path)) {
                        for ($index = 0; $index < $zip_archive->numFiles; $index++) {
                            $file_name = $zip_archive->getNameIndex($index);
                            if (!in_array(substr($file_name, -1), ['/', '\\'], true)) {
                                $zip_path_info = pathinfo($file_name);
                                if (in_array(strtolower($zip_path_info['extension']), $extensions, true)) {
                                    $output[$path . '[' . $index . ']'] = substr($path, $base_path_length)
                                        . ' : ' . $file_name;
                                }
                            }
                        }
                        $zip_archive->close();
                    }
                } else {
                    if (in_array($path_info['extension'], $extensions, true)) {
                        $output[$path] = substr($path, $base_path_length);
                    }
                }
            }
        }
    }
}