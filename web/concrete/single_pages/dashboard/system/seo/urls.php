<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="row">
    <div class="col-md-6">
        <form method="post" action="<?php echo $view->action('save_urls'); ?>">
            <?php echo $this->controller->token->output('save_urls'); ?>


            <div class="form-group">
                <label class="control-label" for="canonical_host"><?=t('Canonical Host and Port')?> <i class="fa fa-question-circle launch-tooltip" title="<?=t('If this site is accessible from multiple domains, it can be useful to specify a single canonical domain and port. These can usually be left blank.')?>"></i></label>
                <div class="input-group">
                    <span class="input-group-addon">http://</span>
                    <input type="text" class="form-control" placeholder="domain.com" value="<?=$host?>" name="canonical_host">
                    <span class="input-group-addon">:</span>
                    <input type="text" class="form-control" placeholder="80" value="<?=$port?>" name="canonical_port">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label"><?=t('Pretty URLs')?> <i class="fa fa-question-circle launch-tooltip" title="<?=t('Removes index.php from your site\'s URLs.')?>"></i></label>
                <div class="checkbox">
                <label>
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
