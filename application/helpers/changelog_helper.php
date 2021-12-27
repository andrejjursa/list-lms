<?php

/**
 * Changelog helper functions.
 *
 * @package LIST_Helpers
 * @author  Andrej Jursa
 */

function changelog_to_html($text): string
{
    $text_lines = explode("\n", htmlspecialchars($text));
    $output = '';
    
    foreach ($text_lines as $line) {
        $matches = [];
        if (preg_match('/^(?P<spaces>\s*)(?P<text>.*)$/', $line, $matches)) {
            if ($output !== '') {
                $output .= '<br />';
            }
            $output .= str_repeat('&nbsp;', mb_strlen($matches['spaces'])) . rtrim($matches['text']);
        }
    }
    
    return $output;
}