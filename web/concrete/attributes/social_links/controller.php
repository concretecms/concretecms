<?
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
	
	protected function getServiceLink ($serviceInfo) {
		/*
		$link = '';
		$services = ServiceList::get();
		foreach($services as $s) {
			if ($s[0] == $service) {
				$link = $s[3];
			}
		}
		$link .= $serviceInfo;
		return $link;
		*/

		$link = $serviceInfo;
		if (strpos($link, 'http://') === 0 || strpos($link, 'https://') === 0) {
			return $link;
		} else {
			return 'http://' . $link;
		}
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
	
	protected function getServiceName($service, $serviceInfo) {
		$services = ServiceList::get();
		foreach($services as $s) {
			if ($s->getHandle() == $service) {
				if ($service == 'wthree') {
					return $serviceInfo;
				}
				return $s->getName();
			}
		}
	}
	
	public function getDisplayValue() {
		$html = '';
		$services = $this->getValue();
		if (count($services) > 0) {
			$env = Environment::get();
			$url = $env->getURL(DIRNAME_ATTRIBUTES . '/social_links/view.css');
			$this->addHeaderItem(Loader::helper('html')->css($url));
			$html .=  '<div class="ccm-social-link-attribute-display">';
			foreach($services as $service => $serviceInfo) {
                if(is_object(Service::getByHandle($service))) {
                    $iconHtml = Service::getByHandle($service)->getServiceIconHTML();
                }
				$html .= '<div class="ccm-social-link-service">';
				$html .=  '<div class="ccm-social-link-service-icon"><a href="' . $this->getServiceLink($serviceInfo) . '">'.$iconHtml.'</a></div>';
				$html .=  '<div class="ccm-social-link-service-info"><a href="' . $this->getServiceLink($serviceInfo) . '">' . $this->getServiceName($service, $serviceInfo) . '</a></div>';
				$html .=  '</div>';
			}
			$html .=  '</div>';		
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