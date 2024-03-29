<?php

/**
 * Course overview widget.
 *
 * @author Andrej
 */
class Course_overview extends abstract_admin_widget
{
    
    public function render(): void
    {
        if (isset($this->config['course_id'])) {
            $course = new Course();
            $course->include_related('period');
            $course->get_by_id((int)$this->config['course_id']);
            $this->parser->assign('course', $course);
            
            if ($course->exists()) {
                $task_sets = new Task_set();
                $task_sets->where_related($course);
                $task_sets->where('published', 1);
                $task_sets->where('content_type', 'task_set');
                $task_sets_count = $task_sets->count();
                
                $this->parser->assign('task_sets_count', $task_sets_count);
                
                $task_sets->where_related($course);
                $task_sets->where('published', 1);
                $task_sets->where('content_type', 'project');
                $projects_count = $task_sets->count();
                
                $this->parser->assign('projects_count', $projects_count);
                
                $groups = new Group();
                $groups->where_related($course);
                $groups_count = $groups->count();
                
                $this->parser->assign('groups_count', $groups_count);
                
                $students = new Student();
                $students->where_related('participant/course', 'id', $course->id);
                $students->where_related('participant', 'allowed', 1);
                $students_count = $students->count();
                
                $this->parser->assign('students_count', $students_count);
                
                $task_set_permissions = new Task_set_permission();
                $task_set_permissions->select_func('COUNT', '*', 'count');
                $task_set_permissions->where('enabled', 1);
                $task_set_permissions->where_related('task_set', 'id', '${parent}.id');
                
                $now = date('Y-m-d H:i:s');
                $plus_two_weeks = date('Y-m-d H:i:s', strtotime($now . ' + 2 weeks'));
                $minus_one_week = date('Y-m-d H:i:s', strtotime($now . ' - 1 week'));
                
                $task_sets->select(
                    'id, name, upload_end_time AS min_upload_end_time, upload_end_time AS max_upload_end_time'
                );
                $task_sets->where_related($course);
                $task_sets->where('published', 1);
                $task_sets->where_subquery('0', $task_set_permissions);
                $task_sets->where('upload_end_time >=', $minus_one_week);
                $task_sets->where('upload_end_time <=', $plus_two_weeks);
                
                $task_sets_2 = new Task_set();
                $task_sets_2->select('id, name');
                $task_sets_2->where_related($course);
                $task_sets_2->where('published', 1);
                $task_sets_2->select_min('task_set_permissions.upload_end_time', 'min_upload_end_time');
                $task_sets_2->select_max('task_set_permissions.upload_end_time', 'max_upload_end_time');
                $task_sets_2->where_related('task_set_permission', 'enabled', 1);
                $task_sets_2->having(
                    '(MAX(`task_set_permissions`.`upload_end_time`) >= ' . $this->db->escape($minus_one_week)
                    . ' AND MAX(`task_set_permissions`.`upload_end_time`) <= ' . $this->db->escape($plus_two_weeks)
                    . ')'
                );
                $task_sets_2->or_having(
                    '(MIN(`task_set_permissions`.`upload_end_time`) >= ' . $this->db->escape($minus_one_week)
                    . ' AND MIN(`task_set_permissions`.`upload_end_time`) <= ' . $this->db->escape($plus_two_weeks)
                    . ')'
                );
                $task_sets_2->group_by('id');
                
                $task_sets->union_iterated(
                    $task_sets_2,
                    false,
                    'min_upload_end_time DESC, max_upload_end_time DESC',
                    isset($this->config['number_of_task_sets']) ? (int)$this->config['number_of_task_sets'] : 5
                );
                
                $this->parser->assign('task_sets', $task_sets);
            }
        }
        $this->parser->parse('widgets/admin/course_overview/main.tpl');
    }
    
    public function getContentTypeName(): string
    {
        return $this->lang->line('widget_admin_course_overview_widget_type_name');
    }
    
    public function preConfigureForm(): void
    {
        $courses = new Course();
        $courses->include_related('period');
        $courses->order_by_related('period', 'sorting', 'asc');
        $courses->order_by_with_constant('name');
        $courses->get_iterated();
        
        $courses_list = ['' => ''];
        
        foreach ($courses as $course) {
            $courses_list[$this->lang->text($course->period_name)][$course->id] = $this->lang->text($course->name);
        }
        
        $this->parser->assign('courses', $courses_list);
    }
    
    public function mergeConfiguration($old_configuration, $new_configuration): array
    {
        if (!is_array($old_configuration)) {
            return $new_configuration;
        }
        return array_merge($old_configuration, $new_configuration);
    }
    
    public function validateConfiguration($configuration): bool
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules(
            'configure[course_id]',
            'lang:widget_admin_course_overview_configure_form_field_course',
            'required'
        );
        $this->form_validation->set_rules(
            'configure[number_of_task_sets]',
            'lang:widget_admin_course_overview_configure_form_field_number_of_task_sets',
            'required|integer|greater_than[0]'
        );
        
        return $this->form_validation->run();
    }
    
    public function defaultConfiguration(): array
    {
        return [
            'course_id'           => null,
            'number_of_task_sets' => 5,
        ];
    }
    
}
