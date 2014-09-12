<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="row">
    <div class="col-md-6">
        <form method="post" action="<?php echo $view->action('update_rewriting'); ?>">
            <?php echo $this->controller->token->output('update_rewriting'); ?>
            <div class="form-group">
                <div class="checkbox">
                    <label for="URL_REWRITING">
                        <?php echo $fh->checkbox('URL_REWRITING', 1, $intRewriting) ?>

                        <?php echo t('Enable Pretty URLs'); ?>
                    </label>
                </div>

                <?php
                // Show the placeholder textarea with the mod_rewrite rules if pretty urls enabled
                // NOTE: The contents of the textarea are not saved
                if(Config::get('concrete.seo.url_rewriting')){
                    echo '
                <div class="clearfix"><label>' . t('Code for your .htaccess file') . '</label>
                <textarea style="width:98%; max-width:98%; min-width:98%; height:150px; min-height:150px; max-height:300px;" onclick="this.select()">' . $strRules . '</textarea></div>';
                }
                ?>
            </div>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <?php echo $interface->submit(t('Save'), 'url-form', 'right', 'btn-primary'); ?>
                </div>
            </div>
        </form>
    </div>
</div>
