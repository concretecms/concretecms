<?php
namespace Concrete\Attribute\SocialLinks;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Type\SocialLinksType;
use Concrete\Core\Entity\Attribute\Value\Value\SelectedSocialLink;
use Concrete\Core\Entity\Attribute\Value\Value\SocialLinksValue;
use Loader;
use Environment;
use Concrete\Core\Sharing\SocialNetwork\ServiceList as ServiceList;
use Concrete\Core\Sharing\SocialNetwork\Service as Service;
use Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('share');
    }

    public function getSearchIndexValue()
    {
        return false;
    }


    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        $values = array();
        if (!is_array($data['service'])) {
            $data['service'] = array();
        }
        if (!is_array($data['serviceInfo'])) {
            $data['serviceInfo'] = array();
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
        $services = $this->getValue();
        foreach ($services as $service => $serviceInfo) {
            $av = $akn->addChild('link');
            $av->addAttribute('service', $service);
            $av->addAttribute('detail', $serviceInfo);
        }
    }

    public function getDisplayValue()
    {
        $html = '';
        $services = $this->getValue()->getSelectedLinks();
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

    public function form()
    {
        $data = array();
        if ($this->isPost()) {
            $data['service'] = $this->post('service');
            $data['serviceInfo'] = $this->post('serviceInfo');
        } else {
            if (is_object($this->attributeValue)) {
                $links = $this->attributeValue->getValue()->getSelectedLinks();
                foreach($links as $link) {
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

}
