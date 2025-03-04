<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();
?>

<div id="brickwall"></div>
<div class="mt-50" id="loginBox">
    <div id="blur">
        <div id="background"></div>
    </div>
    <h1 class="flex items-center justify-center mt-6" id="logo"><a href="index.php">
        <span class="flex-shrink-0 mr-4"></span>
        <img src="logo.php?login" alt="osTicket :: <?php echo __('Agent Password Reset');?>" />
    </a></h1>
    <h3><?php echo __('El correo que confirmación ha sido enviado'); ?></h3> 
    <h3 style="color:black;"><em><?php echo __(
    'Gracias por utilizar el servicio de tickets.'
    ); ?>
    </em></h3>

    <form action="index.php" method="get">
        <input class="submit" type="submit" name="submit" value="Login"/>
    </form>

    <div id="company">
        <div class="content">
            <?php echo __('Copyright'); ?> &copy; <?php echo Format::htmlchars($ost->company) ?: date('Y'); ?>
        </div>
    </div>
</div>
<div id="poweredBy"><?php echo __('Powered by'); ?>
    <a href="http://www.osticket.com" target="_blank">
        <img alt="osTicket" src="images/osticket-grey.png" class="osticket-logo">
    </a>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (undefined === window.getComputedStyle(document.documentElement).backgroundBlendMode) {
            document.getElementById('loginBox').style.backgroundColor = 'white';
        }
    });
    </script>
</body>
</html>
