<?php /* Smarty version Smarty-3.1.12, created on 2013-03-04 12:35:31
         compiled from "application\views\frontend\students\logout.tpl" */ ?>
<?php /*%%SmartyHeaderCode:696751348703433b99-26587020%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd26e3b5f710f8fd5f910fde7404f75270818f7c0' => 
    array (
      0 => 'application\\views\\frontend\\students\\logout.tpl',
      1 => 1362396914,
      2 => 'file',
    ),
    'ddc67b91e8ebcb0b468a0a19d1f48e5f3f8b178d' => 
    array (
      0 => 'application\\views\\layouts\\frontend.tpl',
      1 => 1362328801,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '696751348703433b99-26587020',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'list_internal_css_files' => 0,
    'file' => 0,
    'list_internal_js_files' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5134870371b6b6_36258051',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5134870371b6b6_36258051')) {function content_5134870371b6b6_36258051($_smarty_tpl) {?><?php if (!is_callable('smarty_function_translate')) include 'C:\\xampp\\htdocs\\list-svn\\dev\\application\\third_party\\Smarty\\plugins\\function.translate.php';
?><!DOCTYPE html>
<html>
    <head>
        <meta content="text/html" charset="utf-8" />
        <title></title>
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
        
    <p><?php echo smarty_function_translate(array('line'=>'students_logout_logout_message'),$_smarty_tpl);?>
</p>

    </body>
</html><?php }} ?>