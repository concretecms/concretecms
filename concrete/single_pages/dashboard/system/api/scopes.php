<?php

use Concrete\Core\Entity\OAuth\Scope;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Api\Scopes $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\View\PageView $view
 *
 * @var Concrete\Core\Entity\OAuth\Scope[] $scopes
 */
?>

<form method="post" action="<?= h((string) $controller->action('synchronize')) ?>">
    <?php $token->output('synchronize') ?>
    <div class="ccm-dashboard-header-buttons">
        <button class="btn btn-secondary" type="submit" name="action" value="reload"><?= t('Synchronize Scopes') ?></button>
    </div>
</form>

<p><?= t('The following API scopes are installed and available to integrations in your site.') ?></p>

<div id="ccm-app" v-cloak>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><button title="<?= t('Copy All') ?>" type="button" class="btn btn-sm btn-light user-select-none me-1" @click.prevent="copyIdentifiers($event.currentTarget)">&#x2BBB;</button><?= t('Scope') ?></th>
                <th><?= t('Description') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="s in scopes" :key="s.identifier">
                <td class="w-25"><button title="<?= t('Copy') ?>" type="button" class="btn btn-sm btn-light user-select-none me-1" @click.prevent="copyIdentifier(s, $event.currentTarget)">&#x2BBB;</button><code>{{ s.identifier }}</code></td>
                <td class="w-75">{{ s.description }}</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
$(function() {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: '#ccm-app',
            data() {
                return {
                    scopes: <?= json_encode(array_map(
                        static function (Scope $scope): array {
                            return [
                                'identifier' => $scope->getIdentifier(),
                                'description' => t($scope->getDescription()),
                            ];
                        },
                        $scopes
                    )) ?>,
                };
            },
            methods: {
                async copyText(text) {
                    if (window.navigator?.clipboard?.writeText) {
                        navigator.clipboard.writeText(text);
                    } else if (document.execCommand) {
                        const ta = document.createElement('textarea');
                        ta.value = text;
                        ta.style.position = 'fixed';
                        ta.style.left = '0';
                        ta.style.top = '0';
                        ta.style.width = '1px';
                        ta.style.height = '1px';
                        document.body.appendChild(ta);
                        try {
                            ta.focus();
                            ta.select();
                            if (!document.execCommand('copy')) {
                                throw new Error('execCommand() failed');
                            }
                        } finally {
                            document.body.removeChild(ta);
                        }
                    } else {
                        throw new Error('Not available');
                    }
                },
                setCopyButtonClasses(btn, success) {
                    if (!btn) {
                        return;
                    }
                    btn.classList.toggle('btn-light', typeof success !== 'boolean');
                    btn.classList.toggle('btn-success', success === true);
                    btn.classList.toggle('btn-danger', success === false);
                },
                async copyIdentifiers(btn) {
                    const text = this.scopes.map(s => s.identifier).join(' ');
                    this.setCopyButtonClasses(btn);
                    try {
                        await this.copyText(text);
                        this.setCopyButtonClasses(btn, true);
                    } catch (e) {
                        console.error(e);
                        this.setCopyButtonClasses(btn, false);
                    }
                    setTimeout(() => this.setCopyButtonClasses(btn), 500);
                },
                async copyIdentifier(scope, btn) {
                    this.setCopyButtonClasses(btn);
                    try {
                        await this.copyText(scope.identifier);
                        this.setCopyButtonClasses(btn, true);
                    } catch (e) {
                        console.error(e);
                        this.setCopyButtonClasses(btn, false);
                    }
                    setTimeout(() => this.setCopyButtonClasses(btn), 500);
                },
            },
        });
    });
});
</script>
