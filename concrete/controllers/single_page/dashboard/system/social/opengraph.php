<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Social;

use Concrete\Attribute\ImageFile\Controller as ImageFileController;
use Concrete\Attribute\Text\Controller as TextController;
use Concrete\Attribute\Textarea\Controller as TextareaController;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Page\Controller\DashboardSitePageController;

class Opengraph extends DashboardSitePageController
{

    protected function saveConfigValue($config, $requestField, $configField)
    {
        $ogField = explode(':', $this->request->get($requestField));
        if (!empty($ogField[0]) && !empty($ogField[1])) {
            $ogFieldValue = [
                'value_from' => $ogField[0]
            ];
            $config->save('social.opengraph.' . $configField . '.value_from', $ogField[0]);
            if ($ogField[0] === 'value') {
                $ogFieldValue['value'] = $ogField[1];
            } elseif ($ogField[0] === 'page_property') {
                $ogFieldValue['property'] = $ogField[1];
            } elseif ($ogField[0] === 'page_attribute') {
                $ogFieldValue['attribute'] = $ogField[1];
            }
            $config->save('social.opengraph.' . $configField, $ogFieldValue);
        }
    }

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = $this->site->getConfigRepository();
            if ($this->request->request->get('enableOpengraph')) {
                $config->save('social.opengraph.enabled', true);
            } else {
                $config->save('social.opengraph.enabled', false);
            }
            $config->save('social.opengraph.field_fb_app_id', $this->request->get('fbAppId'));
            $this->saveConfigValue($config, 'ogType', 'field_og_type');
            $this->saveConfigValue($config, 'ogTitle', 'field_og_title');
            $this->saveConfigValue($config, 'ogDescription', 'field_og_description');
            $this->saveConfigValue($config, 'ogThumbnail', 'field_og_thumbnail');
            $this->flash('success', t('OpenGraph settings saved successfully.'));
            return $this->buildRedirect($this->action('view'));
        }
        $this->view();
    }

    public function view()
    {
        $opengraphConfig = $this->site->getConfigRepository()->get('social.opengraph');
        $pageAttributes = CollectionKey::getList();
        $textAttributes = [];
        $imageFileAttributes = [];
        $textareaAttributes = [];
        foreach ($pageAttributes as $key) {
            $controller = $key->getController();
            if ($controller instanceof TextController) {
                $textAttributes[$key->getAttributeKeyHandle()] = $key->getAttributeKeyDisplayName('text');
            } elseif ($controller instanceof ImageFileController) {
                $imageFileAttributes[$key->getAttributeKeyHandle()] = $key->getAttributeKeyDisplayName('text');
            } elseif ($controller instanceof TextareaController) {
                $textareaAttributes[$key->getAttributeKeyHandle()] = $key->getAttributeKeyDisplayName('text');
            }
        }
        $this->set('opengraphConfig', $opengraphConfig);
        $this->set('textAttributes', $textAttributes);
        $this->set('imageFileAttributes', $imageFileAttributes);
        $this->set('textareaAttributes', $textareaAttributes);
    }

}
