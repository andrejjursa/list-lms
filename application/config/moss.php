<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * MOSS USER ID:
 */

$config['moss_user_id'] = '';

/*
 * MOSS SERVER ADDRESS:
 */

$config['moss_server'] = 'moss.stanford.edu';

/*
 * MOSS SERVER PORT:
 */

$config['moss_port'] = 7690;

/*
 * MOSS Languages used by LIST:
 * WARNING: These language names must be supported by MOSS! Otherwise they will not be used.
 */

$config['moss_langs_for_list'] = array('java' => 'Java', 'python' => 'Python', 'cc' => 'C++', 'c' => 'C', 'csharp' => 'C#', 'haskell' => 'Haskell', 'prolog' => 'Prolog');

/*
 * MOSS Languages files extensions:
 */

$config['moss_langs_file_extensions'] = array(
    'java' => array('java', 'jsp'),
    'python' => array('py'),
    'cc' => array('cc', 'cpp', 'h'),
    'c' => array('c', 'h'),
    'csharp' => array('cs'),
    'haskell' => array('hs'),
    'prolog' => array('pl'),
);