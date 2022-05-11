<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Express\Form\Control\Type\Item\ItemInterface;
use Concrete\Core\Express\Form\Control\Type\TypeInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var UserInterface $interface */
/** @var array $drivers */
/** @var FieldSet $set */
/** @var array $tabs */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div>
    <?php echo $interface->tabs($tabs) ?>
</div>

<div class="tab-content">
    <?php $i = 0; ?>

    <?php foreach ($drivers as $type => $driver) { ?>
        <?php /** @var TypeInterface $driver */ ?>

        <div class="tab-pane<?php echo ($i === 0) ? " active" : ""; ?>" id="<?php echo $type ?>" role="tabpanel">
            <ul class="item-select-list" id="ccm-stack-list">
                <?php $items = $driver->getItems($set->getForm()->getEntity()); ?>

                <?php foreach ($items as $item) { ?>
                    <?php /** @var ItemInterface $item */ ?>
                    <li>
                        <a href="#"
                           data-select="control-item"
                           data-item-type="<?php echo h($type) ?>"
                           data-item-id="<?php echo h($item->getItemIdentifier()) ?>">
                            <?php echo $item->getIcon() ?><?php echo $item->getDisplayName() ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <?php $i++; ?>
    <?php } ?>
</div>


<!--suppress ES6ConvertVarToLetConst -->
<script type="text/javascript">
    $(function () {
        $('a[data-select=control-item]').on('click', function () {
            var type = $(this).attr('data-item-type'),
                id = $(this).attr('data-item-id');
            var formData = [{
                'name': 'type',
                'value': type
            }, {
                'name': 'id',
                'value': id
            }, {
                'name': 'ccm_token',
                'value': '<?php echo $token->generate('add_control')?>'
            }];

            jQuery.fn.dialog.showLoader();

            $.ajax({
                type: 'post',
                data: formData,
                url: '<?php echo $view->action('add_control', $set->getId())?>',
                success: function (html) {
                    jQuery.fn.dialog.hideLoader();
                    jQuery.fn.dialog.closeTop();
                    $('div[data-field-set=<?php echo $set->getID()?>] tbody').append(html);
                    $('a.dialog-launch').dialog();

                    // pop open the latest control so we can edit its options immediately
                    $('div[data-field-set=<?php echo $set->getID()?>] tr[data-field-set-control]:last-child a[data-command=edit-control]∫').trigger('click');

                }
            });
        });
    });
</script>