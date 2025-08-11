<?php

use Michelf\Markdown;

/** @var \Concrete\Core\Entity\Package[] $localUpdates */
$localUpdates = $localUpdates ?? [];
/** @var \Concrete\Core\Entity\Package[] $remoteUpdates */
$remoteUpdates = $remoteUpdates ?? [];
/** @var \Concrete\Core\Marketplace\Model\RemotePackage[] $remotePackages */
$remotePackages = $remotePackages ?? [];

defined('C5_EXECUTE') or die('Access Denied.');
$valt = Loader::helper('validation/token');
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/ui');
$tp = new TaskPermission();

$connection = $connection ?? null;
if (!$tp->canInstallPackages()) {
    $connection = null;
}

if (!$tp->canInstallPackages()) {
    ?>
	<p class="block-message alert-message error">
        <?=t('You do not have access to download themes or add-ons from the marketplace.')?>
    </p>
    <?php
} else {
    if (count($localUpdates) == 0 && count($remoteUpdates) == 0) {
        ?>
			<p><?=t('No updates for your add-ons are available.')?></p>
        <?php
    } else {
        ?>
        <table class="table update-addons-table">
			<?php
            foreach ($remoteUpdates as $pkg) {
                $remotePackage = array_first($remotePackages, function ($remote) use ($pkg) {
                    return $remote->handle === $pkg->getPackageHandle();
                });
                ?>

				<tr>
					<td class="ccm-marketplace-list-thumbnail" rowspan="2"><img style="max-width: 50px" src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?></h3><p><?=$pkg->getPackageDescription()?></p>
					<p><strong><?=t('New Version: %s. Upgrading from: %s.', $pkg->getPackageVersionUpdateAvailable(), $pkg->getPackageVersion())?></strong></p>

					</td>
					<?php
                    if (!$remotePackage instanceof \Concrete\Core\Marketplace\Model\RemotePackage) {
                        ?>
						<td class="ccm-marketplace-list-install-button">
                            <input class="btn" disabled="disabled" type="button" value="<?=t('More Information')?>" />
                            <input class="btn primary" disabled="disabled" type="button" value="<?=t('Download and Install')?>" />
                        </td>
			    		<?php
                    } else {
                        ?>
						<td class="ccm-marketplace-list-install-button">
                            <a class="btn" target="_blank" href="#"><?=t('More Information')?></a>
                            <?=$ch->button(t('Download and Install'), View::url('/dashboard/extend/update', 'prepare_remote_upgrade', $remotePackage->id), '', 'primary')?>
                        </td>
					    <?php
                    }
                    ?>
				</tr>
				<?php
                if ($remotePackage instanceof \Concrete\Core\Marketplace\Model\RemotePackage) {
                    ?>
                    <tr>
                        <td colspan="2" style="border-top: 0px">
                            <?php
                            $versionHistory = $remotePackage->fileDescription;
                            if (trim($versionHistory) !== '') {
                                ?>
                                <div class="ccm-marketplace-update-changelog">
                                    <h6><?=t('Version History')?></h6>
                                    <?= Markdown::defaultTransform($versionHistory) ?>
                                </div>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
				    <?php
                } else {
                    ?>
                    <tr>
                        <td colspan="2" style="border-top: 0px">
                            <div class="block-message alert-message error">
                                <p><?=t('Unable to locate this add-on on market.concretecms.com')?></p>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }

            foreach ($localUpdates as $pkg) {
                $entity = $pkg->getPackageEntity();
                ?>

				<tr>
					<td class="ccm-marketplace-list-thumbnail" rowspan="2"><img style="max-width: 50px" src="<?=$ci->getPackageIconURL($pkg)?>" /></td>
					<td class="ccm-addon-list-description"><h3><?=$pkg->getPackageName()?></h3><p><?=$pkg->getPackageDescription()?></p>
					<p><strong><?=t('New Version: %s. Upgrading from: %s.', $pkg->getPackageVersion(), $entity->getPackageVersion())?></strong></p>
					</td>
					<td class="ccm-marketplace-list-install-button"><?=$ch->button(t('Update Add-On'), View::url('/dashboard/extend/update', 'do_update', $pkg->getPackageHandle()), '', 'btn-primary')?></td>
				</tr>
				<tr>
					<td colspan="2" style="border-top: 0px">
						<?php $versionHistory = $pkg->getChangelogContents();
        ?>
						<?php if (trim($versionHistory) != '') {
    ?>
							<div class="ccm-marketplace-update-changelog">
								<h6><?=t('Version History')?></h6>
								<?= Markdown::defaultTransform($versionHistory)?>
							</div>
						<?php
}
        ?>
					</td>
				</tr>

			<?php
    }
    ?>

			</table>

		<?php
}
    ?>

<?php
}

View::element('dashboard/marketplace_connect_offer');
