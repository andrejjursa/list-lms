<?php

$lang['tests_java_type_name'] = 'Java';
$lang['java_tests_subtype_unit_test_name'] = 'Unit test';
$lang['java_tests_config_form_unit_test_zip_file'] = 'ZIP súbor so zdrojovým kódom';
$lang['java_tests_config_form_unit_test_zip_file_hint'] = 'Môžete nahrať ZIP súbor, kde súbor s testom bude v koreňovom adresári. Voliteľne môžete nahrať súbor JAVA, ktorý sa zoZIPuje po nahraní. Maximálna veľkosť súboru: 2MiB.';
$lang['java_tests_config_form_unit_test_class_to_run'] = 'Názov súboru s testom';
$lang['java_tests_config_form_unit_test_class_to_run_hint'] = 'Z bezpečnostných dôvodov súbor s testom, ktorý sa spustí, sa musí volať Test<strong>&lt;váš názov súboru&gt;</strong>.java.<br /><strong>Príklad:</strong> Test<strong>Stvorec</strong>.java';
$lang['java_tests_config_form_unit_test_output_maximum_lines'] = 'Maximum riadkov vo výstupe';
$lang['java_tests_config_form_unit_test_output_maximum_lines_hint'] = 'Nastav na maximum riadkov vo výstupe unit testu alebo nastav na <strong>0</strong> na vypnutie tejto vlastnosti.';
$lang['java_tests_config_validation_unit_test_class_to_run'] = 'názov súboru s testom';
$lang['java_tests_config_validation_unit_test_output_maximum_lines'] = 'maximum riadkov vo výstupe';
$lang['java_tests_run_error_unit_test_class_not_set'] = 'Názov súboru s testom nebol zadaný.';
$lang['java_tests_config_form_scoring_class_label'] = 'Stiahnuť hodnotiaci balíček';
$lang['java_tests_config_form_scoring_class_zip'] = 'Kliknite sem na stiahnutie hodnotiaceho balíčka.';
$lang['java_tests_config_form_scoring_class_zip_hint'] = 'Rozbalte tento balíček do adresára, v ktorom máte svoju triedu testu jUnit. Použite <strong>import LISTTestScoring.LISTTestScoring;</strong> na importovanie tejto triedy balíčka do Vášho kódu. Potom vytvorte <strong>private static LISTTestScoring</strong> premennú vo Vašej triede jUnit. Na jej inštancovanie vytvorte <strong>public static void</strong> metódu bez argumentov, anotujte ju pomocou <strong>@BeforeClass</strong> a tu inštancujte triedu balíčka. Neinštancujte túto triedu inde!';