<?php
defined('C5_EXECUTE') or die("Access Denied.");

?>

<form method="post" id="proxy-form"
	action="<?php echo $view->action('update_proxy'); ?>">
    <?php echo $this->controller->token->output('update_proxy'); ?>
    <div class="row">
        <div class="col-md-4">
            <fieldset>
                <div class="form-group">
                    <?=$form->label('http_proxy_host', t('Proxy Host'));?>
                    <div class="input">
                        <?=$form->text('http_proxy_host', $http_proxy_host)?>
                    </div>
                </div>
                <div class="form-group">
                    <?=$form->label('http_proxy_port', t('Proxy Port'));?>
                    <div class="input">
                        <?=$form->text('http_proxy_port', $http_proxy_port)?>
                    </div>
                </div>
                <div class="form-group">
                    <?=$form->label('http_proxy_user', t('Proxy User'));?>
                    <div class="input">
                        <?=$form->text('http_proxy_user', $http_proxy_user)?>
                    </div>
                </div>
                <div class="form-group">
                    <?=$form->label('http_proxy_pwd', t('Proxy Password'));?>
                    <div class="input">
                        <?=$form->text('http_proxy_pwd', $http_proxy_pwd)?>
                    </div>
                </div>
            </fieldset>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <?php echo $interface->submit(t('Save'), 'proxy-form', 'right', 'btn-primary'); ?>
                </div>
            </div>
        </div>
    </div>
</form>
