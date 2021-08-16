<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<footer>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <?=t('Copyright %s. ', date('Y'))?>
                    <?php echo t('Built with <strong><a href="https://www.concretecms.org" class="Concrete CMS" rel="nofollow">Concrete CMS</a></strong>.') ?>
                </div>
                <div class="col-md-6 text-md-end">
                    <?php echo Core::make('helper/navigation')->getLogInOutLink() ?>
                </div>
            </div>
        </div>
    </section>
</footer>

<?php $this->inc('elements/footer_bottom.php');?>
