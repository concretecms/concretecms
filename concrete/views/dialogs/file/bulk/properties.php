<?php /** @noinspection PhpUnusedParameterInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\View\View;
use Concrete\Controller\Dialog\File\Bulk\Properties;

/** @var Properties $controller */
?>

<div class="ccm-ui">
    <div data-container="editable-fields">
        <section>
            <h4>
                <?php echo t('Other Attributes') ?>
            </h4>

            <?php
                /** @noinspection PhpUnhandledExceptionInspection */
                View::element('attribute/editable_list', [
                    'attributes' => $attributes,
                    'objects' => $files,
                    'saveAction' => $controller->action('update_attribute'),
                    'clearAction' => $controller->action('clear_attribute'),
                    'permissionsCallback' => function ($ak, $permissionsArguments) {
                        return true; // this is fine because you can't even access this interface without being able to edit every file.
                    },
                ]);
            ?>
        </section>

        <script>
            $('div[data-container=editable-fields]').concreteEditableFieldContainer({
                data: [
                    <?php foreach ($files as $f): ?>
                        {'name': 'fID[]', 'value': '<?php echo $f->getFileID()?>'},
                    <?php endforeach; ?>
                ]
            });
        </script>
    </div>
</div>