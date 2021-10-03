<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Solution model.
 *
 * @property int              $id
 * @property string           $updated     date time format YYYY-MM-DD HH:MM:SS
 * @property string           $created     date time format YYYY-MM-DD HH:MM:SS
 * @property int|null         $task_set_id entity id of model {@see Task_set}
 * @property int|null         $student_id  entity id of model {@see Student}
 * @property int|null         $teacher_id  entity id of model {@see Teacher}
 * @property string|null      $comment
 * @property double           $tests_points
 * @property bool             $revalidate
 * @property bool             $not_considered
 * @property string           $ip_address
 * @property int              $best_version
 * @property bool             $disable_evaluation_by_tests
 * @property double           $points
 * @property Task_set         $task_set
 * @property Student          $student
 * @property Teacher          $teacher
 * @property Solution_version $solution_version
 *
 * @method DataMapper where_related_task_set(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_student(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_teacher(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_solution_version(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 */
class Solution extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_one = [
        'task_set',
        'student',
        'teacher',
    ];
    
    public $has_many = [
        'solution_version',
    ];
    
    private $solution_versions_ip_address_cache = [];
    
    
    /**
     * Delete this object from database or specified relations.
     * If this object is deleted, all student files will be deleted as well.
     *
     * @param DataMapper|string $object        related object to delete from relation.
     * @param string            $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '')
    {
        $this_id = $this->id;
        $this_task_set_id = $this->task_set_id;
        $this_student_id = $this->student_id;
        parent::delete($object, $related_field);
        if (empty($object) && !is_array($object) && !empty($this_id)) {
            $task_set = new Task_set();
            $task_set->get_by_id($this_task_set_id);
            if ($task_set->exists()) {
                $student_files = $task_set->get_student_files($this_student_id);
                if (count($student_files) > 0) {
                    foreach ($student_files as $file) {
                        @unlink($file['filepath']);
                    }
                }
            }
        }
    }
    
    public function getSolutionVersionsDistinctIPAddresses()
    {
        if ($this->id === null) {
            return [];
        }
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
    
    public function isSolutionSuspicious(): bool
    {
        $ip_addresses = $this->getSolutionVersionsDistinctIPAddresses();
        
        return count($ip_addresses) > 1;
    }
    
}