<?php

use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Basics\Multilingual\Update $controller
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var string $translateUrlPrefix
 * @var string $currentLocale
 * @var Concrete\Core\Package\Package[] $packages
 */

?>
<script type="text/x-template" id="ccm-ml-app-language-row">
    <tr>
        <td class="pt-2">
            <div v-if="entry.remote" class="progress launch-tooltip" v-bind:title="progressTitle">
                <div class="progress-bar progress-bar-info" role="progressbar" v-bind:style="{width: `${entry.remote.progress}%`}">
                    {{ entry.remote.progress }}%
                </div>
            </div>
        </td>
        <td>
            <code>{{ locale.id }}</code>
        </td>
        <td>
            <a href="#" v-on:click.prevent="$emit('name-click')">{{ locale.name }}</a>
        </td>
        <td class="d-none d-sm-table-cell">
            {{ updatedOn }}
        </td>
        <td class="text-end">
            <button v-if="buttonText" class="btn btn-sm btn-primary text-nowrap" v-bind:disabled="disabled || downloading" v-on:click.prevent="if (!disabled && !downloading) $emit('button-click')">
                <template v-if="downloading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <?= t('Downloading') ?>
                </template>
                <template v-else>
                    {{ buttonText }}
                </template>
            </button>
        </td>
    </tr>
</script>

