<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<form method="post" action="<?php echo $view->action('save_urls'); ?>">
    <?php echo $this->controller->token->output('save_urls'); ?>

    <fieldset>
        <legend><?= t('Pretty URLs') ?></legend>
        <div class="checkbox">
            <label>
                <?php echo $fh->checkbox('URL_REWRITING', 1, $urlRewriting) ?>
                <?php echo t('Remove index.php from URLs'); ?>
            </label>
        </div>
        <?php if (isset($configuration_action)) { ?>
            <div class="form-group">
                <label class="control-label"><?=$configuration_action?></label>
                <textarea rows="8" class="form-control" onclick="this.select()"><?=h($configuration_code)?></textarea>
            </div>
        <? } ?>
    </fieldset>

    <fieldset>
        <legend><?= t('Canonical URLs') ?></legend>
        <div class="form-group">
            <label class="control-label" for="canonical_url"><?= t('Canonical URL') ?></label>
            <input type="text" class="form-control" placeholder="http://domain.com" value="<?= $canonical_url ?>"
                   name="canonical_url">
        </div>

        <div class="form-group">
            <label class="control-label" for="canonical_ssl_url"><?= t('SSL URL') ?></label>
            <input type="text" class="form-control" placeholder="https://domain.com" value="<?= $canonical_ssl_url ?>"
                   name="canonical_ssl_url">
        </div>
        <div class="form-group">
            <label class="control-label" for="redirect_to_canonical_url"><?= t('URL Redirection') ?> <i
                    class="fa fa-question-circle launch-tooltip"
                    title="<?= t('If checked, this site will only be available at the host, port and SSL combination chosen above.') ?>"></i></label>

            <div class="checkbox">
                <label>
                    <?php echo $fh->checkbox('redirect_to_canonical_url', 1, $redirect_to_canonical_url) ?>
                    <?php echo t('Only render at canonical URLs.'); ?>
                </label>
            </div>
        </div>
    </fieldset>



    <div class="alert alert-warning">
        <?= t('Ensure that your site is viewable at the URL(s) above before you check the checkbox below.
        If not, doing so may render your site unviewable until you can manually undo this change.') ?>
    </div>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Save'), 'url-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(function () {

        var steps = [{
            content: '<p><span class="h5"><?=t('Pretty URLs')?></span><br/><?=t('Check this checkbox to remove index.php from your URLs. You will be given code to place in a file named .htaccess in your web root. Concrete5 will try and place this code in the file for you.')?></p>',
            highlightTarget: false,
            nextButton: true,
            target: $('input[name=URL_REWRITING]'),
            my: 'bottom left',
            at: 'top left'
        },{
            content: '<p><span class="h5"><?=t('Canonical URL')?></span><br/><?=t('If you are running a site at multiple domains, enter the canonical domain here. This will be used for sitemap generation, any other purposes that require a specific domain. You can usually leave this blank.')?></p>',
            highlightTarget: false,
            nextButton: true,
            target: $('input[name=canonical_url]'),
            my: 'bottom center',
            at: 'top center',
            setup: function() {
                var $url = $('input[name=canonical_url]');
                $(document).scrollTop($url.offset().top);
            }
        },{
            content: '<p><span class="h5"><?=t('SSL URL')?></span><br/><?=t('Certain add-ons require a secure SSL URL. Enter that URL here.')?></p>',
            highlightTarget: false,
            nextButton: true,
            target: $('input[name=canonical_ssl_url]'),
            my: 'bottom center',
            at: 'top center'
        },{
            content: '<p><span class="h5"><?=t('SSL URL')?></span><br/><?=t('Ensure that your site ONLY renders at the canonical URL or the canonical SSL URL.')?></p>',
            highlightTarget: false,
            nextButton: true,
            target: $('input[name=redirect_to_canonical_url]'),
            my: 'bottom left',
            at: 'top left'
        }];

        var tour = new Tourist.Tour({
            steps: steps,
            tipClass: 'Bootstrap',
            tipOptions:{
                showEffect: 'slidein'
            }
        });
        tour.on('start', function() {
            ConcreteHelpLauncher.close();
        });
        tour.on('stop', function() {
            $(document).scrollTop(0);
        });
        ConcreteHelpGuideManager.register('dashboard-system-urls', tour);

    });


</script>
