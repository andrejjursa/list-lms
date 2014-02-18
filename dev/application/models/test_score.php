<?php

/**
 * Test_score model.
 * @package LIST_CI_Models
 * @author Andrej Jursa
 */
class Test_score extends CI_Model {
    
    public function request_token() {
        $token_ok = FALSE;
        
        do {
            $token = md5(date('U') . '-' . rand(0, 100000));
            $this->db->query('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            $this->db->trans_start();
            $this->db->where('token', $token);
            $query = $this->db->get('test_scores');
            if ($query->num_rows() == 0) {
                $this->db->set('token', $token);
                $this->db->insert('test_scores');
                $token_ok = TRUE;
            }
            $query->free_result();
            $this->db->trans_complete();
        
        } while(!$token_ok);
        
        return $token;
    }
    
    public function delete_token($token) {
        $this->db->query('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        $this->db->where('token', $token);
        $this->db->delete('test_scores');
        $this->db->trans_complete();
    }

    public function get_data_for_student($student_id, $token) {
        $result = array();
        
        $this->db->query('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        $this->db->where('student_id', (int)$student_id);
        $this->db->where('token', $token);
        $query = $this->db->get('test_scores');
        foreach ($query->result() as $result_object) {
            $result[(int)$result_object->task_id] = (int)$result_object->score;
        }
        $query->free_result();
        $this->db->trans_complete();
        
        return $result;
    }
    
    public function set_score_for_task($student_id, $task_id, $token, $score) {
        $this->db->query('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        $this->db->where('task_id', (int)$task_id);
        $this->db->where('student_id', (int)$student_id);
        $this->db->where('token', $token);
        $query = $this->db->get('test_scores');
        if ($query->num_rows() == 0) {
            $this->db->set('token', $token);
            $this->db->set('task_id', (int)$task_id);
            $this->db->set('student_id', (int)$student_id);
            $this->db->set('score', (int)$score);
            $this->db->insert('test_scores');
        } else {
            $this->db->set('score', (int)$score);
            $this->db->where('task_id', (int)$task_id);
            $this->db->where('student_id', (int)$student_id);
            $this->db->where('token', $token);
            $this->db->update('test_scores');
        }
        $query->free_result();
        $this->db->trans_complete();
    }
    
}