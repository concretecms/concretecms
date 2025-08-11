<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Permission\Checker;
$packageRepository = app(PackageRepositoryInterface::class);
$config = app('config');
$token = app('token');
$connection = $packageRepository->getConnection();
$checker = new Checker();

if (!$connection && ($checker->canInstallPackages() && $config->get('concrete.marketplace.enabled') == true)) { ?>

<div class="card mt-4" data-vue-app="marketplace">
    <div class="card-body bg-light">
        <div class="row">
            <div class="col-10">
                <h4><?= t('Marketplace Connection'); ?></h4>
                <p><?= t('Your site is not connected to the Concrete marketplace. Connecting lets you easily extend a site with themes and add-ons. Connect your site to the marketplace with one click without leaving your site.'); ?></p>
            </div>
            <div class="col-2 d-flex">
                <i class="fa fa-exclamation-triangle fs-1 text-warning mx-auto align-self-center"></i>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <button @click.prevent="connect" class="btn btn-primary"><?= t("Connect with One-Click"); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'div[data-vue-app=marketplace]',
                components: config.components,
                data: {
                },
                methods: {
                    connect() {
                        $.concreteAjax({
                            url: '<?=URL::to('/ccm/system/marketplace/connect')?>',
                            data: {
                                ccm_token: '<?= $token->generate('connect') ?>'
                            },
                            success: function(r) {
                                window.location.reload()
                            }
                        })
                    }
                }
            })
        })
    })
</script>



<?php } else {

    Element::get('dashboard/marketplace/extend')->render();

} ?>