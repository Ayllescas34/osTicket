<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email = Format::input($_POST['luser'] ?: $_GET['e']);
$passwd = Format::input($_POST['lpasswd'] ?: $_GET['t']);

$content = Page::lookupByType('banner-client');

if ($content) {
    list($title, $body) = $ost->replaceTemplateVariables(
        array($content->getLocalName(), $content->getLocalBody()));
} else {
    $title = __('Iniciar Sesión');
    $body = __('To better serve you, we encourage our clients to register for an account and verify the email address we have on record.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Format::display($title); ?></title>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/login.css" media="screen"/> <!-- llamado a css -->
</head>
<body>
    <h1 class="tittle"><?php echo Format::display($title); ?></h1>
    <p class="text"><?php echo Format::display($body); ?></p>
    <form  class="form-login"action="login.php" method="post" id="clientLogin">
        <?php csrf_token(); ?>
        <div class="login-form">
            <div class="login-box">
                <strong><?php echo Format::htmlchars($errors['login']); ?></strong>
                <div>
                    <input id="username" placeholder="<?php echo __('Correo o Usuario'); ?>" type="text" name="luser" size="30" value="<?php echo $email; ?>" class="nowarn">
                </div>
                <div>
                    <input id="passwd" placeholder="<?php echo __('Clave'); ?>" type="password" name="lpasswd" size="30" maxlength="128" value="<?php echo $passwd; ?>" class="nowarn">
                </div>
                <p>
                    <input class="btnSummit" type="submit" value="<?php echo __('Iniciar Sesión'); ?>">
                    <?php if ($suggest_pwreset) { ?>
                        <a style="padding-top:4px;display:inline-block;" href="pwreset.php"><?php echo __('Forgot My Password'); ?></a>
                    <?php } ?>
                </p>
            </div>
            <div class="box" >
                <?php
                $ext_bks = array();
                foreach (UserAuthenticationBackend::allRegistered() as $bk)
                    if ($bk instanceof ExternalAuthentication)
                        $ext_bks[] = $bk;

                if (count($ext_bks)) {
                    foreach ($ext_bks as $bk) { ?>
                        <div class="external-auth"><?php $bk->renderExternalLink(); ?></div>
                    <?php
                    }
                }
                if ($cfg && $cfg->isClientRegistrationEnabled()) {
                    if (count($ext_bks)) echo '<hr style="width:70%"/>';
                    ?>
                    <div class="complement">
                        <?php echo __('Aún no te registras?'); ?> <a  class="boton" href="account.php?do=create"><?php echo __('Create an account'); ?></a>
                    </div>
                <?php } ?>
                <div class="complement">
                    <b><?php echo __("I'm an agent"); ?></b> —
                    <a href="<?php echo ROOT_PATH; ?>scp/"><?php echo __('sign in here'); ?></a>
                </div>
            </div>
        </div>
    </form>
    <br>
    <p>
        <?php
        if ($cfg->getClientRegistrationMode() != 'disabled' || !$cfg->isClientLoginRequired()) {
            echo sprintf(__('If this is your first time contacting us or you\'ve lost the ticket number, please %s open a new ticket %s'),
                '<a href="open.php">', '</a>');
        } ?>
    </p>
</body>
</html>
