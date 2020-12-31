<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Attribute\Duration\Controller;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var Controller $view */
/** @var string $value */
/** @var array $unitTypes */
/** @var string $unitType */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<div class="input-group">
    <?php echo $form->number($view->field('value'), $value); ?>

    <div class="input-group-append">
        <?php echo $unitTypes[$unitType]; ?>
    </div>
</div>