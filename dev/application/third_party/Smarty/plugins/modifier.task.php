<?php

include_once(APPPATH . 'third_party/geshi/geshi.php');
include_once(APPPATH . 'third_party/simplehtmldom/simple_html_dom.php');

function smarty_modifier_task($string) {
    return task_modifier_scan_text($string);
}

function task_modifier_scan_text($string) {
    $CI =& get_instance();
    $CI->config->load('geshi');
    $highlight_map = $CI->config->item('geshi_highlighting_map');
    $html = str_get_html($string, true, true, DEFAULT_TARGET_CHARSET, false);
    foreach ($html->find('pre br') as $br) {
        $br->outertext = "\n";
    }
    foreach ($html->find('pre.highlight') as $highlighted_code) {
        $lang = $highlighted_code->lang;
        if (isset($highlight_map[$lang])) {
            $content = str_replace('&nbsp;', ' ', htmlspecialchars_decode(strip_tags($highlighted_code->innertext), ENT_HTML5 | ENT_QUOTES));
            $geshi = new GeSHi($content, $highlight_map[$lang]);
            $geshi->set_header_type(GESHI_HEADER_PRE_VALID);
            $geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);
            $highlighted_code->innertext = $geshi->parse_code();
            $highlighted_code->lang = null;
        } else {
            $highlighted_code->lang = null;
            $highlighted_code->class = null;
        }
    }
    ob_start();
    echo $html;
    return ob_get_clean();
}