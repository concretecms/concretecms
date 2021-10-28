<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Dashboard\Express\Menu;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var View $view */
/** @var Entity $entity */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div class="ccm-dashboard-header-buttons">
    <?php
    $manage = new Menu($entity);
    /** @noinspection PhpDeprecationInspection */
    $manage->render();
    ?>
</div>


<div class="row">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>

    <div class="col-md-8">
        <form action="<?php echo $view->action('delete_entries') ?>" method="post">
            <br>

            <h2>
                <?php echo t('Are you sure you want to clear all entries?') ?>
            </h2>

            <h4 class="text-danger">
                <?php echo t('This process cannot be undone.') ?>
            </h4>

            <br>

            <div class="form-group">
                <?php echo $token->output('clear_entries') ?>
                <?php echo $form->hidden('entity_id', $entity->getId()) ?>

                <a href="<?php echo (string)Url::to('/dashboard/system/express/entities', 'view_entity', $entity->getId()) ?>"
                   class="btn btn-secondary">
                    <?php echo t('Cancel') ?>
                </a>

                <button type="submit" class="btn btn-danger">
                    <?php echo t('Clear Entity Entries') ?>
                </button>
            </div>
        </form>
    </div>
</div>
