<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardSitePageController;

class Urls extends DashboardSitePageController
{
    /**
     * Dashboard page view.
     *
     * @param string|bool $strStatus - Result of attempting to update rewrite rules
     * @param bool $prettyUrlState - Flag denoting if the rewrite rule has been saved
     */
    public function view($strStatus = false, $prettyUrlState = '')
    {
        $globalConfig = $this->app->make('config');
        $siteConfig = $this->getSite()->getConfigRepository();

        $urlRewriting = (bool) $globalConfig->get('concrete.seo.url_rewriting');
        $this->set('fh', $this->app->make('helper/form'));
        $this->set('canonical_url', $siteConfig->get('seo.canonical_url'));
        $this->set('canonical_url_alternative', $siteConfig->get('seo.canonical_url_alternative'));
        $this->set('redirect_to_canonical_url', $globalConfig->get('concrete.seo.redirect_to_canonical_url'));
        $this->set('urlRewriting', $urlRewriting);
        $this->set('canonical_tag', $siteConfig->get('seo.canonical_tag.enabled'));

        $strStatus = (string) $strStatus;
        if ($strStatus === 'saved') {
            $message = t('Settings Saved.');
            $prettyUrlState = (string) $prettyUrlState;
            switch ($prettyUrlState) {
                case 'saved':
                case 'not-needed':
                    $manager = $this->app->make('Concrete\Core\Service\Manager\ServiceManager');
                    $services = $manager->getActiveServices();
                    $service = $services[0];
                    $rule = $service->getGenerator()->getRule('pretty_urls');
                    switch ($prettyUrlState) {
                        case 'saved':
                            if ($urlRewriting) {
                                $this->set('configuration_action', t('The following rule has been added to the server configuration'));
                            } else {
                                $this->set('configuration_action', t('The following rule has been removed from the server configuration'));
                            }
                            break;
                        case 'not-needed':
                            if ($urlRewriting) {
                                $this->set('configuration_action', t('The following rule was already in your server configuration'));
                            } else {
                                $this->set('configuration_action', t('The following rule was already missing in your server configuration'));
                            }
                            break;
                    }
                    $this->set('configuration_code', $rule->getCode());
                    break;
                case 'unrecognized':
                    $codes = array();
                    $manager = $this->app->make('Concrete\Core\Service\Manager\ServiceManager');
                    /* @var \Concrete\Core\Service\Manager\ServiceManager $manager */
                    foreach ($manager->getAllServices() as $service) {
                        $rule = $service->getGenerator()->getRule('pretty_urls');
                        if ($rule !== null) {
                            if (isset($codes[$rule->getCode()])) {
                                $codes[$rule->getCode()][] = $service->getName();
                            } else {
                                $codes[$rule->getCode()] = array($service->getName());
                            }
                        }
                    }
                    $actionMessage = t("It was not possible to detect your server kind.");
                    if ($urlRewriting) {
                        $actionMessage .= ' '.t("Here's the configuration sections for every supported server: please manually add the one relevant for you to your server configuration.");
                    } else {
                        $actionMessage .= ' '.t("Here's the configuration sections for every supported server: please manually remove the one relevant for you from your server configuration.");
                    }
                    $this->set('configuration_action', $actionMessage);
                    $joined = '';
                    foreach ($codes as $code => $serviceNames) {
                        if ($joined !== '') {
                            $joined .= "\n\n";
                        }
                        $joined .= '>>> ' . tc(/*i18n %s is one or more server names */'For server', 'For %s', implode(', ', $serviceNames)) . " <<<\n";
                        $joined .= $code;
                    }
                    $this->set('configuration_code', $joined);
                    break;
                case 'not-readable':
                case 'not-writable':
                    $manager = $this->app->make('Concrete\Core\Service\Manager\ServiceManager');
                    $services = $manager->getActiveServices();
                    $service = $services[0];
                    $rule = $service->getGenerator()->getRule('pretty_urls');
                    $actionMessage = '';
                    switch ($prettyUrlState) {
                        case 'not-readable':
                            $actionMessage .= t('It was not possible read your server configuration.');
                            break;
                        case 'not-writable':
                            $actionMessage .= t('It was not possible write your server configuration.');
                            break;
                    }
                    if ($urlRewriting) {
                        $actionMessage .= ' '.t('Please add this configuration section to your server configuration:');
                    } else {
                        $actionMessage .= ' '.t('Please remove this configuration section from your server configuration');
                    }
                    $this->set('configuration_action', $actionMessage);
                    $this->set('configuration_code', $rule->getCode());
                    break;
            }
            $this->set('message', $message);
        }
    }

