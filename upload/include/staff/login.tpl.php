<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
$info = ($_POST && $errors)?Format::htmlchars($_POST):array();


if ($thisstaff && $thisstaff->is2FAPending())
    $msg = "2FA Pending";

?>
<div  id="brickwall"></div>
<div class="max-w-lg min-h-50 grid gap-1" id="loginBox">
    <div id="blur">
        <div id="background"></div>
    </div>
    <h1 class="flex items-center justify-center" id="logo"><a href="index.php">
        <span class="" class="valign-helper"></span>
        <img  src="logo.php?login" alt="osTicket :: <?php echo __('Staff Control Panel');?>" />
    </a></h1>
    <h3 class="flex items-center justify-center" id="login-message"><?php echo Format::htmlchars($msg); ?></h3>
    <div class="banner"><small><?php echo ($content) ? Format::display($content->getLocalBody()) : ''; ?></small></div>
    <div id="loading" style="display:none;" class="dialog">
        <h1><i class="icon-spinner icon-spin icon-large"></i>
        <?php echo __('Verifying');?></h1>
    </div>
    <form class="max-w-lg min-g-100 flex items-center justify-center" action="login.php" method="post" id="login" onsubmit="attemptLoginAjax(event)">
        <?php csrf_token();
        if ($thisstaff
                &&  $thisstaff->is2FAPending()
                && ($bk=$thisstaff->get2FABackend())
                && ($form=$bk->getInputForm($_POST))) {
            // Render 2FA input form
            include STAFFINC_DIR . 'templates/dynamic-form-simple.tmpl.php';
            ?>
            <fieldset > <!--class="grid grid-cols-3"-->
            <input type="hidden" name="do" value="2fa">
            <button class="flex flex-col w-200" type="submit"
                name="submit"><i class="icon-signin"></i>
                <?php echo __('Verify'); ?>
            </button>
             </fieldset>
        <?php
        } else { ?>
            <input type="hidden" name="do" value="scplogin">
            <fieldset>
            <input class="font-bold tracking-wide" type="text" name="userid" id="name" value="<?php
                echo $info['userid'] ?? null; ?>" placeholder="<?php echo __('Correo o Usuario'); ?>"
                autofocus autocorrect="off" autocapitalize="off">
            <input type="password" name="passwd" id="pass" maxlength="128" placeholder="<?php echo __('Contraseña'); ?>" autocorrect="off" autocapitalize="off">
                <h3 style="display:inline"><a id="reset-link" class="<?php
                    if (!$show_reset || !$cfg->allowPasswordReset()) echo 'hidden';
                    ?>" href="pwreset.php"><?php echo __('Forgot My Password'); ?></a></h3>
                <button class="flex justify-center w-50 p-1 m-1 border-gray-400 " type="submit"
                    name="submit"><i class=""></i>
                    <?php echo __('Iniciar Sesión'); ?>
                </button>
            </fieldset>
        <?php
        } ?>
    </form>
<?php
if (($bks=StaffAuthenticationBackend::getExternal())) { ?>
<div class="or">
    <hr/>
</div><?php
    foreach ($bks as $bk) { ?>
<div class="external-auth"><?php $bk->renderExternalLink(); ?></div><br/><?php
    }
} ?>

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

    function attemptLoginAjax(e) {
        $('#loading').show();
        var objectifyForm = function(formArray) { //serialize data function
            var returnArray = {};
            for (var i = 0; i < formArray.length; i++) {
                returnArray[formArray[i]['name']] = formArray[i]['value'];
            }
            return returnArray;
        };
        if ($.fn.effect) {
            // For some reason, JQuery-UI shake does not considere an element's
            // padding when shaking. Looks like it might be fixed in 1.12.
            // Thanks, https://stackoverflow.com/a/22302374
            var oldEffect = $.fn.effect;
            $.fn.effect = function (effectName) {
                if (effectName === "shake") {
                    $('#loading').hide();
                    var old = $.effects.createWrapper;
                    $.effects.createWrapper = function (element) {
                        var result;
                        var oldCSS = $.fn.css;

                        $.fn.css = function (size) {
                            var _element = this;
                            var hasOwn = Object.prototype.hasOwnProperty;
                            return _element === element && hasOwn.call(size, "width") && hasOwn.call(size, "height") && _element || oldCSS.apply(this, arguments);
                        };

                        result = old.apply(this, arguments);

                        $.fn.css = oldCSS;
                        return result;
                    };
                }
                return oldEffect.apply(this, arguments);
            };
        }
        var form = $(e.target),
            data = objectifyForm(form.serializeArray())
        data.ajax = 1;
        $('button[type=submit]', form).attr('disabled', 'disabled');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: data,
            cache: false,
            success: function(json) {
                 $('button[type=submit]', form).removeAttr('disabled');
                if (!typeof(json) === 'object' || !json.status)
                    return;
                switch (json.status) {
                case 401:
                    if (json && json.redirect)
                        document.location.href = json.redirect;
                    if (json && json.message)
                        $('#login-message').text(json.message)
                    if (json && json.show_reset)
                        $('#reset-link').show()
                    if ($.fn.effect) {
                        $('#loginBox').effect('shake')
                    }
                    // Clear the password field
                    $('#pass').val('').focus();
                    break
                case 302:
                    if (json && json.redirect)
                        document.location.href = json.redirect;
                    break
                }
            },
        });
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        return false;
    }
    </script>
    <!--[if IE]>
    <style>
        #loginBox:after { background-color: white !important; }
    </style>
    <![endif]-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.13.2.custom.min.js?3ff35ad"></script>
</body>
</html>
