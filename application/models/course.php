<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Course model.
 *
 * @property int                  $id
 * @property string               $updated                date time format YYYY-MM-DD HH:MM:SS
 * @property string               $created                date time format YYYY-MM-DD HH:MM:SS
 * @property string               $name
 * @property int|null             $period_id              entity id of model {@see Period}
 * @property string               $description
 * @property int                  $capacity
 * @property string|null          $groups_change_deadline date time format YYYY-MM-DD HH:MM:SS
 * @property double               $default_points_to_remove
 * @property string|null          $allow_subscription_to  date time format YYYY-MM-DD HH:MM:SS
 * @property string               $test_scoring_deadline  date time format YYYY-MM-DD HH:MM:SS
 * @property bool                 $hide_in_lists
 * @property bool                 $auto_accept_students
 * @property string|null          $syllabus
 * @property string|null          $grading
 * @property string|null          $instructions
 * @property string|null          $other_texts
 * @property bool                 $disable_public_groups_page
 * @property string|null          $additional_menu_links
 * @property Period               $period
 * @property Group                $group
 * @property Task_set_type        $task_set_type
 * @property Task_set             $task_set
 * @property Participant          $participant
 * @property Student              $active_for_student
 * @property Teacher              $prefered_for_teacher
 * @property Course_content       $course_content
 * @property Course_content_group $course_content_group
 *
 * @method DataMapper where_related_period(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_group(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set_type(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_task_set(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_active_for_student(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_participant(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_prefered_for_teacher(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_course_content(mixed $related, string $field = null, string $value = null)
 * @method DataMapper where_related_course_content_group(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 *
 */
class Course extends DataMapper implements DataMapperExtensionsInterface
{
    
    //const ACTIVE_COURSE_SESSION_NAME = 'ACTIVE_COURSE_SESSION';
    
    public $has_one = [
        'period',
    ];
    
    public $has_many = [
        'group',
        'task_set_type'        => [
            'join_table' => 'course_task_set_type_rel',
        ],
        'task_set',
        'participant',
        'active_for_student'   => [
            'class'       => 'student',
            'other_field' => 'active_course',
        ],
        'prefered_for_teacher' => [
            'class'          => 'teacher',
            'other_field'    => 'prefered_course',
            'join_self_as'   => 'prefered_course',
            'joint_other_as' => 'prefered_for_teacher',
        ],
        'course_content',
        'course_content_group',
    ];
    
    
    public static function get_all_courses_for_form_select(): array
    {
        $courses = new Course();
        $courses->order_by_related('period', 'sorting', 'asc');
        $courses->order_by_with_constant('name', 'asc');
        $courses->include_related('period', 'name');
        $courses->get_iterated();
        
        $output = [null => ''];
        
        $ci =& get_instance();
        
        foreach ($courses as $course) {
            $output[$ci->lang->text($course->period_name)][$course->id] = $ci->lang->text($course->name);
        }
        
        return $output;
    }
    
    /**
     * Tests if this course has allowed subscription.
     *
     * @return boolean TRUE when subscription is allowed for this (existing) course, FALSE otherwise.
     */
    public function is_subscription_allowed(): bool
    {
        if (!is_null($this->id)) {
            if (is_null($this->allow_subscription_to)) {
                return true;
            }
            if (time() <= strtotime($this->allow_subscription_to)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Initialise download of all solutions in all task sets belonging to this course.
     */
    public function download_all_solutions(): void
    {
        if (!is_null($this->id)) {
            $task_sets = new Task_set();
            $task_sets->where_related('course', $this);
            $task_sets->get_iterated();
            $filename = $this->get_new_solution_zip_filename();
            $zip_archive = new ZipArchive();
            if ($zip_archive->open($filename, ZipArchive::CREATE)) {
                $period = $this->period->get();
                $readme = $this->lang->text($this->name);
                $readme .= "\r\n" . $this->lang->text($period->name);
                $zip_archive->addFromString('readme.txt', $readme);
                foreach ($task_sets as $task_set) {
                    $overlay_name = $this->lang->get_overlay('task_sets', $task_set->id, 'name');
                    $task_set->add_files_to_zip_archive(
                        $zip_archive,
                        $this,
                        normalizeForFilesystem(
                            trim($overlay_name) === '' ? $task_set->name : $overlay_name
                        ) . '_(' . $task_set->id . ')'
                    );
                }
                $zip_archive->close();
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename=' . basename($filename));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filename));
                ob_clean();
                flush();
                $f = fopen($filename, 'r');
                while (!feof($f)) {
                    echo fread($f, 1024);
                }
                fclose($f);
                unlink($filename);
            } else {
                header("HTTP/1.0 404 Not Found");
            }
        } else {
            header("HTTP/1.0 404 Not Found");
        }
        die();
    }
    
    /**
     * Returns unused file name for solution download.
     *
     * @return string file name with path.
     */
    private function get_new_solution_zip_filename(): string
    {
        $path = 'private/extracted_solutions/';
        $i = 0;
        do {
            $filename = 'course_solutions_' . date('U') . '_' . (string)($this->id ?? 'unknown')
                . ($i > 0 ? '_' . $i : '') . '.zip';
            $i++;
        } while (file_exists($path . $filename));
        return $path . $filename;
    }
}