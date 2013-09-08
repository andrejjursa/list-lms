<?php

/**
 * LaMSfET data import helper functions.
 * @package LIST_Helpers
 * @author Andrej Jursa
 */

function lamsfet_download_file($path, $download_to) {
    if (remote_file_exists($path)) {
        $file = fopen($path, 'r');
        ob_start();
        fpassthru($file);
        $file_content = ob_get_clean();
        fclose($file);
        $file_w = fopen($download_to, 'w');
        fwrite($file_w, $file_content);
        fclose($file_w);
        return TRUE;
    }
    return FALSE;
}

function lamsfet_fetch_table($table, $db, $id = 'id') {
    $data = array();
    $query = $db->get($table);
    foreach ($query->result() as $row) {
        $data[$row->$id] = $row;
    }
    echo 'Fetched ' . $query->num_rows() . ' rows of ' . $table . ' from LaMSfET ...' . "\n";
    $query->free_result();
    return $data;
}

function list_import_prepare() {
    $CI =& get_instance();
    $categories = new Category();
    $categories->truncate();
    echo 'LIST categories table truncated ...' . "\n";
    $tasks = new Task();
    $tasks->truncate();
    $CI->lang->delete_overlays('tasks');
    unlink_recursive('private/uploads/task_files/', FALSE);
    unlink_recursive('private/uploads/unit_tests/', FALSE);
    echo 'LIST tasks table truncated ...' . "\n";
    $CI->db->simple_query('TRUNCATE TABLE `task_category_rel`');
    echo 'LIST task_category_rel table truncated ...' . "\n";
    $task_set_types = new Task_set_type();
    $task_set_types->truncate();
    echo 'LIST task_set_types table truncated ...' . "\n";
    $task_sets = new Task_set();
    $task_sets->truncate();
    $CI->lang->delete_overlays('task_sets');
    echo 'LIST task_sets table truncated ...' . "\n";
    $solutions = new Solution();
    $solutions->truncate();
    unlink_recursive('private/uploads/solutions/', FALSE);
    echo 'LIST solutions table truncated ...' . "\n";
    $CI->db->simple_query('TRUNCATE TABLE `task_task_set_rel`');
    echo 'LIST task_task_set_rel table truncated ...' . "\n";
}

function list_import_lamsfet_labels(&$labels) {
    echo 'Starting categories import (' . count($labels) . ') ';
    
    $structured_labels = array();
    if (count($labels)) { foreach($labels as $label) {
        $structured_labels[$label->parent_id][] = $label;
    }}
    
    echo '... structure prepared ... [';
    
    $import_structure = function($parent_id, $structured_labels, &$labels) use (&$import_structure) {
        if (isset($structured_labels[$parent_id]) && count($structured_labels[$parent_id])) {
            foreach ($structured_labels[$parent_id] as $label) {
                $parent_id = NULL;
                if ($label->parent_id > 0 && isset($labels[$label->parent_id]->_list_id)) {
                    $parent_id = $labels[$label->parent_id]->_list_id;
                }
                $parent_category = new Category();
                if (!is_null($parent_id)) {
                    $parent_category->get_by_id($parent_id);
                }
                $category = new Category();
                $category->name = $label->name;
                $category->save(array('parent' => $parent_category));
                echo '.';
                $labels[$label->id]->_list_id = $category->id;
                $import_structure($label->id, $structured_labels, $labels);
            }
        }
    };
    
    $import_structure(0, $structured_labels, $labels);
    
    echo '] ... done' . "\n";
}

function list_import_lamsfet_tasks(&$tasks, $lamsfet_url) {
    echo 'Starting tasks import (' . count($tasks) . ') [';
    
    include_once(APPPATH . 'third_party/simplehtmldom/simple_html_dom.php');
    if (count($tasks)) { foreach($tasks as $task_id => $task) {
        $list_task = new Task();
        $list_task->name = $task->name;
        $list_task->save();
        $tasks[$task_id]->_list_id = $list_task->id;
        $text_html = str_get_html(lamsfet_task_get_formatted_text($task->text), true, true, DEFAULT_TARGET_CHARSET, false);
        foreach ($text_html->find('a') as $element) {
            if (!preg_match('/^[a-zA-Z]+\:\/\//', trim($element->href))) {
                $element->href = lamsfet_import_task_file_to_local_list_storage(rtrim($lamsfet_url, '\\/') . '/' . trim($element->href), $element->href, $list_task->id);
            }
        }
        foreach ($text_html->find('img') as $element) {
            if (!preg_match('/^[a-zA-Z]+\:\/\//', trim($element->src))) {
                $element->src = lamsfet_import_task_file_to_local_list_storage(rtrim($lamsfet_url, '\\/') . '/' . trim($element->src), $element->src, $list_task->id);
            }
        }
        $list_task->text = $text_html->__toString();
        $list_task->save();
        echo '.';
    }}
    
    echo '] ... done' . "\n";
}