    /**
     * Updates the .htaccess file (if writable).
     */
    public function save_urls()
    {
        if ($this->isPost()) {
            if (!$this->token->validate('save_urls')) {
                $this->error->add($this->token->getErrorMessage());
            }

            if ($this->post('canonical_url') &&
                !(
                    strpos(strtolower($this->post('canonical_url')), 'http://') === 0 ||
                    strpos(strtolower($this->post('canonical_url')), 'https://') === 0
                )) {
                $this->error->add(t('The canonical URL provided must start with "http://" or "https://".'));
            }
            if ($this->post('canonical_url_alternative') &&
                !(
                    strpos(strtolower($this->post('canonical_url_alternative')), 'http://') === 0 ||
                    strpos(strtolower($this->post('canonical_url_alternative')), 'https://') === 0
                )) {
                $this->error->add(t('The alternative canonical URL provided must start with "http://" or "https://".'));
            }
            if (!$this->error->has()) {
                $globalConfig = $this->app->make('config');
                $siteConfig = $this->getSite()->getConfigRepository();
                $siteConfig->save('seo.canonical_url', $this->post('canonical_url'));
                $siteConfig->save('seo.canonical_url_alternative', $this->post('canonical_url_alternative'));
                $globalConfig->save('concrete.seo.redirect_to_canonical_url', $this->post('redirect_to_canonical_url') ? 1 : 0);
                $siteConfig->save('seo.canonical_tag.enabled', (bool) $this->post('canonical_tag'));

                $urlRewriting = (bool) $this->post('URL_REWRITING');
                $globalConfig->save('concrete.seo.url_rewriting', $urlRewriting);
                $globalConfig->set('concrete.seo.url_rewriting', $urlRewriting);
                $manager = $this->app->make('Concrete\Core\Service\Manager\ServiceManager');
                /* @var \Concrete\Core\Service\Manager\ServiceManager $manager */
                $prettyUrlState = '';
                $services = $manager->getActiveServices();
                if (empty($services)) {
                    $prettyUrlState = 'unrecognized';
                } else {
                    $service = $services[0];
                    if (!$service->getStorage()->canRead()) {
                        $prettyUrlState = 'not-readable';
                    } else {
                        $rule = $service->getGenerator()->getRule('pretty_urls');
                        if ($rule === null) {
                            $prettyUrlState = 'not-needed';
                        } else {
                            $configuration = $service->getStorage()->read();
                            if ($service->getConfigurator()->hasRule($configuration, $rule) === $urlRewriting) {
                                $prettyUrlState = 'not-needed';
                            } else {
                                if ($service->getStorage()->canWrite() === false) {
                                    $prettyUrlState = 'not-writable';
                                } else {
                                    if ($urlRewriting) {
                                        $configuration = $service->getConfigurator()->addRule($configuration, $rule);
                                    } else {
                                        $configuration = $service->getConfigurator()->removeRule($configuration, $rule);
                                    }
                                    $service->getStorage()->write($configuration);
                                    $prettyUrlState = 'saved';
                                }
                            }
                        }
                    }
                }
                $this->redirect('/dashboard/system/seo/urls', 'saved', $prettyUrlState);
            }
        }
        $this->view();
    }
}
