<?php

$text_to_normalize = 'áéíóúýäôìøžàŸïòñ¾åšœèæ';
$text_to_normalize .= mb_strtoupper($text_to_normalize);

echo '<pre>';

echo 'Original text: ' . $text_to_normalize . PHP_EOL;

if (class_exists('Normalizer', FALSE)) {
    $normalized_form_c = Normalizer::normalize($text_to_normalize, Normalizer::FORM_C);
    $normalized_form_d = Normalizer::normalize($text_to_normalize, Normalizer::FORM_D);
    $normalized_form_kc = Normalizer::normalize($text_to_normalize, Normalizer::FORM_KC);
    $normalized_form_kd = Normalizer::normalize($text_to_normalize, Normalizer::FORM_KD);
    
    echo 'Normalized, FORM_C: ' . $normalized_form_c . PHP_EOL;
    echo 'Normalized, FORM_D: ' . $normalized_form_d . PHP_EOL;
    echo 'Normalized, FORM_KC: ' . $normalized_form_kc . PHP_EOL;
    echo 'Normalized, FORM_KD: ' . $normalized_form_kd . PHP_EOL;
} else {
    echo '<span style="color: red;">Normalizer not found ...</span>' . PHP_EOL;
}

echo '</pre>';