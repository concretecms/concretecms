<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

/** @var string|null $error */
/** @var string|null $filename */
/** @var bool|null $force */
/** @var int $rcID */
/** @var int $fID */
/** @var Page|null $rc */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

$file = File::getByID($fID);

if ($file instanceof FileEntity && $file->hasFileUUID()) {
    $submitPasswordUrl = (string)Url::to('/download_file', 'submit_password', $file->getFileUUID());
} else {
    $submitPasswordUrl = (string)Url::to('/download_file', 'submit_password', $fID);
}

?>

    <h1>
        <?php echo t('Download File') ?>
    </h1>

<?php if (!isset($filename)) { ?>
    <p>
        <?php echo t('Invalid File.'); ?>
    </p>
<?php } else { ?>
    <p>
        <?php echo t('This file requires a password to download.') ?>
    </p>

    <?php if (isset($error)) { ?>
        <div class="ccm-error-response">
            <?php echo $error ?>
        </div>
    <?php } ?>

    <form action="<?php echo h($submitPasswordUrl) ?>" method="post">
        <?php if (isset($force)) { ?>
            <?php echo $form->hidden("force", $force); ?>
        <?php } ?>

        <?php echo $form->hidden("rcID", $rcID); ?>

        <div class="form-group">
            <?php echo $form->label("password", t("Password"), ['class' => 'form-label']); ?>
            <?php echo $form->password("password", ["class" => "form-control"]); ?>
        </div>

        <button type="submit">
            <?php echo t('Download') ?>
        </button>
    </form>
<?php } ?>

<?php if (isset($rc) && is_object($rc)) { ?>
    <p>
        <a href="<?php echo h((string)Url::to($rc)) ?>">
            &lt; <?php echo t('Back') ?>
        </a>
    </p>
<?php }
