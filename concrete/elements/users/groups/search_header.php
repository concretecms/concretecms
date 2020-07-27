<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Concrete\Controller\Search\Groups;

/** @var Groups $searchController */
/** @var bool $canAddGroup */

/** @noinspection PhpComposerExtensionStubsInspection */
$result = json_encode($searchController->getSearchResultObject()->getJSONObject());

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Request $request */
$request = $app->make(Request::class);
?>

<div class="ccm-dashboard-header-buttons">
<form role="form" data-search-form="groups"
      action="<?php echo (string)Url::to('/ccm/system/search/groups/submit') ?>"
      class="ccm-search-fields ccm-search-fields-none form-inline">

    <?php echo $form->hidden("filter", $request->request('filter')); ?>

    <?php echo $form->search('keywords', $request->request('keywords'), [
        'placeholder' => t('Name'),
        'class' => 'form-control-sm',
        'autocomplete' => 'off']);
    ?>

    <button type="submit" class="btn btn-secondary ml-2 btn-sm">
        <svg width="16" height="16">
            <use xlink:href="#icon-search"/>
        </svg>
    </button>


    <?php if ($canAddGroup) { ?>
        <a class="btn btn-secondary btn-sm ml-2"
           href="<?php echo (string)Url::to('/dashboard/users/add_group') ?>"
           title="<?php echo t('Add Group') ?>">
            <?php echo t('Add Group') ?> <i class="fa fa-plus-circle"></i>
        </a>
    <?php } ?>
</form>
</div>

<!--suppress EqualityComparisonWithCoercionJS, ES6ConvertVarToLetConst -->
<script>
    $(function () {
        $('#ccm-dashboard-content').concreteAjaxSearch({
            result: <?php echo $result?>,
            onLoad: function (concreteSearch) {
                var handleSubmit = function () {
                    var $input = concreteSearch.$element.find('input[name=keywords]');

                    if ($input.val() != '') {
                        concreteSearch.$element.find('[data-group-tree]').hide();
                        concreteSearch.$results.show();
                    } else {
                        concreteSearch.$element.find('[data-group-tree]').show();
                        concreteSearch.$results.hide();
                    }
                    return false;
                }
                concreteSearch.$element.on('submit', 'form[data-search-form=groups]', handleSubmit);
                handleSubmit();
                concreteSearch.$element.on('keyup', 'input[name=keywords]', function () {
                    if ($(this).val() == '') {
                        handleSubmit();
                    }
                });
                <?php if ($selectMode) { ?>
                concreteSearch.$element.on('click', 'a[data-group-id]', function () {
                    ConcreteEvent.publish('SelectGroup', {
                        gID: $(this).attr('data-group-id'),
                        gName: $(this).attr('data-group-name')
                    });
                    return false;
                });
                <?php  } ?>
            }
        });
    });
</script>