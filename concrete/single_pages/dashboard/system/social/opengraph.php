<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" action="<?php
echo $this->action('submit'); ?>" v-cloak data-vue-app="opengraph">

    <?php
    echo $this->controller->token->output('submit'); ?>

    <!-- Enable OpenGraph Checkbox -->
    <div class="mb-3">
        <div class="form-check">
            <input
                    type="checkbox"
                    id="enable-opengraph"
                    name="enableOpengraph"
                    value="1"
                    v-model="enableOpengraph"
                    class="form-check-input"
            />
            <label for="enable-opengraph" class="form-check-label">
                <?= t('Enable Open Graph') ?>
            </label>
        </div>
    </div>

    <div v-if="enableOpengraph">
        <fieldset>
            <legend><?= t('Standard Properties') ?></legend>
            <!-- Type (og:type) Select -->
            <div class="mb-3">
                <label for="og-type" class="form-label"><?= t('Type (og:type)') ?></label>
                <select id="og-type" v-model="ogType" name="ogType" class="form-select">
                    <optgroup label="Basic">
                        <option value="value:website"><?= t('Use the string \'website\'') ?></option>
                    </optgroup>
                    <optgroup label="<?= t('Use Page Attribute') ?>">
                        <option
                                v-for="(value, key) in textAttributes"
                                :key="key"
                                :value="'page_attribute:' + key"
                        >
                            {{ value }}
                        </option>
                    </optgroup>
                </select>
            </div>

            <!-- Title (og:title) Select -->
            <div class="mb-3">
                <label for="og-title" class="form-label"><?= t('Title (og:title)') ?></label>
                <select id="og-title" v-model="ogTitle" name="ogTitle" class="form-select">
                    <optgroup label="Core Properties">
                        <option value="page_property:title"><?= t('Page Title') ?></option>
                    </optgroup>
                    <optgroup label="<?= t('Use Page Attribute') ?>">
                        <option
                                v-for="(value, key) in textAttributes"
                                :key="key"
                                :value="'page_attribute:' + key"
                        >
                            {{ value }}
                        </option>
                    </optgroup>
                </select>
            </div>

            <!-- Description (og:description) Select -->
            <div class="mb-3">
                <label for="og-description" class="form-label">
                    <?= t('Description (og:description)') ?>
                </label>
                <select
                        id="og-description"
                        name="ogDescription"
                        v-model="ogDescription"
                        class="form-select"
                >
                    <optgroup label="Core Properties">
                        <option value="page_property:description"><?= t('Page Description') ?></option>
                    </optgroup>
                    <optgroup label="<?= t('Use Page Attribute') ?>">
                        <option
                                v-for="(value, key) in textareaAttributes"
                                :key="key"
                                :value="'page_attribute:' + key"
                        >
                            {{ value }}
                        </option>
                    </optgroup>
                </select>
            </div>

            <!-- Thumbnail (og:thumbnail) Select -->
            <div class="mb-3">
                <label for="og-thumbnail" class="form-label">
                    <?= t('Thumbnail (og:thumbnail)') ?>
                </label>
                <select id="og-thumbnail" name="ogThumbnail" v-model="ogThumbnail" class="form-select">
                    <optgroup label="<?= t('Use Page Attribute') ?>">
                        <option
                                v-for="(value, key) in imageFileAttributes"
                                :key="key"
                                :value="'page_attribute:' + key"
                        >
                            {{ value }}
                        </option>
                    </optgroup>
                </select>
            </div>
        </fieldset>
        <fieldset class="mt-4">
            <legend><?= t('Facebook') ?></legend>
            <!-- Facebook App ID (fb:app_id) Text Input -->
            <div class="mb-3">
                <label for="fb-app-id" class="form-label"><?= t('Facebook App ID (fb:app_id)') ?></label>
                <input
                        name="fbAppId"
                        type="text"
                        id="fb-app-id"
                        v-model="fbAppId"
                        class="form-control"
                />
            </div>
        </fieldset>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-success" type="submit"><?php
                echo t('Save') ?></button>
        </div>
    </div>

</form>

<script type="text/javascript">
    $(function () {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'form[data-vue-app=opengraph]',
                components: config.components,
                data: {
                    enableOpengraph: false,
                    ogType: '',
                    ogTitle: '',
                    ogDescription: '',
                    ogThumbnail: '',
                    fbAppId: '',
                    opengraphConfig: <?=json_encode($opengraphConfig)?>,
                    textareaAttributes: <?=json_encode($textareaAttributes)?>,
                    imageFileAttributes: <?=json_encode($imageFileAttributes)?>,
                    textAttributes: <?=json_encode($textAttributes)?>
                },
                mounted() {
                    this.enableOpengraph = this.opengraphConfig.enabled
                    if (this.opengraphConfig.field_og_type.value_from === 'value') {
                        this.ogType = 'value:' + this.opengraphConfig.field_og_type.value
                    } else {
                        this.ogType = 'page_attribute:' + this.opengraphConfig.field_og_type.attribute
                    }
                    if (this.opengraphConfig.field_og_title.value_from === 'page_property') {
                        this.ogTitle = 'page_property:' + this.opengraphConfig.field_og_title.property
                    } else {
                        this.ogTitle = 'page_attribute:' + this.opengraphConfig.field_og_title.attribute
                    }
                    if (this.opengraphConfig.field_og_description.value_from === 'page_property') {
                        this.ogDescription = 'page_property:' + this.opengraphConfig.field_og_description.property
                    } else {
                        this.ogDescription = 'page_attribute:' + this.opengraphConfig.field_og_description.attribute
                    }
                    if (this.opengraphConfig.field_og_thumbnail.value_from === 'page_attribute') {
                        this.ogThumbnail = 'page_attribute:' + this.opengraphConfig.field_og_thumbnail.attribute
                    }
                    this.fbAppId = this.opengraphConfig.field_fb_app_id
                }
            })
        })
    })
</script>
