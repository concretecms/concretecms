<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;

/** @var string $headerSearchAction */
/** @var Form $form */
?>

<div class="ccm-header-search-form ccm-ui" data-header="api-integrations">
    <form method="get" class="form-inline" action="<?php echo $headerSearchAction ?>">

        <div class="ccm-header-search-form-input input-group">
            <?php
                echo $form->search('keywords', [
                    'placeholder' => t('Search'),
                    'class' => 'form-control border-end-0',
                    'autocomplete' => 'off'
                ]);
            ?>
            <button type="submit" class="input-group-icon">
                <svg width="16" height="16">
                    <use xlink:href="#icon-search"/>
                </svg>
            </button>
        </div>
    </form>
</div>