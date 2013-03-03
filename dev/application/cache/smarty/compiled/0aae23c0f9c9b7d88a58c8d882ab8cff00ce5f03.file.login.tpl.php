<?php /* Smarty version Smarty-3.1.12, created on 2013-03-03 18:29:21
         compiled from "application\views\frontend\students\login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:272885133512b14fc26-25716192%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0aae23c0f9c9b7d88a58c8d882ab8cff00ce5f03' => 
    array (
      0 => 'application\\views\\frontend\\students\\login.tpl',
      1 => 1362331759,
      2 => 'file',
    ),
    'ddc67b91e8ebcb0b468a0a19d1f48e5f3f8b178d' => 
    array (
      0 => 'application\\views\\layouts\\frontend.tpl',
      1 => 1362328801,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '272885133512b14fc26-25716192',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5133512b19c913_58728208',
  'variables' => 
  array (
    'list_internal_css_files' => 0,
    'file' => 0,
    'list_internal_js_files' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5133512b19c913_58728208')) {function content_5133512b19c913_58728208($_smarty_tpl) {?><?php if (!is_callable('smarty_function_translate')) include 'C:\\xampp\\htdocs\\list-svn\\dev\\application\\third_party\\Smarty\\plugins\\function.translate.php';
if (!is_callable('smarty_function_internal_url')) include 'C:\\xampp\\htdocs\\list-svn\\dev\\application\\third_party\\Smarty\\plugins\\function.internal_url.php';
if (!is_callable('smarty_function_form_error')) include 'C:\\xampp\\htdocs\\list-svn\\dev\\application\\third_party\\Smarty\\plugins\\function.form_error.php';
?><!DOCTYPE html>
<html>
    <head>
        <meta content="text/html" charset="utf-8" />
        <title><?php echo smarty_function_translate(array('line'=>'students_login_welcome_text'),$_smarty_tpl);?>
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
        
    <div id="loginbox">
        <div class="boxborder">
            <h1><?php echo smarty_function_translate(array('line'=>'students_login_welcome_text'),$_smarty_tpl);?>
</h1>
            <form action="<?php echo smarty_function_internal_url(array('url'=>'students/do_login'),$_smarty_tpl);?>
" method="post">
                <p><label><?php echo smarty_function_translate(array('line'=>'students_login_label_email'),$_smarty_tpl);?>
:</label></p>
                <p><input type="text" name="student[email]" value="<?php echo htmlspecialchars($_POST['student']['email'], ENT_QUOTES, 'UTF-8', true);?>
" /></p>
                <?php echo smarty_function_form_error(array('field'=>'student[email]','left_delimiter'=>'<p class="error">','right_delimiter'=>'</p>'),$_smarty_tpl);?>

                <p><label><?php echo smarty_function_translate(array('line'=>'students_login_label_password'),$_smarty_tpl);?>
:</label></p>
                <p><input type="password" name="student[password]" value="<?php echo htmlspecialchars($_POST['student']['password'], ENT_QUOTES, 'UTF-8', true);?>
" /></p>
                <?php echo smarty_function_form_error(array('field'=>'student[password]','left_delimiter'=>'<p class="error">','right_delimiter'=>'</p>'),$_smarty_tpl);?>

                <p><input type="submit" name="button_submit" value="<?php echo smarty_function_translate(array('line'=>'students_login_submit_button_label'),$_smarty_tpl);?>
" /></p>
            </form>
        </div>
    </div>

    </body>
</html><?php }} ?>