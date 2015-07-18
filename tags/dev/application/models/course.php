<?php

/**
 * Course model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Course extends DataMapper {
    
    //const ACTIVE_COURSE_SESSION_NAME = 'ACTIVE_COURSE_SESSION';
    
    public $has_one = array(
        'period'
    );
    
    public $has_many = array(
        'group',
    	'task_set_type' => array(
    		'join_table' => 'course_task_set_type_rel',
    	),
    	'task_set',
        'participant',
        'active_for_student' => array(
            'class' => 'student',
            'other_field' => 'active_course',
        ),
        'prefered_for_teacher' => array(
            'class' => 'teacher',
            'other_field' => 'prefered_course',
            'join_self_as' => 'prefered_course',
            'joint_other_as' => 'prefered_for_teacher',
        ),
    );
    
    /**
     * Tests if this course have allowed subscription.
     * @return boolean TRUE when subscription is allowed for this (existing) course, FALSE otherwise.
     */
    public function is_subscription_allowed() {
        if (!is_null($this->id)) {
            if (is_null($this->allow_subscription_to)) { return TRUE; }
            if (time() <= strtotime($this->allow_subscription_to)) { return TRUE; }
        }
        return FALSE;
    }
    
    /**
     * Initialise download of all solutions in all task sets belonging to this course.
     */
    public function download_all_solutions() {
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
                    $task_set->add_files_to_zip_archive($zip_archive, $this, normalizeForFilesystem(trim($overlay_name) == '' ? $task_set->name : $overlay_name) . '_(' . $task_set->id . ')');
                }
                $zip_archive->close();
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename='.basename($filename));
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
     * @return string file name with path.
     */
    private function get_new_solution_zip_filename() {
        $path = 'private/extracted_solutions/';
        $filename = '';
        $i = 0;
        do {
            $filename = 'course_solutions_' . date('U') . '_' . ($this->id != NULL ? $this->id : 'unknown') . ($i > 0 ? '_' . $i : '') . '.zip';
            $i++;
        } while (file_exists($path . $filename));
        return $path . $filename;
    }
}