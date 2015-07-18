<?php require_once(BASEPATH . 'core/Input.php'); ?>
<?php $local_input = new CI_Input(); ?>
<?php if ($local_input->is_cli_request()): ?>

Severity:    <?php echo strip_tags($severity) . "\n"; ?>
Filename:    <?php echo strip_tags($filepath) . "\n"; ?>
Line Number: <?php echo strip_tags($line) . "\n"; ?>
Message:
<?php echo strip_tags($message); ?>

<?php else: ?>
<div style="border:1px solid #990000;padding:20px;margin:5px 0 5px 0;border-radius: 5px;box-shadow:5px 5px #990000;">

    <h4 style="color:darkred;font-size:20px;margin:0;padding:0;">A PHP Error was encountered</h4>
    
    <table style="border-collapse: collapse;margin:5px 0 0 0;padding:0;">
        <tbody>
            <tr>
                <td style="font-weight: bold;color:red;width:100px;vertical-align:top;margin:0;padding:3px;">Severity:</td>
                <td style="vertical-align:top;margin:0;padding:3px;"><?php echo $severity; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;color:red;width:100px;vertical-align:top;margin:0;padding:3px;">Message:</td>
                <td style="vertical-align:top;margin:0;padding:3px;"><?php echo $message; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;color:red;width:100px;vertical-align:top;margin:0;padding:3px;">Filename:</td>
                <td style="vertical-align:top;margin:0;padding:3px;"><?php echo $filepath; ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold;color:red;width:100px;vertical-align:top;margin:0;padding:3px;">Line Number:</td>
                <td style="vertical-align:top;margin:0;padding:3px;"><?php echo $line; ?></td>
            </tr>
        </tbody>
    </table>

</div>
<?php endif; ?>