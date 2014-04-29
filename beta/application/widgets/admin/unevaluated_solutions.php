<?php

class Unevaluated_solutions extends abstract_admin_widget {
    
    public function getContentTypeName() {
        return $this->lang->line('widget_admin_unevaluated_solutions_widget_type_name');
    }

    public function mergeConfiguration($old_configuration, $new_configuration) {
        if (!is_array($old_configuration)) {
            return $new_configuration;
        }
        return array_merge($old_configuration, $new_configuration);
    }

    public function preConfigureForm() {
        $courses = new Course();
        $courses->include_related('period');
        $courses->order_by_related('period', 'sorting', 'asc');
        $courses->order_by_with_constant('name');
        $courses->get_iterated();
        
        $courses_list = array('' => '');
        
        foreach ($courses as $course) {
            $courses_list[$this->lang->text($course->period_name)][$course->id] = $this->lang->text($course->name);
        }
        
        $this->parser->assign('courses', $courses_list);
    }

    public function render() {
        $course = new Course();
        $course->include_related('period');
        $course->get_by_id((int)@$this->config['course_id']);
        
        $this->parser->assign('course', $course);
        
        if ($course->exists()) {
            $solutions = new Solution();
            $solutions->select_func('COUNT', 'id', 'count');
            $solutions->where('revalidate', 1);
            $solutions->where_related('task_set', 'id', '${parent}.id');
            
            $task_sets = new Task_set();
            $task_sets->select('*');
            $task_sets->select_subquery($solutions, 'solutions_count');
            $task_sets->where_related($course);
            $task_sets->where_related('solution', 'revalidate', 1);
            $task_sets->group_by('id');
            $task_sets->order_by_with_overlay('name', 'ASC');
            $task_sets->get_iterated();
            
            $this->parser->assign('task_sets', $task_sets);
        }
        $this->parser->parse('widgets/admin/unevaluated_solutions/main.tpl');
    }

    public function validateConfiguration($configuration) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('configure[course_id]', 'lang:widget_admin_unevaluated_solutions_configure_form_field_course', 'required');
        
        return $this->form_validation->run();
    }    
    
    public function defaultConfiguration() {
        return array(
            'course_id' => NULL,
        );
    }
}