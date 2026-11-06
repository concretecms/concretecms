<?php

namespace Concrete\Attribute\SocialLinks;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\Attribute\OpenApiSpecifiableInterface;
use Concrete\Core\Api\Attribute\SupportsAttributeValueFromJsonInterface;
use Concrete\Core\Api\Fractal\Transformer\SocialLinkTransformer;
use Concrete\Core\Api\OpenApi\SpecProperty;
use Concrete\Core\Api\OpenApi\SpecPropertyRef;
use Concrete\Core\Api\Resources;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\Form\Control\View\GroupedView;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\Value\SelectedSocialLink;
use Concrete\Core\Entity\Attribute\Value\Value\SocialLinksValue;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Sharing\SocialNetwork\LinkUrlValidator;
use Concrete\Core\Sharing\SocialNetwork\Service as Service;
use Concrete\Core\Sharing\SocialNetwork\ServiceList as ServiceList;
use Environment;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceInterface;
use SimpleXMLElement;

class Controller extends AttributeTypeController implements
    SimpleTextExportableAttributeInterface,
    OpenApiSpecifiableInterface,
    SupportsAttributeValueFromJsonInterface,
    ApiResourceValueInterface
{
    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('share');
    }

    public function getSearchIndexValue()
    {
        return false;
    }

    public function getAttributeValueClass()
    {
        return SocialLinksValue::class;
    }

    public function createAttributeValueFromRequest()
    {
        return $this->createAttributeValue($this->getLinksFromRequestData($this->post()));
    }

    public function validateForm($data)
    {
        $links = $this->getLinksFromRequestData($data);
        if ($links === []) {
            return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
        }
        $validator = $this->app->make(LinkUrlValidator::class);
        foreach ($links as $url) {
            if (!$validator->isValid($url)) {
                return new Error($validator->getInvalidUrlMessage($url), new AttributeField($this->getAttributeKey()));
            }
        }

        return true;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException if a link is not a valid URL
     */
    public function createAttributeValue($values)
    {
        $av = new SocialLinksValue();
        if (is_iterable($values)) {
            $textHelper = $this->app->make('helper/text');
            $validator = $this->app->make(LinkUrlValidator::class);
            foreach ($values as $service => $serviceInfo) {
                if (!$serviceInfo) {
                    continue;
                }
                if (!$validator->isValid($serviceInfo)) {
                    throw new UserMessageException($validator->getInvalidUrlMessage($serviceInfo));
                }
                $service = $textHelper->entities($service);
                $link = new SelectedSocialLink();
                $link->setService($service);
                $link->setServiceInfo($serviceInfo);
                $link->setAttributeValue($av);
                $av->getSelectedLinks()->add($link);
            }
        }

        return $av;
    }

    public function exportValue(SimpleXMLElement $akn)
    {
        $services = $this->attributeValue->getValue()->getSelectedLinks();
        foreach ($services as $link) {
            $av = $akn->addChild('link');
            $av->addAttribute('service', $link->getService());
            $av->addAttribute('detail', $link->getServiceInfo());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Controller::importValue()
     */
    public function importValue(SimpleXMLElement $akn)
    {
        $result = [];
        if (isset($akn->link)) {
            $validator = $this->app->make(LinkUrlValidator::class);
            foreach ($akn->link as $xLink) {
                $service = trim((string) $xLink['service']);
                // Versions up to 9.5.5 exported the URL HTML-encoded
                $url = html_entity_decode((string) $xLink['detail'], ENT_QUOTES, 'UTF-8');
                if ($service === '' || !$validator->isValid($url)) {
                    continue;
                }
                $result[$service] = $url;
            }
        }

        return $result;
    }

    public function getDisplayValue()
    {
        $html = '';
        $services = $this->attributeValue->getValue()->getSelectedLinks();
        if (count($services) > 0) {
            $env = Environment::get();
            $url = $env->getURL(DIRNAME_ATTRIBUTES . '/social_links/view.css');
            $this->addHeaderItem($this->app->make('helper/html')->css($url));
            $html .= '<span class="ccm-social-link-attribute-display">';
            foreach ($services as $link) {
                $serviceObject = Service::getByHandle($link->getService());
                if (!is_object($serviceObject)) {
                    continue;
                }
                $href = h($link->getServiceInfo());
                $icon = $serviceObject->getServiceIconHTML();
                $name = $serviceObject->getDisplayName();
                $html .= <<<EOT
                    <span class="ccm-social-link-service">
                        <span class="ccm-social-link-service-icon"><a href="{$href}">{$icon}</a></span>
                        <span class="ccm-social-link-service-info"><a href="{$href}">{$name}</a></span>
                    </span>
                    EOT;
            }
            $html .= '</span>';
        }

        return $html;
    }

    public function getControlView(\Concrete\Core\Form\Context\ContextInterface $context)
    {
        return new GroupedView($context, $this->getAttributeKey(), $this->getAttributeValue());
    }

    public function form()
    {
        $data = [];
        if ($this->request->isPost()) {
            $data['service'] = $this->post('service');
            $data['serviceInfo'] = $this->post('serviceInfo');
        } else {
            if (is_object($this->attributeValue)) {
                $links = $this->attributeValue->getValue()->getSelectedLinks();
                foreach ($links as $link) {
                    $data['service'][] = $link->getService();
                    $data['serviceInfo'][] = $link->getServiceInfo();
                }
            }
        }
        $services = ServiceList::get();
        if (!is_array($data['service'] ?? null) || !is_string($data['service'][0] ?? null) || $data['service'][0] === '') {
            $data['service'] = [$services[0]->getHandle()];
            $data['serviceInfo'] = [''];
        } elseif (!is_array($data['serviceInfo'] ?? null)) {
            $data['serviceInfo'] = [];
        }
        $this->set('data', $data);
        $this->set('services', $services);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::getAttributeValueTextRepresentation()
     */
    public function getAttributeValueTextRepresentation()
    {
        $links = [];
        $value = $this->getAttributeValueObject();
        if ($value !== null) {
            foreach ($value->getSelectedLinks() as $socialLink) {
                $links[] = $socialLink->getService() . ':' . $socialLink->getServiceInfo();
            }
        }

        return implode("\n", $links);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\SimpleTextExportableAttributeInterface::updateAttributeValueFromTextRepresentation()
     */
    public function updateAttributeValueFromTextRepresentation($textRepresentation, ErrorList $warnings)
    {
        $value = $this->getAttributeValueObject();
        $value->getSelectedLinks()->clear();
        $textRepresentation = trim($textRepresentation);
        if ($textRepresentation === '') {
            if ($value !== null) {
                $value->getSelectedLinks()->clear();
            }
        } else {
            $initialized = false;
            $validator = $this->app->make(LinkUrlValidator::class);
            foreach (explode("\n", $textRepresentation) as $serviceAndInfo) {
                $serviceAndInfo = trim($serviceAndInfo);
                if ($serviceAndInfo === '') {
                    continue;
                }
                $chunks = explode(':', $serviceAndInfo, 2);
                if (!isset($chunks[1])) {
                    $warnings->add(t('"%1$s" is not a valid Social Link value for the attribute with handle %2$s', $serviceAndInfo, $this->attributeKey->getAttributeKeyHandle()));
                    continue;
                }
                $serviceHandle = trim($chunks[0]);
                $serviceInfo = trim($chunks[1]);
                $serviceObject = Service::getByHandle($serviceHandle);
                if ($serviceObject === null) {
                    $warnings->add(t('"%1$s" is not a valid Social Service handle for the attribute with handle %2$s', $serviceHandle, $this->attributeKey->getAttributeKeyHandle()));
                    continue;
                }
                if (!$validator->isValid($serviceInfo)) {
                    $warnings->add(t('"%1$s" is not a valid URL for the attribute with handle %2$s', $serviceInfo, $this->attributeKey->getAttributeKeyHandle()));
                    continue;
                }
                if ($initialized === false) {
                    $initialized = true;
                    if ($value === null) {
                        $value = $this->createAttributeValue([]);
                    } else {
                        $value->getSelectedLinks()->clear();
                    }
                }
                $link = new SelectedSocialLink();
                $link->setAttributeValue($value);
                $link->setService($serviceHandle);
                $link->setServiceInfo($serviceInfo);
                $value->getSelectedLinks()->add($link);
            }
        }

        return $value;
    }

    public function getOpenApiSpecProperty(Key $key): SpecProperty
    {
        return new SpecProperty(
            $key->getAttributeKeyHandle(),
            $key->getAttributeKeyDisplayName(),
            'array',
            null,
            new SpecPropertyRef(
                '/components/schemas/UserSocialLink'
            )
        );
    }

    public function createAttributeValueFromNormalizedJson($json)
    {
        $data = [];
        foreach ((array) $json as $record) {
            $service = $record['service'] ?? null;
            $serviceInfo = $record['service_info'] ?? null;
            if ($service && $serviceInfo) {
                $data[$service] = $serviceInfo;
            }
        }
        return $this->createAttributeValue($data);
    }

    public function getApiValueResource(): ?ResourceInterface
    {
        $value = $this->getAttributeValueObject();
        if ($value) {
            /**
             * @var $value SocialLinksValue
             */
            $links = $value->getSelectedLinks()->toArray();
            return new Collection($links, new SocialLinkTransformer(), Resources::RESOURCE_SOCIAL_LINKS);
        }
        return null;
    }

    /**
     * @param mixed $data the attribute data in the request (the 'service' and 'serviceInfo' lists)
     *
     * @return array service handle => URL
     */
    protected function getLinksFromRequestData($data): array
    {
        $services = is_array($data['service'] ?? null) ? array_values($data['service']) : [];
        $urls = is_array($data['serviceInfo'] ?? null) ? array_values($data['serviceInfo']) : [];
        $result = [];
        foreach ($services as $index => $service) {
            $url = $urls[$index] ?? '';
            if (is_string($service) && is_string($url) && $url !== '') {
                $result[$service] = $url;
            }
        }

        return $result;
    }
}
