<?php require_once(BASEPATH . 'core/Input.php');
/**
 * @var string $heading
 * @var string $message
 */
?>
<?php $local_input = new CI_Input(); ?>
<?php if ($local_input->is_cli_request()): ?>

<?php echo strip_tags($heading) . "\n"; ?>
--------------------
<?php echo strip_tags($message); ?>

<?php else: ?>
<?php if (!$local_input->is_ajax_request()): ?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Error</title>
<meta charset="utf-8" />
<?php endif; ?>
<style type="text/css">

#container::selection{ background-color: #E13300; color: white; }
#container::moz-selection{ background-color: #E13300; color: white; }
#container::webkit-selection{ background-color: #E13300; color: white; }

<?php if (!$local_input->is_ajax_request()): ?>
body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}
<?php endif; ?>

#container a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

#container h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

#container code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
    border-radius: 5px;
	-webkit-box-shadow: 0 0 8px 8px #D0D0D0;
    box-shadow: 0 0 8px 8px #D0D0D0;
}

#container p {
	margin: 12px 15px 12px 15px;
}
</style>
<?php if (!$local_input->is_ajax_request()): ?>
</head>
<body>
<?php endif; ?>
	<div id="container">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
	</div>
<?php if (!$local_input->is_ajax_request()): ?>
</body>
</html>
<?php endif; ?>
<?php endif; ?>