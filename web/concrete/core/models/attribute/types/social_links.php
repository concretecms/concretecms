<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_AttributeType_SocialLinks extends AttributeTypeController  {

	
	public function getServices() {
		$services = array(
			array('facebook', 'Facebook', t('Enter your Facebook username.'), 'http://facebook.com/'),
			array('twitter', 'Twitter', t('Enter your Twitter username.'), 'http://twitter.com/'),
			/* array('instagram', 'Instagram', t('Enter your Instagram username.')), */
			array('pinterest', 'Pinterest', t('Enter your Pinterest username.'), 'http://pinterest.com/'),
			array('youtube', 'Youtube', t('Enter your Youtube channel or profile URL.'), ''),
			array('gplus', 'Google Plus', t('Enter your Google Plus profile URL.'), ''),
			array('flickr', 'Flickr', t('Enter your Flickr Profile URL.'), ''),
			array('myspace', 'MySpace', t('Enter the full URL of your MySpace profile.'), ''),
			array('wthree', 'Other', t('Enter the full URL of this website.'), '')
		);
		return $services;	
	}
	
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
			$service = Loader::helper('text')->entities($service);
			$serviceInfo = Loader::helper('text')->entities($serviceInfo);			
			$db->Execute('insert into atSocialLinks (avID, service, serviceInfo) values (?, ?, ?)', array($this->getAttributeValueID(), $service, $serviceInfo));
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
	
	protected function getServiceLink($service, $serviceInfo) {
		$link = '';
		$services = $this->getServices();
		foreach($services as $s) {
			if ($s[0] == $service) {
				$link = $s[3];
			}
		}
		$link .= $serviceInfo;
		return $link;
	}
	
	protected function getServiceName($service, $serviceInfo) {
		$services = $this->getServices();
		foreach($services as $s) {
			if ($s[0] == $service) {
				return $s[1];
			}
		}
	}
	
	public function getDisplayValue() {
		$services = $this->getValue();
		if (count($services) == 0) {
			print t('None');
		} else {
			$this->addHeaderItem(Loader::helper('html')->css('ccm.social.networks.css'));
			print '<table class="ccm-social-link-attribute-display socialicons">';
			foreach($services as $service => $serviceInfo) {
				print '<tr>';
				$icon = $service;
				print '<td class="ccm-social-link-service-icon"><a class="color" href="' . $this->getServiceLink($service, $serviceInfo) . '"><i class="' . $icon . '"></i></a></td>';
				print '<td><a href="' . $this->getServiceLink($service, $serviceInfo) . '">' . $this->getServiceName($service, $serviceInfo) . '</a><td>';
				print '</tr>';
			}
			print '</table>';		
		}
	}
	
	public function form() {
		$this->addHeaderItem(Loader::helper('html')->javascript("bootstrap.js"));
		if ($this->isPost()) {
			$data['service'] = $this->post('service');			
			$data['serviceInfo'] = $this->post('serviceInfo');			
		} else {
			$d = $this->getValue();
			$data['service'] = array();
			$data['serviceInfo'] = array();
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
		$this->set('services', $this->getServices());
	}
}