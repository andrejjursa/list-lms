<?php

$lang['tests_python_type_name'] = 'Python';
$lang['python_tests_subtype_unit_test_name'] = 'Unit test';
$lang['python_tests_config_form_unit_test_zip_file'] = 'ZIP súbor so zdrojovým kódom';
$lang['python_tests_config_form_unit_test_zip_file_hint'] = 'Môžete nahrať ZIP súbor, kde súbor s testom bude v koreňovom adresári. Voliteľne môžete nahrať súbor PY, ktorý sa zoZIPuje po nahraní. Maximálna veľkosť súboru: 2MiB.';
$lang['python_tests_config_form_unit_test_class_to_run'] = 'Názov súboru s testom';
$lang['python_tests_config_form_unit_test_class_to_run_hint'] = 'Z bezpečnostných dôvodov súbor s testom, ktorý sa spustí, sa musí volať Test<strong>&lt;váš názov súboru&gt;</strong>.py.<br /><strong>Príklad:</strong> Test<strong>Stvorec</strong>.py';
$lang['python_tests_config_form_unit_test_output_maximum_lines'] = 'Maximum riadkov vo výstupe';
$lang['python_tests_config_form_unit_test_output_maximum_lines_hint'] = 'Nastav na maximum riadkov vo výstupe unit testu alebo nastav na <strong>0</strong> na vypnutie tejto vlastnosti.';
$lang['python_tests_config_validation_unit_test_class_to_run'] = 'názov súboru s testom';
$lang['python_tests_config_validation_unit_test_output_maximum_lines'] = 'maximum riadkov vo výstupe';
$lang['python_tests_run_error_unit_test_class_not_set'] = 'Názov súboru s testom nebol zadaný.';
$lang['python_tests_subtype_io_test_name'] = 'Vstupno/výstupný test';
$lang['python_tests_config_form_io_test_input_file'] = 'Vstupný textový súbor';
$lang['python_tests_config_form_io_test_input_file_hint'] = 'Toto je vstupný textový súbor pre test. Dajte pozor aby mal správne formátovanie znakov.';
$lang['python_tests_config_form_io_test_judge_type'] = 'Typ sudcu';
$lang['python_tests_config_form_io_test_judge_type_diff'] = 'Štandardný DIFF program spolu s textovým súborom s cieľovým výsledkom.';
$lang['python_tests_config_form_io_test_judge_type_script'] = 'Vlastný skript v python-e, ktorý dostane na svoj štandardný vstup výstup s testovaného programu.';
$lang['python_tests_config_form_io_test_target_file'] = 'Cieľový textový súbor';
$lang['python_tests_config_form_io_test_target_file_hint'] = 'Ak je typ sudcu nastavený na diff, prosím vložte textový súbor cieľového riešenia.';
$lang['python_tests_config_form_io_test_judge_source'] = 'Zdrojový kód sudcu';
$lang['python_tests_config_form_io_test_judge_source_hint'] = 'Ak je typ sudcu nastavený na vlastný skript, prosím pošlite svoj zdrojový kód pre sudcov program. Názov súboru bude zmenený na <strong>test_judge.py</strong>, preto je dobré s tým počítať prípadne vlastný súbor so zdrojovým kódom takto pomenovať.';