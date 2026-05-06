<?php

declare(strict_types=1);

use Concrete\Core\Utility\RegexParser\ParsedRegex;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Permissions\EarlyPageNotFound $controller
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\View\PageView $view
 *
 * @var string $rootUrl
 * @var bool $early404Enabled
 * @var array[] $early404Rules
 */

?>
<div class="ccm-dashboard-header-buttons">
    <div id="ccm-e404-tools" v-cloak>
        <button class="btn" v-bind:class="rulesEnabled ? 'btn-success' : 'btn-danger'" v-bind:disabled="busy" v-on:click.prevent="toggleRulesEnabled()">
            <template v-if="rulesEnabled"><?= t('Rules Enabled') ?></template>
            <template v-else><?= t('Rules Disabled') ?></template>
        </button>
    </div>
</div>
<div id="ccm-e404-main" v-cloak>
    <fieldset>
        <legend><?= t('Rules') ?></legend>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 1px"></th>
                    <th style="width: 1px"><?= t('Type') ?></th>
                    <th><?= t('Text') ?></th>
                    <th style="width: 1px"><?= t('Options') ?></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(rule, index) in rules">
                    <td>
                        <button class="btn btn-sm btn-danger" v-on:click.prevent="deleteRule(rule)" v-bind:disabled="busy">&times;</button>
                    </td>
                    <td>
                        <select class="form-control form-control-sm" v-model="rule.type" style="width: auto; min-width: max-content" v-bind:disabled="busy">
                            <option v-bind:value="TYPE.EQUALS"><?= t('Equals') ?></option>
                            <option v-bind:value="TYPE.CONTAINS"><?= t('Contains') ?></option>
                            <option v-bind:value="TYPE.STARTSWITH"><?= t('Starts With') ?></option>
                            <option v-bind:value="TYPE.ENDSWITH"><?= t('Ends With') ?></option>
                            <option v-bind:value="TYPE.REGEX"><?= t('Regular Expression') ?></option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control font-monospace" v-model.trim="rule.text" v-bind:readonly="busy" />
                            <span v-if="rule.type === TYPE.REGEX" class="input-group-text">
                                <?= t('Delimiter: %s', '&nbsp;<code>{{ rule.delimiter }}</code>') ?>
                            </span>
                        </div>
                        <div class="text-danger" style="white-space: pre-wrap" v-if="rule.error">{{ rule.error }}</div>
                    </td>
                    <td class="text-nowrap">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" value="" v-bind:id="`ccm-e404-ci${index}`" v-bind:value="MODIFIER.CASE_INSENSITIVE" v-model="rule.modifiers" v-bind:disabled="busy" />
                            <label class="form-check-label" v-bind:for="`ccm-e404-ci${index}`">
                                <?= t('Case Insensitive') ?>
                            </label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <fieldset>
        <legend><?= t('Test') ?></legend>
        <div class="input-group input-group-sm">
            <span class="input-group-text"><?= h($rootUrl) ?></span>
            <input type="text" class="form-control font-monospace" v-model.trim="test.url" v-bind:readonly="busy" />
        </div>
        <div v-bind:class="test.resultClass">{{ test.resultText }}</div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <button class="btn btn-primary" v-bind:disabled="busy" v-on:click.prevent="save()"><?= t('Save Rules') ?></button>
            </div>
        </div>
    </div>
