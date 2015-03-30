<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<form method="post" action="<?php echo $view->action('save_urls'); ?>">
    <?php echo $this->controller->token->output('save_urls'); ?>


    <div class="row">
        <div class="col-sm-8">
            <div class="form-group">
                <label class="control-label" for="canonical_host"><?=t('Canonical Host and Port')?> <i class="fa fa-question-circle launch-tooltip" title="<?=t('If this site is accessible from multiple domains, it can be useful to specify a single canonical domain and port. These can usually be left blank.')?>"></i></label>
                <div class="input-group">
                    <span class="input-group-addon" data-field="url-scheme">http://</span>
                    <input type="text" class="form-control" placeholder="domain.com" style="width: 100%" value="<?=$host?>" name="canonical_host">
                    <span class="input-group-addon">:</span>
                    <input type="text" class="form-control" value="<?=$port?>" placeholder="80" name="canonical_port">
                    <span class="input-group-addon">
                        <?php echo $fh->checkbox('force_ssl', 1, $force_ssl) ?>
                        <?php echo t('SSL'); ?>
                    </span>
                </div>
            </div>
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

    <h4><?=t('Advanced')?></h4>

    <div class="alert alert-warning"><?=t('Ensure that your site is viewable at the host/ssl/port combination above before you check the checkbox below. If not, doing so may render your site unviewable until you can manually undo this change.')?></div>

    <div class="form-group">
        <label class="control-label" for="force_ssl"><?=t('URL Redirection')?> <i class="fa fa-question-circle launch-tooltip" title="<?=t('If checked, this site will only be available at the host, port and SSL combination chosen above.')?>"></i></label>
        <div class="checkbox">
            <label>
                <?php echo $fh->checkbox('redirect_to_canonical_host', 1, $redirect_to_canonical_host) ?>
                <?php echo t('Only render at canonical host and port.'); ?>
            </label>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Save'), 'url-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(function() {
        $('input[name=force_ssl]').on('change', function() {
           if ($(this).is(':checked')) {
               $('span[data-field=url-scheme]').html('https://');
           } else {
               $('span[data-field=url-scheme]').html('http://');
           }
        }).trigger("change");
    });
</script>
