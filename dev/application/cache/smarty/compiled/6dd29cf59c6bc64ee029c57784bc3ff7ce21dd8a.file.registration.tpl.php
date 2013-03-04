<?php /* Smarty version Smarty-3.1.12, created on 2013-03-04 19:47:15
         compiled from "application\views\frontend\students\registration.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8516513487c23869c2-44776357%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6dd29cf59c6bc64ee029c57784bc3ff7ce21dd8a' => 
    array (
      0 => 'application\\views\\frontend\\students\\registration.tpl',
      1 => 1362422719,
      2 => 'file',
    ),
    'ddc67b91e8ebcb0b468a0a19d1f48e5f3f8b178d' => 
    array (
      0 => 'application\\views\\layouts\\frontend.tpl',
      1 => 1362328801,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8516513487c23869c2-44776357',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_513487c2400281_81694954',
  'variables' => 
  array (
    'list_internal_css_files' => 0,
    'file' => 0,
    'list_internal_js_files' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_513487c2400281_81694954')) {function content_513487c2400281_81694954($_smarty_tpl) {?><?php if (!is_callable('smarty_function_translate')) include 'C:\\xampp\\htdocs\\list-svn\\dev\\application\\third_party\\Smarty\\plugins\\function.translate.php';
if (!is_callable('smarty_function_internal_url')) include 'C:\\xampp\\htdocs\\list-svn\\dev\\application\\third_party\\Smarty\\plugins\\function.internal_url.php';
if (!is_callable('smarty_function_form_error')) include 'C:\\xampp\\htdocs\\list-svn\\dev\\application\\third_party\\Smarty\\plugins\\function.form_error.php';
?><!DOCTYPE html>
<html>
    <head>
        <meta content="text/html" charset="utf-8" />
        <title><?php echo smarty_function_translate(array('line'=>'students_registration_welcome_text'),$_smarty_tpl);?>
</title>
        <?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['file']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list_internal_css_files']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
$_smarty_tpl->tpl_vars['file']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['file']->value['html'];?>
<?php } ?>
        <?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['file']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list_internal_js_files']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
$_smarty_tpl->tpl_vars['file']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['file']->value['html'];?>
<?php } ?>
    </head>
    <body>
        
    <h1><?php echo smarty_function_translate(array('line'=>'students_registration_welcome_text'),$_smarty_tpl);?>
</h1>
    <?php if ($_smarty_tpl->tpl_vars['save_error']->value){?>
    <div class="error"><?php echo smarty_function_translate(array('line'=>'students_registration_error_cant_save_student'),$_smarty_tpl);?>
</div>
    <?php }?>
    <form action="<?php echo smarty_function_internal_url(array('url'=>'students/do_registration'),$_smarty_tpl);?>
" method="post">
        <div class="field_wrap">
            <label><?php echo smarty_function_translate(array('line'=>'students_registration_label_fullname'),$_smarty_tpl);?>
:</label><br />
            <input type="text" name="student[fullname]" value="<?php echo htmlspecialchars($_POST['student']['fullname'], ENT_QUOTES, 'UTF-8', true);?>
" maxlength="255" />
            <?php echo smarty_function_form_error(array('field'=>'student[fullname]','left_delimiter'=>'<div class="error">','right_delimiter'=>'</div>'),$_smarty_tpl);?>

        </div>
        <div class="field_wrap">
            <label><?php echo smarty_function_translate(array('line'=>'students_registration_label_email'),$_smarty_tpl);?>
:</label><br />
            <input type="text" name="student[email]" value="<?php echo htmlspecialchars($_POST['student']['email'], ENT_QUOTES, 'UTF-8', true);?>
" maxlength="255" />
            <?php echo smarty_function_form_error(array('field'=>'student[email]','left_delimiter'=>'<div class="error">','right_delimiter'=>'</div>'),$_smarty_tpl);?>

        </div>
        <div class="field_wrap">
            <label><?php echo smarty_function_translate(array('line'=>'students_registration_label_password'),$_smarty_tpl);?>
:</label><br />
            <input type="text" name="student[password]" value="<?php echo htmlspecialchars($_POST['student']['password'], ENT_QUOTES, 'UTF-8', true);?>
" maxlength="255" />
            <?php echo smarty_function_form_error(array('field'=>'student[password]','left_delimiter'=>'<div class="error">','right_delimiter'=>'</div>'),$_smarty_tpl);?>

        </div>
        <div class="field_wrap">
            <label><?php echo smarty_function_translate(array('line'=>'students_registration_label_password_verification'),$_smarty_tpl);?>
:</label><br />
            <input type="text" name="student[password_verification]" value="<?php echo htmlspecialchars($_POST['student']['password_verification'], ENT_QUOTES, 'UTF-8', true);?>
" maxlength="255" />
            <?php echo smarty_function_form_error(array('field'=>'student[password_verification]','left_delimiter'=>'<div class="error">','right_delimiter'=>'</div>'),$_smarty_tpl);?>

        </div>
        <div class="buttons_wrap">
            <input type="submit" name="submit_button" value="<?php echo smarty_function_translate(array('line'=>'students_registration_submit_button_text'),$_smarty_tpl);?>
" />
        </div>
    </form>

    </body>
</html><?php }} ?>