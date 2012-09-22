<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_AttributeType_SocialLinks extends AttributeTypeController  {

	
	public function getServices() {
		$services = array(
			array('Facebook', t('Enter your Facebook username.')),
			array('Twitter', t('Enter your Twitter username.')),
			array('Instagram', t('Enter your Instagram username.')),
			array('Pinterest', t('Enter your Pinterest username.')),
			array('Youtube', t('Enter your Youtube channel or profile URL.')),
			array('Google Plus', t('Enter your Google Plus username.')),
			array('Flickr', t('Enter your Flickr Profile URL.')),
			array('MySpace', t('Enter the full URL of your MySpace profile.')),
			array('Other', t('Enter the full URL of this website.'))
		);
		return $services;	
	}
	
	public function form() {
		$this->addHeaderItem(Loader::helper('html')->javascript("bootstrap.js"));
		$this->set('services', $this->getServices());
	}
}