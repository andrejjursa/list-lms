<?php

/**
 * Test_score model.
 *
 * @property CI_DB $db
 *
 * @package LIST_CI_Models
 * @author  Andrej Jursa
 */
class Test_score extends CI_Model
{
    
    public function request_token(): string
    {
        $token_ok = false;
        
        do {
            $token = md5(date('U') . '-' . rand(0, 100000));
            $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
            $this->db->trans_start();
            $this->db->where('token', $token);
            $query = $this->db->get('test_scores');
            if ($query->num_rows() === 0) {
                $this->db->set('token', $token);
                $this->db->set('test_type', '-');
                $this->db->insert('test_scores');
                $token_ok = true;
            }
            $query->free_result();
            $this->db->trans_complete();
            
        } while (!$token_ok);
        
        return $token;
    }
    
    public function delete_token($token): void
    {
        $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        $this->db->where('token', $token);
        $this->db->delete('test_scores');
        $this->db->trans_complete();
    }
    
    public function delete_old_scores(): void
    {
        $min_time = date('Y-m-d H:i:s', strtotime('now -1 day'));
        $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        $this->db->where('updated <', $min_time);
        $this->db->delete('test_scores');
        $this->db->trans_complete();
    }
    
    public function get_data_for_student($student_id, $token, $test_type): array
    {
        $result = [];
        
        $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        $this->db->where('student_id', (int)$student_id);
        $this->db->where('token', $token);
        $this->db->where('test_type', $test_type);
        $query = $this->db->get('test_scores');
        foreach ($query->result() as $result_object) {
            $result[(int)$result_object->task_id] = (int)$result_object->score;
        }
        $query->free_result();
        $this->db->trans_complete();
        
        return $result;
    }
    
    public function set_score_for_task($student_id, $task_id, $token, $score, $test_type): void
    {
        $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE;');
        $this->db->trans_start();
        $this->db->where('task_id', (int)$task_id);
        $this->db->where('student_id', (int)$student_id);
        $this->db->where('token', $token);
        $this->db->where('test_type', $test_type);
        $query = $this->db->get('test_scores');
        if ($query->num_rows() === 0) {
            $this->db->set('token', $token);
            $this->db->set('task_id', (int)$task_id);
            $this->db->set('student_id', (int)$student_id);
            $this->db->set('score', (int)$score);
            $this->db->set('test_type', $test_type);
            $this->db->insert('test_scores');
        } else {
            $this->db->set('score', 'score+' . (int)$score, false);
            $this->db->where('task_id', (int)$task_id);
            $this->db->where('student_id', (int)$student_id);
            $this->db->where('token', $token);
            $this->db->where('test_type', $test_type);
            $this->db->update('test_scores');
        }
        $query->free_result();
        $this->db->trans_complete();
    }
    
}