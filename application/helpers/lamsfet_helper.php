<?php

/**
 * LaMSfET data import helper functions.
 * @package LIST_Helpers
 * @author Andrej Jursa
 */

function lamsfet_download_file($path, $download_to) {
    if (@file_exists($path) || @remote_file_exists($path)) {
        $file = @fopen($path, 'r');
        if ($file === FALSE) { return FALSE; }
        ob_start();
        fpassthru($file);
        $file_content = ob_get_clean();
        fclose($file);
        $file_w = @fopen($download_to, 'w');
        if ($file_w === FALSE) { return FALSE; }
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
    $periods = new Period();
    $periods->truncate();
    echo 'LIST periods table truncated ...' . "\n";
    $courses = new Course();
    $courses->truncate();
    echo 'LIST courses table truncated ...' . "\n";
    $groups = new Group();
    $groups->truncate();
    echo 'LIST groups table truncated ...' . "\n";
    $rooms = new Room();
    $rooms->truncate();
    echo 'LIST rooms table truncated ...' . "\n";
    $participants = new Participant();
    $participants->truncate();
    echo 'LIST participants table truncated ...' . "\n";
    $CI->db->simple_query('TRUNCATE TABLE `course_task_set_type_rel`');
    echo 'LIST course_task_set_type_rel table truncated ...' . "\n";
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
    $comments = new Comment();
    $comments->truncate();
    echo 'LIST comments table truncated ...' . "\n";
    $solutions = new Solution();
    $solutions->truncate();
    unlink_recursive('private/uploads/solutions/', FALSE);
    echo 'LIST solutions table truncated ...' . "\n";
    $CI->db->simple_query('TRUNCATE TABLE `task_task_set_rel`');
    echo 'LIST task_task_set_rel table truncated ...' . "\n";
}

function list_import_lamsfet_courses_and_courses_terms(&$courses_terms, $courses) {
    echo 'Starting courses import (' . count($courses_terms) . ') and periods import (' . count($courses) . ') ';
    
    $periods = array();
    if (count($courses_terms)) { foreach ($courses_terms as $course_term) {
        $periods[$course_term->year][$course_term->term] = isset($periods[$course_term->year][$course_term->term]) ? $periods[$course_term->year][$course_term->term] : new stdClass();
        $periods[$course_term->year][$course_term->term]->name = (strtoupper($course_term->term) == 'Z' ? 'Zimný semester ' : 'Letný semester ') . $course_term->year;
        $periods[$course_term->year][$course_term->term]->_list_id = NULL;
        $periods[$course_term->year][$course_term->term]->ids[] = $course_term->id;
    }}
    reset($periods);
    krsort($periods);
    if (count($periods)) { foreach ($periods as $year => $data) {
        reset($periods[$year]);
        ksort($periods[$year]);
        reset($periods[$year]);
    }}
    reset($periods);
    
    echo '... structure prepared ... [';
    
    $sorting = 1;
    if (count($periods)) { foreach ($periods as $year => $year_row) { if (count($year_row)) { foreach ($year_row as $term => $period) {
            $list_period = new Period();
            $list_period->name = $period->name;
            $list_period->sorting = $sorting;
            $list_period->save();
            $periods[$year][$term]->_list_id = $list_period->id;
            $sorting++;
            echo '.';
    }}}}
    if (count($courses_terms) && count($courses)) { foreach ($courses_terms as $course_term) {
        $list_course = new Course();
        $list_course->name = $courses[$course_term->course_id]->name;
        $list_course->period_id = $periods[$course_term->year][$course_term->term]->_list_id;
        $list_course->capacity = 0;
        $list_course->groups_change_deadline = 0;
        $list_course->allow_subscription_to = '1970-01-01 00:00:00';
        $list_course->save();
        $courses_terms[$course_term->id]->_list_id = $list_course->id;
        echo '.';
    }}
    
    echo '] ... done' . "\n";
}

function list_import_lamsfet_courses_set_types_relation($courses_set_types, $courses_terms, $set_types) {
    echo 'Starting course_task_set_type_rel import (' . count($courses_set_types) . ') ';
    
    if (count($courses_set_types)) { foreach ($courses_set_types as $course_set_type) {
        $course_id = $courses_terms[$course_set_type->course_term_id]->_list_id;
        $task_set_type_id = $set_types[$course_set_type->set_type_id]->_list_id;
        $course = new Course();
        $course->get_by_id(intval($course_id));
        $task_set_type = new Task_set_type();
        $task_set_type->get_by_id(intval($task_set_type_id));
        if ($course->exists() && $task_set_type->exists()) {
            $course->save($task_set_type);
            $course->set_join_field($task_set_type, 'upload_solution', $course_set_type->submit_allowed == 't' ? 1 : 0);
            echo '.';
        } else {
            echo ' ( TASK SET TYPE OR COURSE NOT FOUND ' . $task_set_type_id . '(' .  $course_set_type->set_type_id . ')/' . $course_id . '(' .  $course_set_type->course_term_id . ') ) ';
        }
    }}
    
    echo '] ... done' . "\n";
}

function list_import_lamsfet_excercise_groups(&$excercise_groups, $courses_terms) {
    echo 'Starting groups import (' . count($excercise_groups) . ') ';
    
    $days = array(1 => 'Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok', 'Sobora', 'Nedeľa');
    
    if (count($excercise_groups)) { foreach ($excercise_groups as $id => $excercise_group) {
        $course_id = $courses_terms[$excercise_group->course_term_id]->_list_id;
        $course = new Course();
        $course->get_by_id(intval($course_id));
        if ($course->exists()) {
            $group = new Group();
            list($h, $m, $s) = explode(':', $excercise_group->time);
            $time_begin = (int)$s + (int)$m * 60 + (int)$h * 3600;
            if ($time_begin >= 86400) {
                $excercise_group->time = '00:00:00';
                $time_begin = 0;
            }
            $group->name = 'Skupina ' . $excercise_group->place . ' ' . $days[$excercise_group->day] . ' ' . $excercise_group->time;
            $group->save($course);
            $excercise_groups[$id]->_list_id = $group->id;
            $room = new Room();
            $room->name = $excercise_group->place;
            $room->time_day = $excercise_group->day;
            $room->time_begin = $time_begin; 
            $room->time_end = $room->time_begin + 5400;
            $room->capacity = $excercise_group->capacity;
            $room->teachers_plain = trim($excercise_group->teacher) != '' ? trim($excercise_group->teacher) : NULL;
            $room->save($group);
            $course->capacity += $room->capacity;
            $course->save();
            echo '.';
        } else {
            echo ' ( COURSE NOT FOUND ' . $course_id . '(' . $excercise_group->course_term_id . ') ) ';
        }
    }}
    
    echo '] ... done' . "\n";
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
                $element->href = lamsfet_import_task_file_to_local_list_storage(rtrim($lamsfet_url, '\\/') . '/' . trim($element->href), $element->href, $list_task->id, $task->id);
            }
        }
        foreach ($text_html->find('img') as $element) {
            if (!preg_match('/^[a-zA-Z]+\:\/\//', trim($element->src))) {
                $element->src = lamsfet_import_task_file_to_local_list_storage(rtrim($lamsfet_url, '\\/') . '/' . trim($element->src), $element->src, $list_task->id, $task->id);
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

function list_import_lamsfet_sets(&$sets, $set_types, $courses_terms, $excercise_groups) {
    echo 'Starting task_sets import (' . count($sets) . ') [';
    
    if (count($sets)) { foreach($sets as $id => $set) {
        $task_set_type_id = $set_types[$set->set_type_id]->_list_id;
        $task_set_type = new Task_set_type();
        $task_set_type->get_by_id(intval($task_set_type_id));
        $course_id = $courses_terms[$set->course_term_id]->_list_id;
        $course = new Course();
        $course->get_by_id(intval($course_id));
        $group_id = !is_null($set->excercise_group_id) ? $excercise_groups[$set->excercise_group_id]->_list_id : NULL;
        $group = new Group();
        if (!is_null($group_id)) {
            $group->get_by_id(intval($group_id));
        }
        $task_set = new Task_set();
        $task_set->name = $set->name;
        if (!is_null($set->comment)) {
            $task_set->instructions = nl2br($set->comment);
        }
        if (!empty($set->date_from)) {
            $task_set->publish_start_time = $set->date_from;
        }
        if (!empty($set->submit_to)) {
            $task_set->upload_end_time = $set->submit_to;
        }
        $task_set->published = 1;
        $task_set->save(array($task_set_type, $group, $course));
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

function lamsfet_import_task_file_to_local_list_storage($full_path, $original_path, $list_task_id, $lamsfet_task_id) {
    $local_path = 'private/uploads/task_files/task_' . $list_task_id . '/hidden/';
    if (!file_exists($local_path)) {
        @mkdir('private/uploads/task_files/task_' . $list_task_id, DIR_READ_MODE);
        @mkdir('private/uploads/task_files/task_' . $list_task_id . '/hidden', DIR_READ_MODE);
    }
    $file_name = basename($full_path);
    $full_local_path = $local_path . $file_name;
    
    if (lamsfet_download_file($full_path, $full_local_path)) {
        return 'index.php/tasks/download_hidden_file/' . $list_task_id . '/' . encode_for_url($file_name);
    } else {
        echo ' ( FILE NOT FOUND ' . $full_path . ' FOR LaMSfET TASK ' . $lamsfet_task_id . ' [LIST ' . $list_task_id . '] ) ';
        log_message('ERROR', 'FILE NOT FOUND ' . $full_path . ' FOR LaMSfET TASK ' . $lamsfet_task_id . ' [LIST ' . $list_task_id . ']', FALSE);
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

function fix_broken_tasks_links($broken_prefix) {
    include_once(APPPATH . 'third_party/simplehtmldom/simple_html_dom.php');
    
    $CI =& get_instance();
    $CI->lang->load_all_overlays('tasks');
    $languages = $CI->lang->get_list_of_languages();
    
    $tasks = new Task();
    $tasks->get_iterated();
    foreach ($tasks as $task) {
        $text_html = str_get_html($task->text, true, true, DEFAULT_TARGET_CHARSET, false);
        $save = FALSE;
        echo 'TASK ' . $task->id . ":\n";
        foreach ($text_html->find('img, a') as $element) {
            if ($element->tag == 'a' && mb_strpos(trim($element->href), $broken_prefix) === 0) {
                echo ' BROKEN a TAG' . "\n";
                echo '  FOUND BROKEN LINK: ' . trim($element->href) . "\n";
                $new_link = ltrim(mb_substr(trim($element->href), mb_strlen($broken_prefix)), '\\/');
                echo '  REPAIR TO: ' . $new_link . "\n";
                $element->href = $new_link;
                $save = TRUE;
            } elseif ($element->tag == 'img' && mb_strpos(trim($element->src), $broken_prefix) === 0) {
                echo ' BROKEN img TAG' . "\n";
                echo '  FOUND BROKEN LINK: ' . trim($element->src) . "\n";
                $new_link = ltrim(mb_substr(trim($element->src), mb_strlen($broken_prefix)), '\\/');
                echo '  REPAIR TO: ' . $new_link . "\n";
                $element->src = $new_link;
                $save = TRUE;
            }
        }
        if ($save) {
            $task->text = $text_html->__toString();
            echo 'SAVING TASK' . "\n";
            $task->save();
        }
        echo 'TESTING OVERLAYS' . "\n";
        foreach ($languages as $idiom => $title) {
            echo '  ' . $title . "\n";
            $text = $CI->lang->get_overlay('tasks', $task->id, 'text', $idiom);
            if (!empty($text)) {
                $text_html = str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
                $save = FALSE;
                foreach ($text_html->find('img, a') as $element) {
                    if ($element->tag == 'a' && mb_strpos(trim($element->href), $broken_prefix) === 0) {
                        echo '   BROKEN a TAG' . "\n";
                        echo '     FOUND BROKEN LINK: ' . trim($element->href) . "\n";
                        $new_link = ltrim(mb_substr(trim($element->href), mb_strlen($broken_prefix)), '\\/');
                        echo '     REPAIR TO: ' . $new_link . "\n";
                        $element->href = $new_link;
                        $save = TRUE;
                    } elseif ($element->tag == 'img' && mb_strpos(trim($element->src), $broken_prefix) === 0) {
                        echo '   BROKEN img TAG' . "\n";
                        echo '    FOUND BROKEN LINK: ' . trim($element->src) . "\n";
                        $new_link = ltrim(mb_substr(trim($element->src), mb_strlen($broken_prefix)), '\\/');
                        echo '    REPAIR TO: ' . $new_link . "\n";
                        $element->src = $new_link;
                        $save = TRUE;
                    }
                }
                if ($save) {
                    $text = $text_html->__toString();
                    echo '  SAVING OVERLAY' . "\n";
                    $CI->lang->save_overlay('tasks', $task->id, 'text', $idiom, $text);
                }
            }
        }
    }
}