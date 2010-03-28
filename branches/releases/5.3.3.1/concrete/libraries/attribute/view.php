<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * An object corresponding to a particular view of an attribute.
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
	class AttributeTypeView extends View {
	
		protected function getValue() {return $this->attributeValue;}
		protected function getAttributeKey() {return $this->attributeKey;}
		
		public function field($fieldName) {
			return $this->controller->field($fieldName);
		}
		
		public function action($action) {
			$uh = Loader::helper('concrete/urls');
			$a = func_get_args();
			$args = '';
			for ($i = 1; $i < count($a); $i++) {
				$args .= '&args[]=' . $a[$i];
			}
			$action = $uh->getToolsURL('attribute_type_actions') . '?atID=' . $this->controller->attributeType->getAttributeTypeID() . '&action=' . $action . $args;
			return $action;
		}
		
		public function getAttributeTypeURL($filename = false) {
			$atHandle = $this->attributeType->getAttributeTypeHandle();
			if (file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $filename)) {
				$url = BASE_URL . DIR_REL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . $filename;
			}
			
			if (!isset($file) && $this->pkgID > 0) {
				$pkgHandle = PackageList::getHandle($pkgID);
				$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
				$rdirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? BASE_URL . DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle : ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle;
				if (file_exists($dirp . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $filename)) {
					$url = $rdirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . $filename;
				}
			}
			
			if (!isset($url)) {
				$url = ASSETS_URL . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $filename;
			}
			
			return $url;
		}
		
		public function __construct($attributeType, $attributeKey, $attributeValue) {
			$this->controller = $attributeType->getController();
			$this->controller->setAttributeKey($attributeKey);
			$this->controller->setAttributeValue($attributeValue);
			$this->attributeValue = $attributeValue;
			$this->attributeKey = $attributeKey;
			$this->attributeType = $attributeType;
		}
		
		/** 
		 * Renders a particular view for an attribute
		 */
		public function render($view, $return = false) {
			
			if ($return) {
				ob_start();
			}
			
			Loader::element(DIRNAME_ATTRIBUTES . '/' . $view . '_header', array('type' => $this->attributeType));
			
			$js = $this->attributeType->getAttributeTypeFileURL($view . '.js');
			$css = $this->attributeType->getAttributeTypeFileURL($view . '.css');
			
			$html = Loader::helper('html');
			if ($js != false) { 
				$this->controller->addHeaderItem($html->javascript($js));
			}
			if ($css != false) { 
				$this->controller->addHeaderItem($html->css($css));
			}

			$this->controller->setupAndRun($view);
			extract($this->controller->getSets());
			extract($this->controller->getHelperObjects());
			$atHandle = $this->attributeType->getAttributeTypeHandle();
			
			if (is_object($attributeKey)) {
				$this->controller->set('akID', $this->attributeKey->getAttributeKeyID());
			}

			if (file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $view . '.php')) {
				$file = DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . $view . '.php';
			}
			
			if (!isset($file) && $this->pkgID > 0) {
				$pkgHandle = PackageList::getHandle($pkgID);
				$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
				if (file_exists($dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $view . '.php')) {
					$file = $dirp . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $view . '.php';
				}
			}
			
			if (!isset($file)) {
				if (file_exists(DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $view . '.php')) {
					$file = DIR_MODELS_CORE . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $view . '.php';
				}
			}
			
			if (isset($file)) {
				include($file);
			}

			Loader::element(DIRNAME_ATTRIBUTES . '/' . $view . '_footer', array('type' => $this->attributeType));
			
			if ($return) {
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}			
		}		
	}