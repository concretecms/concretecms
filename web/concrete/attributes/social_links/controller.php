<?php
namespace Concrete\Attribute\SocialLinks;
use Loader;
use Environment;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Sharing\SocialNetwork\ServiceList as ServiceList;
use \Concrete\Core\Sharing\SocialNetwork\Service as Service;
use \Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController  {

	
	public function saveForm($data) {
		if (!is_array($data['service'])) {
			$data['service'] = array();
		}
		if (!is_array($data['serviceInfo'])) {
			$data['serviceInfo'] = array();
		}
		for ($i = 0; $i < count($data['service']); $i++) {
			$values[$data['service'][$i]] = $data['serviceInfo'][$i];
		}
		$this->saveValue($values);
	}
	
	public function saveValue($values) {
		$db = Loader::db();
		$db->Execute('delete from atSocialLinks where avID = ?', array($this->getAttributeValueID()));
		foreach($values as $service => $serviceInfo) {
			if ($serviceInfo) {
                $serviceInfo = filter_var($serviceInfo, FILTER_SANITIZE_URL);
				$service = Loader::helper('text')->entities($service);
				$serviceInfo = Loader::helper('text')->entities($serviceInfo);			
				$db->Execute('insert into atSocialLinks (avID, service, serviceInfo) values (?, ?, ?)', array($this->getAttributeValueID(), $service, $serviceInfo));
			}
		}
	}
	
	public function getValue() {
		$db = Loader::db();
		$services = array();
		$r = $db->Execute('select service, serviceInfo from atSocialLinks where avID = ? order by avsID asc', array($this->getAttributeValueID()));
		while ($row = $r->FetchRow()) {
			$services[$row['service']] = $row['serviceInfo'];
		}
		return $services;
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atSocialLinks where avID = ?', array($id));
		}
	}

	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atSocialLinks where avID = ?', array($this->getAttributeValueID()));
	}

	public function getDisplayValue() {
		$html = '';
		$services = $this->getValue();
		if (count($services) > 0) {
			$env = Environment::get();
			$url = $env->getURL(DIRNAME_ATTRIBUTES . '/social_links/view.css');
			$this->addHeaderItem(Loader::helper('html')->css($url));
			$html .=  '<span class="ccm-social-link-attribute-display">';
			foreach($services as $service => $serviceInfo) {
                $serviceObject = Service::getByHandle($service);
                if (is_object($serviceObject)) {
                    $iconHtml = $serviceObject->getServiceIconHTML();
                }
				$html .= '<span class="ccm-social-link-service">';
				$html .=  '<span class="ccm-social-link-service-icon"><a href="' . filter_var($serviceInfo, FILTER_VALIDATE_URL) . '">'.$iconHtml.'</a></span>';
				$html .=  '<span class="ccm-social-link-service-info"><a href="' . filter_var($serviceInfo, FILTER_VALIDATE_URL) . '">' . $serviceObject->getName() . '</a></span>';
				$html .=  '</span>';
			}
			$html .=  '</span>';
		}
		return $html;
	}
	
	public function form() {
		if ($this->isPost()) {
			$data['service'] = $this->post('service');			
			$data['serviceInfo'] = $this->post('serviceInfo');			
		} else {
			$d = $this->getValue();
			foreach($d as $k => $v) {
				$data['service'][] = $k;
				$data['serviceInfo'][] = $v;
			}
		}
		if (!is_array($data['service'])) {
			$data['service'][] = 'facebook';
			$data['serviceInfo'][] = '';
		}
		$this->set('data', $data);
		$this->set('services', ServiceList::get());
	}
}