<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var string $name */
/** @var string $label */
/** @var string $value */
/** @var string $valueFormat */
/** @var string $default */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Repository $config */
$config = $app->make(Repository::class);
$min = $config->get('concrete.limits.style_customizer.size_min', -50);
$max = $config->get('concrete.limits.style_customizer.size_max', 200);

if (!isset($default)) {
    $default = 0;
}
?>
<div>
    <label class="form-label">
        <?php echo $label; ?>
    </label>

    <div class="ccm-inline-style-slider-wrapper">
        <div class="ccm-inline-style-sliders"
             data-style-slider-min="<?php echo h($min) ?>"
             data-style-slider-max="<?php echo h($max) ?>"
             data-style-slider-default-setting="<?php echo (int)$default; ?>">
        </div>

        <span class="ccm-inline-style-slider-display-value">
        <?php
        $miscFields = [
            "class" => "ccm-inline-style-slider-value",
            "data-value-format" => $valueFormat,
            "autocomplete" => "off"
        ];

        if (!$value) {
            $miscFields["disabled"] = "disabled";
        }

        echo $form->text($name, $value ? h($value) : '0' . $valueFormat, $miscFields);
        ?>
    </span>
    </div>
</div>

<div class="clearfix"></div>