</div>
<script>$(document).ready(async function() {

const {Vue} = await Concrete.Vue.activateContextAsync('cms');

const tools = new Vue({
    el: '#ccm-e404-tools',
    data() {
        return {
            busy: false,
            rulesEnabled: <?= json_encode($early404Enabled) ?>,
            storedRulesHaveErrors: <?= json_encode(
                array_filter(
                    $early404Rules,
                    static function (array $rule): bool {
                        return $rule['error'] !== '';
                    }
                ) !== []
            ) ?>,
        };
    },
    methods: {
        async toggleRulesEnabled() {
            if (this.busy) {
                return;
            }
            this.busy = true;
            try {
                if (!this.rulesEnabled && this.storedRulesHaveErrors) {
                    throw new Error(<?= json_encode(t('Please fix the rule errors and save the rules before activating them')) ?>);
                }
                this.rulesEnabled = await $.ajax({
                    type: 'POST',
                    url: <?= json_encode($view->action('set_rules_enabled')); ?>,
                    data: {
                        <?= json_encode($token::DEFAULT_TOKEN_NAME); ?>: <?= json_encode($token->generate('e404-e')); ?>,
                        enabled: !this.rulesEnabled
                    },
                    dataType: 'json'
                });
            } catch (e) {
                let error;
                if (e?.responseJSON?.errors?.length) {
                    error = e?.responseJSON.errors.join('\n');
                } else {
                    error = e?.responseText || e?.message || <?= json_encode(t('Unknown error')) ?>;
                }
                ConcreteAlert.error({message: error, plainTextMessage: true});
            } finally {
                this.busy = false;
            }
        },
    },
});

const main = new Vue({
    el: '#ccm-e404-main',
    data() {
        return {
            busy: false,
            askBeforeUnload: true,
            rules: <?= json_encode($early404Rules) ?>.map(rule => this.unserializeRule(rule)),
            TYPE: <?= json_encode([
                'REGEX' => ParsedRegex::TYPE_REGEX,
                'EQUALS' => ParsedRegex::TYPE_EQUALS,
                'STARTSWITH' => ParsedRegex::TYPE_STARTSWITH,
                'ENDSWITH' => ParsedRegex::TYPE_ENDSWITH,
                'CONTAINS' => ParsedRegex::TYPE_CONTAINS,
            ]) ?>,
            MODIFIER: <?= json_encode([
                'CASE_INSENSITIVE' => ParsedRegex::MODIFIER_CASELESS,
            ]) ?>,
            WATCH_DELAY: 250,
            rulesWatchTimer: null,
            testUrlWatchTimer: null,
            test: {
                url: '',
                resultClass: '',
                resultText: '',
                _checkKey: '',
            },
        };
    },
    created() {
        this.checkRules();
        this.updateTestUrlResult();
    },
    mounted() {
        window.addEventListener('beforeunload', (e) => {
            if (this.busy && this.askBeforeUnload) {
                return e.returnValue = 'confirm';
            }
        });
    },
    methods: {
        unserializeRule(rule) {
            rule.modifiers = rule.modifiers.split('');
            rule._checkKey = this.buildCheckKey(rule);
            return rule;
        },
        serializeRule(rule) {
            return {
                type: rule.type,
                text: rule.text,
                delimiter: rule.delimiter,
                modifiers: rule.modifiers.join(''),
            };
        },
        buildCheckKey(rule) {
            return [rule.type, rule.text, rule.delimiter, rule.modifiers.join('\x01')].join('\x02');
        },
        checkEmptyRules() {
            if (!this.rules.some(rule => rule.text === '')) {
                this.addRule();
            }
        },
        checkRules() {
            this.checkEmptyRules();
            this.rules.forEach((rule) => {
                if (rule._checkKey !== this.buildCheckKey(rule)) {
                    this.checkRule(rule);
                }
            });
        },
        async checkRule(rule) {
            const checkKey = this.buildCheckKey(rule);
            if (rule.text === '') {
                rule._checkKey = checkKey;
                rule.error = '';
                return;
            }
            if (rule._checkingKey === checkKey) {
                return;
            }
            rule._checkingKey = checkKey;
            try {
                await $.ajax({
                    type: 'POST',
                    url: <?= json_encode($view->action('test_rule')); ?>,
                    data: $.extend(
                        {
                            <?= json_encode($token::DEFAULT_TOKEN_NAME); ?>: <?= json_encode($token->generate('e404-tr')); ?>,
                        },
                        this.serializeRule(rule),
                    ),
                    dataType: 'json'
                });
                if (rule._checkingKey !== checkKey) {
                    return;
                }
                delete rule._checkingKey;
                rule._checkKey = checkKey;
                rule.error = '';
            } catch (e) {
                if (rule._checkingKey !== checkKey) {
                    return;
                }
                delete rule._checkingKey;
                rule._checkKey = checkKey;
                if (e?.responseJSON?.errors?.length) {
                    rule.error = e?.responseJSON.errors.join('\n');
                } else {
                    rule.error = e?.responseText || e?.message || <?= json_encode(t('Unknown error')) ?>;
                }
            }
        },
        addRule() {
            this.rules.push(this.unserializeRule({
                type: this.TYPE.CONTAINS,
                text: '',
                delimiter: '#',
                modifiers: this.MODIFIER.CASE_INSENSITIVE,
                error: '',
            }));
        },
        deleteRule(rule) {
            const index = this.rules.indexOf(rule);
            if (index >= 0) {
                this.rules.splice(index, 1);
            }
        },
        async updateTestUrlResult() {
            if (this.test.url === '') {
                this.test.resultClass = 'text-muted';
                this.test.resultText = <?= json_encode(t('Specify a URL to check if the rules match')) ?>;
                return;
            }
            const rules = this.rules.filter(rule => rule.text !== '');
            if (rules.length === 0) {
                this.test.resultClass = 'text-muted';
                this.test.resultText = <?= json_encode(t('Specify at least one rule')) ?>;
                return;
            }
            const checkKey = [this.test.url, ...rules.map(rule => rule._checkKey)].join('\x03');
            if (this.test._checkKey === checkKey) {
                return;
            }
            this.test.resultClass = 'text-muted';
            this.test.resultText = '...';
            try {
                const response = await $.ajax({
                    type: 'POST',
                    url: <?= json_encode($view->action('test_url')); ?>,
                    data: {
                        <?= json_encode($token::DEFAULT_TOKEN_NAME); ?>: <?= json_encode($token->generate('e404-tu')); ?>,
                        rules: rules.map(rule => this.serializeRule(rule)),
                        url: this.test.url,
                    },
                    dataType: 'json'
                });
                console.log(response);
                this.test.resultClass = response.class;
                this.test.resultText = response.text;
            } catch (e) {
                this.test.resultClass = 'text-danger';
                if (e?.responseJSON?.errors?.length) {
                    this.test.resultText =e?.responseJSON.errors.join('\n');
                } else {
                    this.test.resultText =e?.responseText || e?.message || <?= json_encode(t('Unknown error')) ?>;
                }
            }
        },
        async save() {
            if (this.busy) {
                return;
            }
            const someErrors = this.rules.some((rule) => {
                if (!rule.error) {
                    return false;
                }
                ConcreteAlert.error({message: <?= json_encode(t('Please fix the errors')) ?>});
                return true;
            });
            if (someErrors) {
                return;
            }
            this.busy = true;
            try {
                await $.ajax({
                    type: 'POST',
                    url: <?= json_encode($view->action('save')); ?>,
                    data: {
                        <?= json_encode($token::DEFAULT_TOKEN_NAME); ?>: <?= json_encode($token->generate('e404-s')); ?>,
                        rules: this.rules
                            .filter(rule => rule.text !== '')
                            .map(rule => this.serializeRule(rule))
                        ,
                    },
                    dataType: 'json'
                });
                this.askBeforeUnload = false;
                window.location.reload();
            } catch (e) {
                this.busy = false;
                let error;
                if (e?.responseJSON?.errors?.length) {
                    error = e?.responseJSON.errors.join('\n');
                } else {
                    error = e?.responseText || e?.message || <?= json_encode(t('Unknown error')) ?>;
                }
                ConcreteAlert.error({message: error, plainTextMessage: true});
            }
        },
    },
    watch: {
        rules: {
            deep: true,
            handler() {
                this.checkEmptyRules();
                this.rules.forEach(rule => {
                    if (rule.text === '') {
                        this.checkRule(rule);
                    }
                });
                if (this.rulesWatchTimer) {
                    clearTimeout(this.rulesWatchTimer);
                }
                this.rulesWatchTimer = setTimeout(
                    () => {
                        clearTimeout(this.rulesWatchTimer);
                        this.rulesWatchTimer = null;
                        this.checkRules();
                        this.updateTestUrlResult();
                    },
                    this.WATCH_DELAY
                );
            }
        },
        'test.url'() {
            if (this.testUrlWatchTimer) {
                clearTimeout(this.testUrlWatchTimer);
            }
            this.testUrlWatchTimer = setTimeout(
                () => {
                    clearTimeout(this.testUrlWatchTimer);
                    this.testUrlWatchTimer = null;
                    this.updateTestUrlResult();
                },
                this.WATCH_DELAY
            );
        },
    },
});

});</script>