function list_import_lamsfet_tasks_labels_relations($tasks, $labels, $tasks_labels) {
    echo 'Starting task_category_rel import (' . count($tasks_labels) . ') [';
    
    if (count($tasks_labels)) { foreach($tasks_labels as $relation) {
        $task_id = $tasks[$relation->task_id]->_list_id;
        $category_id = $labels[$relation->label_id]->_list_id;
        $task = new Task();
        $task->get_by_id(intval($task_id));
        $category = new Category();
        $category->get_by_id(intval($category_id));
        if ($task->exists() && $category->exists()) {
            $task->save($category);
        } else {
            echo ' ( CATEGORY OR TASK NOT FOUND ' . $task_id . '(' .  $relation->task_id. ')/' . $category_id . '(' . $relation->label_id . ') ) ';
        }
        echo '.';
    }}
    
    echo '] ... done' . "\n";
}

function list_import_lamsfet_set_types(&$set_types) {
    echo 'Starting task_set_types import (' . count($set_types) . ') [';
    
    if (count($set_types)) { foreach($set_types as $id => $set_type) {
        $task_set_type = new Task_set_type();
        $task_set_type->name = $set_type->name;
        $task_set_type->save();
        $set_types[$id]->_list_id = $task_set_type->id;
        echo '.';
    }}
    
    echo '] ... done' . "\n";
}

function list_import_lamsfet_sets(&$sets, $set_types) {
    echo 'Starting task_sets import (' . count($sets) . ') [';
    
    if (count($sets)) { foreach($sets as $id => $set) {
        $task_set_type_id = $set_types[$set->set_type_id]->_list_id;
        $task_set_type = new Task_set_type();
        $task_set_type->get_by_id(intval($task_set_type_id));
        $task_set = new Task_set();
        $task_set->name = $set->name;
        if (!is_null($set->comment)) {
            $task_set->instructions = nl2br($set->comment);
        }
        if (!empty($set->date_from)) {
            $task_set->publish_start_time = strtotime($set->date_from);
        }
        if (!empty($set->submit_to)) {
            $task_set->upload_end_time = strtotime($set->submit_to);
        }
        $task_set->save($task_set_type);
        $sets[$id]->_list_id = $task_set->id;
        echo '.';
    }}
    
    echo '] ... done' . "\n";
}

function list_import_lamsfet_tasks_in_sets_relation($sets, $tasks, $tasks_in_sets) {
    echo 'Starting task_task_set_rel import (' . count($tasks_in_sets) . ') [';
    
    if (count($tasks_in_sets)) { foreach($tasks_in_sets as $task_in_set) {
        $task_set_id = $sets[$task_in_set->set_id]->_list_id;
        $task_id = $tasks[$task_in_set->task_id]->_list_id;
        $task_set = new Task_set();
        $task_set->get_by_id(intval($task_set_id));
        $task = new Task();
        $task->get_by_id(intval($task_id));
        if ($task_set->exists() && $task->exists()) {
            $task_set->save($task);
            $task_set->set_join_field($task, 'sorting', $task_in_set->order);
            $task_set->set_join_field($task, 'points_total', floatval($task_in_set->points));
            $task_set->set_join_field($task, 'bonus_task', 0);
        } else {
            echo ' ( TASK SET OR TASK NOT FOUND ' . $task_set_id . '(' .  $task_in_set->set_id. ')/' . $task_id . '(' .  $task_in_set->task_id. ') ) ';
        }
        echo '.';
    }}
    
    echo '] ... done' . "\n";
}

function lamsfet_task_get_formatted_text($text) {
    $parts = explode("<code>", $text);
    $formatted = array_shift($parts);
    foreach($parts as $part) {
        $code = explode("</code>", $part);
        $formatted .= "<code>" . htmlspecialchars(str_replace("&#039;", "'", array_shift($code))) . "</code>" . implode("</code>", $code);
    }
    return lamsfet_task_break_lines($formatted);
}
  
function lamsfet_task_break_lines($text) {
    $parts = explode("<pre>", $text);
    $formatted = nl2br(array_shift($parts));
    foreach($parts as $part) {
        $code = explode("</pre>", $part);
        $formatted .= "<pre>" . array_shift($code) . "</pre>";
        $unformatted = array();
        foreach($code as $cd) { $unformatted[] = nl2br($cd); }
        $formatted .=  implode("</pre>", $unformatted);
    }
    return $formatted;
}

function lamsfet_import_task_file_to_local_list_storage($full_path, $original_path, $list_task_id) {
    $local_path = 'private/uploads/task_files/task_' . $list_task_id . '/hidden/';
    if (!file_exists($local_path)) {
        @mkdir('private/uploads/task_files/task_' . $list_task_id, 0744);
        @mkdir('private/uploads/task_files/task_' . $list_task_id . '/hidden', 0744);
    }
    $file_name = basename($full_path);
    $full_local_path = $local_path . $file_name;
    
    if (lamsfet_download_file($full_path, $full_local_path)) {
        return 'index.php/tasks/download_hidden_file/' . $list_task_id . '/' . encode_for_url($file_name);
    } else {
        echo ' ( FILE NOT FOUND ' . $full_path . ' ) ';
        log_message('ERROR', 'FILE NOT FOUND ' . $full_path, FALSE);
        return $original_path;
    }
}

function remote_file_exists($url) {
    $curl = curl_init($url);

    //don't fetch the actual page, you only want to check the connection is ok
    curl_setopt($curl, CURLOPT_NOBODY, true);

    //do request
    $result = curl_exec($curl);

    $ret = false;

    //if request did not fail
    if ($result !== false) {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

        if ($statusCode == 200) {
            $ret = true;   
        }
    }

    curl_close($curl);

    return $ret;
}