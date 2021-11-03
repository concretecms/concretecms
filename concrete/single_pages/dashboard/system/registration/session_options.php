<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var string $samesite */
/** @var string $domain */
/** @var bool $secure */
/** @var bool $httponly */
/** @var bool $raw */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div class="alert alert-warning">
    <?php echo t("Changing these values can break your site. "); ?>
</div>

<form method="POST" action="#">
    <?php echo $token->output("update_cookie_options"); ?>

    <div class="form-group">
        <div class="form-check">
            <?php echo $form->checkbox("secure", 1, $secure, ["class" => "form-check-input"]); ?>
            <?php echo $form->label("secure", t("Enable %s", "<code>secure</code>"), ["class" => "form-check-label"]); ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox("httponly", 1, $httponly, ["class" => "form-check-input"]); ?>
            <?php echo $form->label("httponly", t("Enable %s", "<code>httponly</code>"), ["class" => "form-check-label"]); ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox("raw", 1, $raw, ["class" => "form-check-input"]); ?>
            <?php echo $form->label("raw", t("Enable %s", "<code>raw</code>"), ["class" => "form-check-label"]); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("domain", t("Domain")); ?>
        <?php echo $form->text("domain", $domain, ["placeholder" => t("Leave empty to use current domain...")]); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("samesite", t("Same Site")); ?>
        <?php echo $form->select("samesite", [
            "" => t("** Please select"),
            "Lax" => t("Lax"),
            "Strict" => t("Strict"),
            "None" => t("None")
        ], $samesite); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn btn-primary float-end">
                <?php echo t('Save') ?>
            </button>
        </div>
    </div>
</form>