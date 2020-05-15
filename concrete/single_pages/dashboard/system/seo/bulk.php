<?php

defined('C5_EXECUTE') or die('Access Denied');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Seo\Bulk $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Form\Service\Widget\PageSelector $pageSelector
 * @var int[] $allowedNumResults
 * @var array $searchRequest
 * @var Concrete\Core\Page\Page[]|null $pages
 * @var string $pagination
 */
if ($pages !== null) {
    echo $interface->tabs([
        ['ccm-seobulk-search', t('Search'), false],
        ['ccm-seobulk-results', t('Results'), true],
    ]);
    ?>
    <div class="tab-content">
    <?php
}

if ($pages !== null) {
    ?>
    <div class="tab-pane" id="ccm-seobulk-search" role="tabpanel">
    <?php
}
?>
<form method="GET" action="<?= $controller->action('') ?>">
    <input type="hidden" name="search" value="1" />
    <div class="form-group">
        <?= $form->label('keywords', t('Search')) ?>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
            <?= $form->search('keywords', $searchRequest['keywords'], ['placeholder' => t('Keywords')]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('numResults', t('Number of Pages to Display')) ?>
        <?= $form->select('numResults', array_combine($allowedNumResults, $allowedNumResults), $searchRequest['numResults']) ?>
    </div>
    <div class="form-group">
        <?= $form->label('cParentIDSearchField', t('Parent Page')) ?>
        <?= $pageSelector->selectPage('cParentIDSearchField', $searchRequest['cParentIDSearchField']) ?>
    </div>
    <div class="form-group">
        <?= $form->label('cParentAll', t('How Many Levels Below Parent?')) ?>
        <div class="radio">
            <label>
                <?= $form->radio('cParentAll', '0', $searchRequest['cParentAll'] ? '1' : '0') ?>
                <?= t('First Level') ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <?= $form->radio('cParentAll', '1', $searchRequest['cParentAll'] ? '1' : '0') ?>
                <?= t('All Levels') ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('', t('Filter By:')) ?>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('noDefaultDescription', '1', $searchRequest['noDefaultDescription']) ?>
                <?= t('No Default Description') ?>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <?= $form->checkbox('noMetaDescription', '1', $searchRequest['noMetaDescription']) ?>
                <?= t('No Meta Description') ?>
            </label>
        </div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn btn-primary float-right"><?= t('Search') ?></button>
        </div>
    </div>
</form>
<?php
if ($pages !== null) {
    ?>
    </div>
    <?php
}

if($pages === null) {
    return;
}
?>
<div class="tab-pane show active d-none" id="ccm-seobulk-results" role="tabpanel">
    <div v-if="pages.length">
        <table v-if="pages.length" class="ccm-search-results-table table table-sm">
            <tbody>
                <tr v-for="(page, pageIndex) in pages" v-bind:key="pageIndex">
                    <td>
                        <h2 class="text-center">{{ page.name }}</h2>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col">
                                    <dl class="alert alert-secondary">
                                        <dt><?= t('Page Name') ?></dt>
                                        <dd>{{ page.name }}</dd>
                                        <dt><?= t('Page Type') ?></dt>
                                        <dd>{{ page.type }}</dd>
                                        <dt><?= t('Modified') ?></dt>
                                        <dd>{{ page.modified }}</dd>
                                        <dt><?= t('Page ID') ?></dt>
                                        <dd>{{ page.cID }}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <span class="float-right"><?= t('Characters: %s', '{{ page.input.metaTitle.length }}') ?></span>
                                        <?= $form->label('', t('Meta Title'), ['v-bind:for' => "'meta_title' + pageIndex"]) ?>
                                        <?= $form->text('', '', ['v-model.trim' => 'page.input.metaTitle', 'v-bind:placeholder' => 'page.autoTitle', 'v-bind:id' => "'meta_title' + pageIndex"]) ?>
                                        <span v-bind:class="{invisible: page.input.metaTitle !== ''}" class="help-inline"><?= t('Default value. Click to edit.') ?></span>
                                    </div>
                                    <div class="form-group">
                                        <span class="float-right"><?= t('Characters: %s', '{{ page.input.metaDescription.length }}') ?></span>
                                        <?= $form->label('', t('Meta Description'), ['v-bind:for' => "'meta_description' + pageIndex"]) ?>
                                        <?= $form->textarea('meta_description', '', ['v-model.trim' => 'page.input.metaDescription', 'v-bind:placeholder' => 'page.autoDescription', 'v-bind:id' => "'meta_description' + pageIndex"]) ?>
                                        <span v-bind:class="{invisible: page.input.metaDescription !== ''}" class="help-inline"><?= t('Default value. Click to edit.') ?></span>
                                    </div>
                                    <div v-if="!page.isHomePage" class="form-group">
                                        <?= $form->label('', t('Slug'), ['v-bind:for' => "'collection_handle' + pageIndex"]) ?>
                                        <?= $form->text('', '', ['v-model.trim' => 'page.input.handle', 'maxlength' => '255', 'v-bind:id' => "'collection_handle' + pageIndex"]) ?>
                                        <a class="help-inline url-path" v-bind:href="page.url" target="_blank" v-html="page.htmlPath"></a>
                                    </div>
                                    <div class="form-group form-group-last submit-changes">
                                        <a class="btn float-right" v-on:click.prevent="savePage(page)" v-bind:class="getSavePageClass(page)"><?= t('Save') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        if ($pagination !== '') {
            ?>
            <div style="text-align: center">
                <?= $pagination ?>
            </div>
            <?php
        }
        ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="btn btn-primary float-right" v-on:click.prevent="saveAll" v-bind:class="{disabled: !isSaveAllEnabled}"><?= t('Save All') ?></button>
            </div>
        </div>
    </div>
    <div v-else class="alert alert-info"><?= t('No pages found.') ?></div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.min.js"></script>
<script>
$(document).ready(function() {
    $('#ccm-seobulk-results').removeClass('d-none');
    new Vue({
        el: '#ccm-seobulk-results',
        data: function() {
            return {
                busy: false,
                pages: <?= json_encode($pages) ?>,
            };
        },
        methods: {
            isPageDirty: function(page) {
                if (page.input.metaTitle !== page.metaTitle || page.input.metaDescription !== page.metaDescription) {
                    return true;
                }
                if (!page.isHomePage && page.input.handle !== page.handle) {
                    return true;
                }
                return false;
            },
            isSavePageEnabled: function(page) {
                return this.busy ? false : this.isPageDirty(page);
            },
            getSavePageClass: function(page) {
                return this.isSavePageEnabled(page) ? 'btn-primary' : 'btn-secondary disabled';
            },
            savePage: function(page, callback) {
                var my = this;
                if (!my.isSavePageEnabled(page)) {
                    return;
                }
                var pageIndex = my.pages.indexOf(page);
                my.busy = true;
                $.concreteAjax({
                    url: page.saveAction,
                    data: $.extend(true, {}, page.savePayload, page.input),
                    success: function(newPage) {
                        my.pages.splice(pageIndex, 1, newPage);
                        if (callback) {
                            callback();
                        }
                    },
                    complete: function() {
                        my.busy = false;
                        $.fn.dialog.hideLoader();
                    },
                })
            },
            saveAll: function() {
                var my = this;
                if (!my.isSaveAllEnabled) {
                    return;
                }
                var nextPageIndex = 0;
                function saveNext(nextPageIndex) {
                    for (; nextPageIndex < my.pages.length; nextPageIndex++) {
                        if (!my.isPageDirty(my.pages[nextPageIndex])) {
                            continue;
                        }
                        my.savePage(
                            my.pages[nextPageIndex],
                            function() {
                                setTimeout(function() {
                                    saveNext(nextPageIndex+1);
                                }, 0);
                                
                            }
                        );
                        return;
                    }
                }
                saveNext(0);
            },
        },
        computed: {
            isSaveAllEnabled: function() {
                var my = this;
                if (my.busy) {
                    return false;
                }
                var result = false;
                my.pages.some(function(page) {
                    if (my.isPageDirty(page)) {
                        result = true;
                        return true;
                    }
                });
                return result;
            },
        },
    });
});
</script>
