<?php

/**
 * Log model.
 * @package LIST_DM_Models
 * @author Andrej Jursa
 */
class Log extends DataMapper {
    
    const LOG_TYPE_STUDENT_SOLUTION_UPLOAD = 1;
    const LOG_TYPE_TEACHER_SOLUTION_UPLOAD = 2;
    const LOG_TYPE_STUDENT_LOGIN = 3;
    const LOG_TYPE_STUDENT_SOLUTION_DOWNLOAD = 4;

    public $has_one = array(
        'student',
        'teacher',
    );
    
    /**
     * Add log message about student solution upload.
     * @param string $message message text.
     * @param int|Student $student student id or student model.
     * @param int $table_id solutions table id affected.
     * @param string $language language idiom, NULL means autodetect.
     * @return boolean save operation status.
     */
    public function add_student_solution_upload_log($message, $student, $table_id, $language = NULL) {
        if (is_null($language)) { $language = $this->lang->get_current_idiom(); }
        $this->language = $language;
        $this->message = $message;
        $this->ip_address = $_SERVER["REMOTE_ADDR"];
        if (is_numeric($student)) {
            $this->student_id = (int)$student;
        } elseif ($student instanceOf Student) {
            $this->student_id = $student->id;
        } else {
            $this->student_id = NULL;
        }
        $this->teacher_id = NULL;
        $this->log_type = self::LOG_TYPE_STUDENT_SOLUTION_UPLOAD;
        $this->additional_data = serialize(array());
        $this->affected_table = 'solutions';
        $this->affected_row_primary_id = (int)$table_id;
        return $this->save();
    }
    
    /**
     * Add log message about student solution upload by teacher.
     * @param string $message message text.
     * @param int|Teacher $teacher teacher id or teacher model.
     * @param int|Student $student student id or student model.
     * @param int $table_id solutions table id affected.
     * @param string $language language idiom, NULL means autodetect.
     * @return boolean save operation status.
     */
    public function add_teacher_solution_upload_log($message, $teacher, $student, $table_id, $language = NULL) {
        if (is_null($language)) { $language = $this->lang->get_current_idiom(); }
        $this->language = $language;
        $this->message = $message;
        $this->ip_address = $_SERVER["REMOTE_ADDR"];
        if (is_numeric($student)) {
            $this->student_id = (int)$student;
        } elseif ($student instanceOf Student) {
            $this->student_id = $student->id;
        } else {
            $this->student_id = NULL;
        }
        if (is_numeric($teacher)) {
            $this->teacher_id = (int)$teacher;
        } elseif ($teacher instanceOf Teacher) {
            $this->teacher_id = $teacher->id;
        } else {
            $this->teacher_id = NULL;
        }
        $this->log_type = self::LOG_TYPE_TEACHER_SOLUTION_UPLOAD;
        $this->additional_data = serialize(array());
        $this->affected_table = 'solutions';
        $this->affected_row_primary_id = (int)$table_id;
        return $this->save();
    }
    
    /**
     * Add log message about student login into system.
     * @param string $message message text.
     * @param int|Teacher $teacher teacher id or teacher model.
     * @param int|Student $student student id or student model.
     * @param string $language language idiom, NULL means autodetect.
     * @return boolean save operation status.
     */
    public function add_student_login_log($message, $teacher, $student, $language = NULL) {
        if (is_null($language)) { $language = $this->lang->get_current_idiom(); }
        $this->language = $language;
        $this->message = $message;
        $this->ip_address = $_SERVER['REMOTE_ADDR'];
        if (is_numeric($student)) {
            $this->student_id = (int)$student;
        } elseif ($student instanceOf Student) {
            $this->student_id = $student->id;
        } else {
            $this->student_id = NULL;
        }
        if (is_numeric($teacher)) {
            $this->teacher_id = (int)$teacher;
        } elseif ($teacher instanceOf Teacher) {
            $this->teacher_id = $teacher->id;
        } else {
            $this->teacher_id = NULL;
        }
        $this->log_type = self::LOG_TYPE_STUDENT_LOGIN;
        $this->additional_data = serialize(array());
        return $this->save();
    }
    
    /**
     * Add log message about solution file download from student interface.
     * @param string $message message text.
     * @param int|Student $student student id or student model.
     * @param string $solution_file name of solution file.
     * @param int $task_set_id id of task set from which is file downloaded.
     * @param string $language language idiom, NULL means autodetect.
     * @return boolean save operation status.
     */
    public function add_student_solution_download_log($message, $student, $solution_file, $task_set_id, $language = NULL) {
        if (is_null($language)) { $language = $this->lang->get_current_idiom(); }
        $this->language = $language;
        $this->message = $message;
        $this->ip_address = $_SERVER['REMOTE_ADDR'];
        if (is_numeric($student)) {
            $this->student_id = (int)$student;
        } elseif ($student instanceOf Student) {
            $this->student_id = $student->id;
        } else {
            $this->student_id = NULL;
        }
        $this->teacher_id = NULL;
        $this->log_type = self::LOG_TYPE_STUDENT_SOLUTION_DOWNLOAD;
        $this->additional_data = serialize(array(
            'task_set_id' => $task_set_id,
            'solution_file' => $solution_file,
        ));
        return $this->save();
    }
    
}