<div id="ccm-ml-app" v-cloak>
    <div v-if="state === STATES.FETCHING" class="alert alert-info text-center">
        <i class="fas fa-spinner fa-spin"></i>
        <span v-if="fetchingDescription">
            <br />
            {{ fetchingDescription }}
        </span>
    </div>
    <div v-else-if="state === STATES.FETCH_FAILED" class="alert alert-danger" style="white-space: pre-wrap">{{ fetchError }}</div>
    <div v-else-if="state === STATES.FETCHED"> 
        <div class="accordion" id="ccm-packages">
            <div v-for="(item, itemIndex) in items" class="accordion-item">
                <div class="accordion-header" v-bind:id="`ccm-package-${item.handle ?? ''}-header`">
                    <h4 class="panel-title">
                        <button type="button" class="h2 accordion-button" v-bind:class="itemIndex === 0 ? '' : 'collapsed'" data-bs-toggle="collapse" v-bind:data-bs-target="`#ccm-package-${item.handle ?? ''}-body`">
                            <span class="position-relative">
                                {{ item.name }}
                                <span v-if="getNumOutdated(item)" class="position-absolute top-0 translate-middle badge rounded-pill bg-info ms-3 small">
                                    {{ getNumOutdated(item) }}
                                </span>
                            </span>
                        </button>
                    </h4>
                </div>
                <div v-bind:id="`ccm-package-${item.handle ?? ''}-body`" class="accordion-collapse collapse" v-bind:class="itemIndex === 0 ? 'show' : ''" data-bs-parent="#ccm-packages">
                    <div class="accordion-body">
                        <a v-if="translateUrlPrefix" target="_blank" class="float-end" v-bind:href="getItemTranslateUrl(item)"><?= t('more details') ?></a>
                        <table class="table table-sm table-hover">
                            <colgroup>
                                <col width="60" />
                                <col width="1" />
                                <col />
                                <col />
                                <col width="1" />
                            </colgroup>
                            <tbody>
                                <tr v-if="item.data.outdated"><th colspan="5"><?= t('Updates to installed languages') ?></th></tr>
                                <tr v-for="locale in listLocaleKeys(item.data.outdated)"
                                    is="LanguageRow"
                                    v-bind:disabled="busy || item.data.outdated[locale.id].installed"
                                    v-bind:downloading="downloading?.item === item && downloading?.localeID === locale.id"
                                    v-bind:item="item"
                                    v-bind:locale="locale"
                                    v-bind:entry="item.data.outdated[locale.id]"
                                    v-bind:button-text="item.data.outdated[locale.id].installed ? <?= h(json_encode(t('Updated'))) ?> : <?= h(json_encode(t('Update'))) ?>"
                                    v-on:name-click="showDetails(item, locale, item.data.outdated[locale.id])"
                                    v-on:button-click="install(item, locale.id, item.data.outdated[locale.id])"
                                ></tr>

                                <tr v-if="item.data.onlyRemote"><th colspan="5"><?= t('Installable languages') ?></th></tr>
                                <tr v-for="locale in listLocaleKeys(item.data.onlyRemote)"
                                    is="LanguageRow"
                                    v-bind:disabled="busy || item.data.onlyRemote[locale.id].installed"
                                    v-bind:downloading="downloading?.item === item && downloading?.localeID === locale.id"
                                    v-bind:item="item"
                                    v-bind:locale="locale"
                                    v-bind:entry="item.data.onlyRemote[locale.id]"
                                    v-bind:button-text="item.data.onlyRemote[locale.id].installed ? <?= h(json_encode(t('Installed'))) ?> : <?= h(json_encode(t('Install'))) ?>"
                                    v-on:name-click="showDetails(item, locale, item.data.onlyRemote[locale.id])"
                                    v-on:button-click="install(item, locale.id, item.data.onlyRemote[locale.id])"
                                ></tr>
                                
                                <tr v-if="item.data.updated"><th colspan="5"><?= t('Up-to-date languages') ?></th></tr>
                                <tr v-for="locale in listLocaleKeys(item.data.updated)"
                                    is="LanguageRow"
                                    v-bind:item="item"
                                    v-bind:locale="locale"
                                    v-bind:entry="item.data.updated[locale.id]"
                                    v-on:name-click="showDetails(item, locale, item.data.updated[locale.id])"
                                ></tr>
                                
                                <tr v-if="item.data.onlyLocal"><th colspan="5"><?= t('Only local languages') ?></th></tr>
                                <tr v-for="locale in listLocaleKeys(item.data.onlyLocal)"
                                    is="LanguageRow"
                                    v-bind:item="item"
                                    v-bind:locale="locale"
                                    v-bind:entry="item.data.onlyLocal[locale.id]"
                                    v-on:name-click="showDetails(item, locale, item.data.onlyLocal[locale.id])"
                                ></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="ccm-ml-app-details" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?=t('Language Details')?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <table v-if="displayDetailsFor?.entry.local" class="table table-sm">
                        <caption class="caption-top"><?= t('Local translations file') ?></caption>
                        <tbody>
                            <tr>
                                <th><?= t('File path') ?></th>
                                <td><code>{{ displayDetailsFor.entry.local.file }}</code></td>
                            </tr>
                            <tr>
                                <th><?= t('Version') ?></th>
                                <td><code>{{ displayDetailsFor.entry.local.version }}</code></td>
                            </tr>
                            <tr>
                                <th><?= t('Updated on') ?></th>
                                <td>{{ formatTimestamp(displayDetailsFor.entry.local.updatedOn) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table v-if="displayDetailsFor?.entry.remote" class="table table-sm mb-0">
                        <caption class="caption-top"><?= t('Remote translations file') ?></caption>
                        <tbody>
                            <tr>
                                <th><?= t('Version') ?></th>
                                <td><code>{{ displayDetailsFor.entry.remote.version }}</code></td>
                            </tr>
                            <tr>
                                <th><?= t('Updated on') ?></th>
                                <td>{{ formatTimestamp(displayDetailsFor.entry.remote.updatedOn) }}</td>
                            </tr>
                            <tr>
                                <th><?= t('Total strings') ?></th>
                                <td>{{ displayDetailsFor.entry.remote.total }}</td>
                            </tr>
                            <tr>
                                <th><?= t('Translated strings') ?></th>
                                <td>{{ displayDetailsFor.entry.remote.translated }}</td>
                            </tr>
                            <tr>
                                <th><?= t('Untranslated strings') ?></th>
                                <td>{{ displayDetailsFor.entry.remote.total - displayDetailsFor.entry.remote.translated }}</td>
                            </tr>
                            <tr>
                                <th><?= t('Translation progress') ?></th>
                                <td>{{ displayDetailsFor.entry.remote.progress }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary float-end" data-bs-dismiss="modal"><?=t('Close')?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper" v-if="getNextOutdated()">
        <div class="ccm-dashboard-form-actions">
            <button v-if="getNextOutdated()" class="btn btn-primary float-end" v-bind:disabled="busy" v-on:click.prevent="updateAllOutdated()"><?= t('Update all outdated languages') ?></button>
        </div>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', function() { 

const LANGUAGE_DISPLAYNAMES = new Intl.DisplayNames(
    <?= json_encode(str_replace('_', '-', $currentLocale)) ?>,
    {
        type: 'language',
        style: 'long',
        fallback: 'code',
        languageDisplay: 'standard',
    }
);

function getLocaleName(localeID)
{
    return LANGUAGE_DISPLAYNAMES.of(localeID.replace(/_/g, '-'));
}

const RELATIVE_TIME_FORMAT = new Intl.RelativeTimeFormat(
    <?= json_encode(str_replace('_', '-', $currentLocale)) ?>,
    {
        numeric: 'auto',
    }
);
const DATE_FORMAT = new Intl.DateTimeFormat(
    <?= json_encode(str_replace('_', '-', $currentLocale)) ?>,
    {
        dateStyle: 'long',
        timeStyle: 'short', 
    }
);

function formatTimestamp(timestamp)
{
    if (!timestamp) {
        return '';
    }
    const date = new Date(timestamp * 1000);
    const ageInSeconds = Math.round((date - new Date()) / 1000);
    if (ageInSeconds >= 0) {
        if (ageInSeconds < 60) {
            return RELATIVE_TIME_FORMAT.format(ageInSeconds, 'second');
        }
        if (ageInSeconds < 3600) {
            return RELATIVE_TIME_FORMAT.format(Math.round(diffInSeconds / 60), 'minute');
        }
        if (diffInSeconds < 86400) {
            return RELATIVE_TIME_FORMAT.format(Math.round(diffInSeconds / 3600), 'hour');
        }
    }
    return DATE_FORMAT.format(date);
}


const LanguageRow = {
    template: '#ccm-ml-app-language-row',
    props: {
        disabled: {
            type: Boolean,
            default: false,
        },
        downloading: {
            type: Boolean,
            default: false,
        },
        item: {
            type: Object,
            required: true,
        },
        locale: {
            type: Object,
            required: true,
        },
        entry: {
            type: Object,
            required: true,
        },
        buttonText: {
            type: String,
            default: '',
        },
    },
    computed: {
        progressTitle() {
            if (!this.entry.remote) {
                return '';
            }
            return <?= json_encode('%1$s translated strings out of %2$s') ?>.replace('%1$s', this.entry.remote.translated).replace('%2$s', this.entry.remote.total)
        },
        updatedOn() {
            const dateTime = formatTimestamp(this.entry.remote ? this.entry.remote.updatedOn : this.entry.local?.updatedOn);
            return dateTime ? <?= json_encode(tc('DateTime', 'Updated: %s')) ?>.replace('%s', dateTime) : '';
        },
    },
};

new Vue({
    el: '#ccm-ml-app',
    components: {
        LanguageRow,
    },
    data() {
        const STATES = {
            FETCHING: 1,
            FETCHED: 2,
            FETCH_FAILED: 4,
        };
        return {
            STATES,
            state: STATES.FETCHING,
            translateUrlPrefix: <?= json_encode($translateUrlPrefix) ?>,
            items: [{
                name: 'Concrete',
                data: null,
            }].concat(<?= json_encode(array_map(
                static function (Package $package) {
                    return [
                        'handle' => $package->getPackageHandle(),
                        'name' => $package->getPackageName(),
                        'data' => null,
                    ];
                },
                $packages
            )) ?>),
            fetchingDescription: '',
            fetchError: null,
            displayDetailsFor: null,
            downloading: null,
        };
    },
    mounted() {
        window.addEventListener('beforeunload', (e) => {
            if (this.state === this.STATES.FETCHED && this.busy) {
                e.preventDefault();
                return e.returnValue = 'confirm';
            }
        });
        this.fetchNextItem();
    },
    computed: {
        busy() {
            return this.downloading ? true : false;
        },
    },
    methods: {
        formatTimestamp,
        getNumOutdated(item) {
            if (!item.data.outdated) {
                return 0;
            }
            let result = 0;
            for (const localeID in item.data.outdated) {
                if (!item.data.outdated[localeID].installed) {
                    result++;
                }
            }
            return result;
        },
        async fetchNextItem() {
            if (this.state !== this.STATES.FETCHING) {
                return;
            }
            let item = null;
            this.items.some((i) => {
                if (i.data === null) {
                    item = i;
                    return true;
                }
            });
            if (item === null) {
                NProgress.done();
                this.state = this.STATES.FETCHED;
                return;
            }
            this.fetchingDescription = <?= json_encode(t('Checking translations for %s')) ?>.replace('%s', item.name);
            let responseData;
            NProgress.set(this.items.filter((p) => p.data !== null).length / this.items.length);
            try {
                const response = await window.fetch(<?= json_encode($view->action('fetchState')) ?>, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: new URLSearchParams([
                        [<?= json_encode($token::DEFAULT_TOKEN_NAME) ?>, <?= json_encode($token->generate('ccm-ml-fetch')) ?>],
                        [item.handle === undefined ? 'core' : 'package', item.handle ?? true],
                    ]),
                });
                const content = await response.text();
                try {
                    responseData = JSON.parse(content);
                } catch {
                    throw new Error(content);
                }
                if (typeof responseData?.error === 'string') {
                    throw new Error(responseData.error);
                }
                if (responseData.error === true) {
                    if (Array.isArray(responseData?.errors) && typeof responseData.errors[0] === 'string') {
                        throw new Error(responseData.errors[0]);
                    }
                    throw new Error(<?= json_encode(t('Unexpected server response')) ?>);
                }
                
                if (!response.ok) {
                    throw new Error(content);
                }
            } catch (e) {
                this.fetchError = e.message || e.toString();
                this.state = this.STATES.FETCH_FAILED;
                return;
            }
            item.data = responseData;
            this.fetchNextItem();
        },
        getItemTranslateUrl(item) {
            return this.translateUrlPrefix + '/' + (item.handle ?? 'concrete');
        },
        listLocaleKeys(obj) {
            if (!obj) {
                return [];
            }
            const localeIDs = Object.keys(obj);
            const list = localeIDs.map((localeID) => {
                return {
                    id: localeID,
                    name: getLocaleName(localeID),
                };
            });
            list.sort((a, b) => a.name.localeCompare(b.name, undefined, {sensitivity: 'base'}));
            return list;
        },
        showDetails(item, locale, entry) {
            this.displayDetailsFor = {item, locale, entry};
            const modal = bootstrap.Modal.getOrCreateInstance('#ccm-ml-app-details');
            modal.show();
        },
        async install(item, localeID, entry, successCallback) {
            if (this.busy) {
                return;
            }
            this.downloading = {item, localeID};
            try {
                const response = await window.fetch(<?= json_encode($view->action('install')) ?>, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                    },
                    body: new URLSearchParams([
                        [<?= json_encode($token::DEFAULT_TOKEN_NAME) ?>, <?= json_encode($token->generate('ccm-ml-install')) ?>],
                        [item.handle === undefined ? 'core' : 'package', item.handle ?? true],
                        ['localeID', localeID],
                    ]),
                });
                const content = await response.text();
                try {
                    responseData = JSON.parse(content);
                } catch {
                    throw new Error(content);
                }
                if (typeof responseData?.error === 'string') {
                    throw new Error(responseData.error);
                }
                if (responseData.error === true) {
                    if (Array.isArray(responseData?.errors) && typeof responseData.errors[0] === 'string') {
                        throw new Error(responseData.errors[0]);
                    }
                    throw new Error(<?= json_encode(t('Unexpected server response')) ?>);
                }
                
                if (!response.ok) {
                    throw new Error(content);
                }
            } catch (e) {
                ConcreteAlert.error({
                    message: e.message || e.toString()
                });
                return;
            } finally {
                this.downloading = null;
            }
            entry.local = responseData;
            entry.installed = true;
            if (successCallback) {
                successCallback();
            }
        },
        updateAllOutdated() {
            if (this.state !== this.STATES.FETCHED || this.busy) {
                return;
            }
            const nextOutdated = this.getNextOutdated();
            if (!nextOutdated) {
                return;
            }
            const {item, localeID, entry} = nextOutdated;
            this.install(item, localeID, entry, () => this.updateAllOutdated());
        },
        getNextOutdated() {
            if (this.state !== this.STATES.FETCHED) {
                return null;
            }
            for (const item of this.items) {
                if (!item.data.outdated) {
                    continue;
                }
                for (const localeID in item.data.outdated) {
                    const entry = item.data.outdated[localeID];
                    if (!entry.installed) {
                        return {item, localeID, entry};
                    }
                }
            }
            return null;
        },
    },
});

});</script>
