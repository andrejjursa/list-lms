<?php

/**
 * Solution model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Solution extends DataMapper {
    
    public $has_one = array(
        'task_set',
        'student',
        'teacher',
    );
    
    public $has_many = array(
        'solution_version',
    );

    private $solution_versions_ip_address_cache = [];


    /**
     * Delete this object from database or specified relations.
     * If this object is deleted, all student files will be deleted as well.
     * @param DataMapper|string $object related object to delete from relation.
     * @param string $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '') {
        $this_id = $this->id;
        $this_task_set_id = $this->task_set_id;
        $this_student_id = $this->student_id;
        parent::delete($object, $related_field);
        if (empty($object) && !is_array($object) && !empty($this_id)) {
            $task_set = new Task_set();
            $task_set->get_by_id($this_task_set_id);
            if ($task_set->exists()) {
                $student_files = $task_set->get_student_files($this_student_id);
                if (count($student_files) > 0) { foreach($student_files as $file) {
                    @unlink($file['filepath']);
                }}
            }
        }
    }

    public function getSolutionVersionsDistinctIPAddresses() {
        if ($this->id === null) { return []; }
        if (!isset($this->solution_versions_ip_address_cache[$this->id])) {
            $solution_versions = new Solution_version();
            $solution_versions->distinct();
            $solution_versions->select('ip_address');
            $solution_versions->where_related_solution($this);
            $solution_versions->where('ip_address !=', '');
            $solution_versions->order_by('ip_address');
            $solution_versions->get_iterated();
            $this->solution_versions_ip_address_cache[$this->id] = [];
            foreach ($solution_versions as $solution_version) {
                $this->solution_versions_ip_address_cache[$this->id][] = $solution_version->ip_address;
            }
        }
        return $this->solution_versions_ip_address_cache[$this->id];
    }

    public function isSolutionSuspicious() {
        $ip_adresses = $this->getSolutionVersionsDistinctIPAddresses();

        return count($ip_adresses) > 1;
    }
    
}