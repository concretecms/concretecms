<?php /** @noinspection PhpDeprecationInspection */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\View\View;

/** @var Concrete\Core\Entity\Attribute\Key\Key[] $attributeKeys */
/** @var PageKey[] $pageAttributeKeys */
/** @var Calendar[] $calendars */
/** @var object|null $featuredAttribute */
/** @var string $filterByTopic */
/** @var array|null $caID */

/** @var string|null $calendarAttributeKeyHandle */
/** @var int $totalToRetrieve */
/** @var int $totalPerPage */
/** @var int $filterByTopicAttributeKeyID */
/** @var int|null $filterByTopicID */
/** @var string|null $filterByPageTopicAttributeKeyHandle */
/** @var bool|null $filterByFeatured */
/** @var string $eventListTitle */
/** @var int|null $linkToPage */
/** @var string $titleFormat */
/** @var Form $form */
$titleFormat = $titleFormat ?? 'h5';

/** @var PageSelector $pageSelector */
$pageSelector = app(PageSelector::class);
$pageTopicAttributeKeyHandles = null;

if (count($pageAttributeKeys)) {
    $pageTopicAttributeKeyHandles = ['' => t('** Select Page Attribute')];

    foreach ($pageAttributeKeys as $attributeKey) {
        $pageTopicAttributeKeyHandles[$attributeKey->getAttributeKeyHandle()] = $attributeKey->getAttributeKeyDisplayName();
    }
}
?>

<fieldset>
    <legend>
        <?php echo t('Data Source') ?>
    </legend>

    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('calendar/block/data_source', [
        'multiple' => true,
        'caID' => $caID ?? null,
        'calendarAttributeKeyHandle' => $calendarAttributeKeyHandle ?? null,
    ]); ?>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Filtering') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('filterByTopic', t('Filter By Topic')); ?>
        <?=$filterByTopic?>
        <div class="form-check">
            <?php echo $form->radio('filterByTopic', 'none', $filterByTopic, ['id' => 'filterByTopicNone', 'name' => 'filterByTopic']); ?>
            <?php echo $form->label('filterByTopicNone', t('No topic filtering'), ['class' => 'form-check-label']); ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('filterByTopic', 'specific', $filterByTopic, ['id' => 'filterByTopicSpecific', 'name' => 'filterByTopic']); ?>
            <?php echo $form->label('filterByTopicSpecific', t('Specific Topic'), ['class' => 'form-check-label']); ?>
        </div>

        <?php if (count($pageAttributeKeys)) { ?>
            <div class="form-check">
                <?php echo $form->radio('filterByTopic', 'page_attribute', $filterByTopic, ['id' => 'filterByTopicPageAttribute', 'name' => 'filterByTopic']); ?>
                <?php echo $form->label('filterByTopicPageAttribute', t('Current Page'), ['class' => 'form-check-label']); ?>
            </div>

            <div data-row="page-attribute">
                <div class="form-group">
                    <?php echo $form->select('filterByPageTopicAttributeKeyHandle', $pageTopicAttributeKeyHandles, $filterByPageTopicAttributeKeyHandle ?? null); ?>
                </div>
            </div>
        <?php } ?>

        <div data-row="specific-topic">
            <div class="form-group">
                <!--suppress HtmlFormInputWithoutLabel -->
                <select class="form-select" name="filterByTopicAttributeKeyID">
                    <option value="">
                        <?php echo t('** Select Topic Attribute') ?>
                    </option>

                    <?php foreach ($attributeKeys as $ak) { ?>
                        <?php
                        /** @var \Concrete\Attribute\Topics\Controller $attributeController */
                        $attributeController = $ak->getController(); ?>

                        <option value="<?php echo h($ak->getAttributeKeyID()) ?>"
                                <?php if ($ak->getAttributeKeyID() == $filterByTopicAttributeKeyID) { ?>selected<?php } ?>
                                data-tree-id="<?php echo h($attributeController->getTopicTreeID()) ?>">
                            <?php echo $ak->getAttributeKeyDisplayName() ?>
                        </option>
                    <?php } ?>
                </select>

                <?php echo $form->hidden('filterByTopicID', (string) ($filterByTopicID ?? null)); ?>

                <div id="ccm-block-event-list-topic-tree-wrapper"></div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('filterByFeatured', t('Featured Events')); ?>

        <div class="form-check">
            <?php

            $checkboxAttributes = [];
            if (!is_object($featuredAttribute)) {
                $checkboxAttributes['disabled'] = 'disabled';
            }

            echo $form->checkbox('filterByFeatured', '1', $filterByFeatured ?? null, $checkboxAttributes);
            echo $form->label('filterByFeatured', t('Display featured events only.'), ['class' => 'form-check-label']);
            ?>
        </div>

        <?php if (!is_object($featuredAttribute)) { ?>
            <div class="alert alert-info">
                <?php echo t('(%s: You must create the "is_featured" event attribute first.)', '<strong>' . t('Note') . '</strong>'); ?>
            </div>
        <?php } ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Results') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('eventListTitle', t('Title')); ?>
	    <div class="input-group">
        	<?php echo $form->text('eventListTitle', $eventListTitle) ?>
			<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, ['style' => 'width:105px;flex-grow:0;', 'class' => 'form-select']); ?>
		</div>
	</div>

    <div class="form-group">
        <?php echo $form->label('totalToRetrieve', t('Total Number of Events to Retrieve')); ?>
        <?php echo $form->text('totalToRetrieve', (string) $totalToRetrieve); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('totalPerPage', t('Events to Display Per Page')); ?>
        <?php echo $form->text('totalPerPage', (string) $totalPerPage); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('linkToPage', t('Link To More Events Calendar/Page')); ?>
        <?php echo $pageSelector->selectPage('linkToPage', $linkToPage ?? null) ?>
    </div>
</fieldset>

<!--suppress EqualityComparisonWithCoercionJS -->
<script type="text/javascript">
    $(function () {
        $('input[name=filterByTopic]').on('change', function () {
            let selected = $('input[name=filterByTopic]:checked').val();
            if (selected == 'page_attribute') {
                $('div[data-row=specific-topic]').hide();
                $('div[data-row=page-attribute]').show();
            } else if (selected == 'specific') {
                $('div[data-row=page-attribute]').hide();
                $('div[data-row=specific-topic]').show();
            } else {
                $('div[data-row=specific-topic]').hide();
                $('div[data-row=page-attribute]').hide();
            }
        }).trigger('change');

        $('select[name=filterByTopicAttributeKeyID]').on('change', function () {
            let $tree = $('#ccm-block-event-list-topic-tree');

            $tree.remove();

            let chosenTree = $(this).find('option:selected').attr('data-tree-id');

            if (!chosenTree) {
                return;
            }

            $('#ccm-block-event-list-topic-tree-wrapper').append($('<div id=ccm-block-event-list-topic-tree>'));

            $tree.concreteTree({
                'treeID': chosenTree,
                'chooseNodeInForm': true,
                <?php if (isset($filterByTopicID)) { ?>
                'selectNodesByKey': [<?php echo (int) $filterByTopicID ?>],
                <?php } ?>
                'onSelect': function (nodes) {
                    if (nodes.length) {
                        $('input[name=filterByTopicID]').val(nodes[0]);
                    } else {
                        $('input[name=filterByTopicID]').val('');
                    }
                }
            });
        }).trigger('change');
    });
</script>
