<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Config\Repository\Repository $config
 * @var Concrete\Core\Mail\SenderConfiguration $senderConfiguration
 */?>

<form action="<?= h($view->action('save')) ?>" method="POST">
    <?php
    $token->output('addresses');
    foreach ($senderConfiguration->getEntries() as $entry) {
        $keyPrefix = $entry->getPackageHandle();
        if ($keyPrefix !== '') {
            $keyPrefix = "{$keyPrefix}::";
        }
        ?>
        <fieldset>
            <legend><?= h($entry->getName()) ?></legend>
    		<?php
            if ($entry->getNotes() !== '') {
                ?>
            	<div class="small text-muted mb-2">
                	<?= $entry->getNotes() ?>
            	</div>
            	<?php
            }
            if ($entry->getNameKey() !== '') {
                $fieldName = str_replace('.', '__', "name@{$keyPrefix}{$entry->getNameKey()}");
                $value = $config->get($keyPrefix . $entry->getNameKey());
                $attributes = ($entry->getRequired() & $entry::REQUIRED_EMAIL_AND_NAME) === $entry::REQUIRED_EMAIL_AND_NAME ? ['required' => 'required'] : [];
                ?>
                <div class="form-group">
                    <?= $form->label($fieldName, t('Sender Name')) ?>
                    <?= $form->text($fieldName, $value, $attributes) ?>
                </div>
                <?php
            }
            $fieldName = str_replace('.', '__', "address@{$keyPrefix}{$entry->getEmailKey()}");
            $value = $config->get($keyPrefix . $entry->getEmailKey());
            $attributes = $entry->getRequired() & $entry::REQUIRED_EMAIL === $entry::REQUIRED_EMAIL ? ['required' => 'required'] : [];
            ?>
            <div class="form-group">
                <?= $form->label($fieldName, t('Email Address')) ?>
                <?= $form->email($fieldName, $value, $attributes) ?>
            </div>
        </fieldset>
        <?php
    }
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary">
                <?= t('Save') ?>
            </button>
        </div>
    </div>
</form>
