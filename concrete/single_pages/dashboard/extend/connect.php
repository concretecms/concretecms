<?php
use Concrete\Core\Marketplace\Marketplace;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $permissions \Concrete\Core\Permission\Checker
 * @var $marketplace Marketplace
 */
if ($permissions->canInstallPackages()) { ?>

    <form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('do_connect')?>">
        <?=$this->controller->token->output('do_connect')?>


  <?php
    if ($marketplace->hasConnectionError()) { ?>

        <h4><?=t('Marketplace Error')?></h4>

        <?php
        if ($marketplace->getConnectionError() == Marketplace::E_INVALID_BASE_URL) { ?>

            <fieldset>
                <div class="alert alert-danger">
                <h5><?=t('Error: Invalid Base URL')?></h5>

                <p style="font-size: 1.2rem"><?=t("The URL of this site has not been registered as a valid location in the Concrete marketplace. Please sign in to the project page for this site and add the following URL as a site instance:")?></p>

                </div>

                <div>
                    <input readonly class="form-control" onclick="this.select()" value="<?=$marketplace->getSiteURL()?>">
                </div>
            </fieldset>
        <?php
        } else if ($marketplace->getConnectionError() == Marketplace::E_UNRECOGNIZED_SITE_TOKEN) { ?>

            <fieldset>
                <div class="alert alert-danger">
                    <h5><?=t('Error: Unrecognized Site Token')?></h5>

                    <p style="font-size: 1.2rem"><?=t("This site contains a marketplace token and client ID, but they don't match the ones stored on marketplace.concretecms.com. Please re-enter proper credentials below.")?></p>

                </div>
            </fieldset>

            <?php
        } else if ($marketplace->getConnectionError() ==  Marketplace::E_DELETED_SITE_TOKEN) { ?>


            <fieldset>
                <div class="alert alert-danger">
                    <h5><?=t('Error: Project Page Deleted')?></h5>

                    <p style="font-size: 1.2rem"><?=t("This site is connected to a marketplace page that has been deleted. Please reconnect it to another project page or disconnect it using the button below.")?></p>

                </div>

                <button type="submit" value="disconnect" class="btn btn-secondary" name="disconnect"><?=t("Disconnect")?></button>

            </fieldset>

            <?php
        } else if ($marketplace->getConnectionError() ==  Marketplace::E_SITE_TYPE_MISMATCH_MULTISITE) { ?>

            <fieldset>
                <div class="alert alert-danger">
                    <h5><?=t('Error: Site Type Mismatch – Multi-Site')?></h5>

                    <p style="font-size: 1.2rem"><?=t("This site is connected to a marketplace page of the wrong type. Since this site uses multi-site functionality, the project page it is connected to must be of the Multi-Site Project page type. Please reconnect it to another project page.")?></p>

                </div>

                <button type="submit" value="disconnect" class="btn btn-secondary" name="disconnect"><?=t("Disconnect")?></button>

            </fieldset>
        <?php } else { ?>

            <fieldset>
                <div class="alert alert-danger">
                    <h5><?=t('Error: General Connection Failure')?></h5>

                    <p style="font-size: 1.2rem"><?=t("This site cannot connect to the Concrete marketplace. Ensure curl is enabled on your web server.")?></p>

                </div>

            </fieldset>

        <?php }

    } ?>


            <fieldset class="mb-3">
                <legend><?= t('Step 1: Create a Project Page') ?></legend>
                <?php if ($marketplace->isConnected()) { ?>
                    <?php View::element('dashboard/marketplace_project_page');?>
                <?php } else { ?>
                    <p style="font-size: 1.2rem"><?=t("Downloading add-ons and themes for your site starts with connecting your site to the Concrete Marketplace. If you haven't already, create a project page for your site by clicking the button below. Once your page is created, come back here and paste your connection information into the form below.")?></p>
                    <a href="<?=$projectPageURL?>" target="_blank" class="btn btn-secondary"><?=t('Create Project Page')?></a>
                <?php } ?>
            </fieldset>

            <fieldset>
                <legend><?= t('Step 2: Provide Connection Information') ?></legend>
                <div class="form-group">
                    <label for="csURLToken" class="launch-tooltip control-label" data-placement="right" title="<?=t('This can be found in your project page on marketplace.concretecms.com in the ID field.')?>"><?=t('ID')?></label>
                    <?=$form->text('csURLToken', $dbConfig->get('concrete.marketplace.url_token'))?>
                </div>
                <div class="form-group">
                    <label for="csURLToken" class="launch-tooltip control-label" data-placement="right" title="<?=t('This can be found in your project page on marketplace.concretecms.com the Secret Key field.')?>"><?=t('Secret Key')?></label>
                    <?=$form->text('csToken', $dbConfig->get('concrete.marketplace.token'))?>
                </div>
            </fieldset>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button class="float-end btn btn-primary" type="submit" ><?=t('Save')?></button>
                </div>
            </div>

        </form>


    <?php
} else { ?>

    <?=t('You do not have permission to connect this site to the marketplace.')?>

<?php } ?>
