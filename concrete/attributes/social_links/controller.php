<?php

namespace Concrete\Attribute\SocialLinks;

use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\Form\Control\View\GroupedView;
use Concrete\Core\Attribute\SimpleTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Value\Value\SelectedSocialLink;
use Concrete\Core\Entity\Attribute\Value\Value\SocialLinksValue;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Sharing\SocialNetwork\Service as Service;
use Concrete\Core\Sharing\SocialNetwork\ServiceList as ServiceList;
use Environment;
use Loader;

class Controller extends AttributeTypeController implements SimpleTextExportableAttributeInterface
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
        $data = $this->post();
        $values = [];
        if (!is_array($data['service'])) {
            $data['service'] = [];
        }
        if (!is_array($data['serviceInfo'])) {
            $data['serviceInfo'] = [];
        }
        for ($i = 0; $i < count($data['service']); ++$i) {
            $values[$data['service'][$i]] = $data['serviceInfo'][$i];
        }

        return $this->createAttributeValue($values);
    }

    public function createAttributeValue($values)
    {
        $av = new SocialLinksValue();

        foreach ($values as $service => $serviceInfo) {
            if ($serviceInfo) {
                $serviceInfo = filter_var($serviceInfo, FILTER_SANITIZE_URL);
                $service = $this->app->make('helper/text')->entities($service);
                $serviceInfo = $this->app->make('helper/text')->entities($serviceInfo);
                $link = new SelectedSocialLink();
                $link->setService($service);
                $link->setServiceInfo($serviceInfo);
                $link->setAttributeValue($av);
                $av->getSelectedLinks()->add($link);
            }
        }

        return $av;
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        $services = $this->attributeValue->getValue()->getSelectedLinks();
        foreach ($services as $link) {
            $av = $akn->addChild('link');
            $av->addAttribute('service', $link->getService());
            $av->addAttribute('detail', $link->getServiceInfo());
        }
    }

    public function getDisplayValue()
    {
        $html = '';
        $services = $this->attributeValue->getValue()->getSelectedLinks();
        if (count($services) > 0) {
            $env = Environment::get();
            $url = $env->getURL(DIRNAME_ATTRIBUTES . '/social_links/view.css');
            $this->addHeaderItem(Loader::helper('html')->css($url));
            $html .= '<span class="ccm-social-link-attribute-display">';
            foreach ($services as $link) {
                $serviceObject = Service::getByHandle($link->getService());
                if (is_object($serviceObject)) {
                    $iconHtml = $serviceObject->getServiceIconHTML();
                }
                $html .= '<span class="ccm-social-link-service">';
                $html .= '<span class="ccm-social-link-service-icon"><a href="' . filter_var($link->getServiceInfo(),
                        FILTER_VALIDATE_URL) . '">' . $iconHtml . '</a></span>';
                $html .= '<span class="ccm-social-link-service-info"><a href="' . filter_var($link->getServiceInfo(),
                        FILTER_VALIDATE_URL) . '">' . $serviceObject->getName() . '</a></span>';
                $html .= '</span>';
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
        if ($this->isPost()) {
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
        if (!isset($data['service'])) {
            $data['service'][] = 'facebook';
            $data['serviceInfo'][] = '';
        }
        $this->set('data', $data);
        $this->set('services', ServiceList::get());
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
}
