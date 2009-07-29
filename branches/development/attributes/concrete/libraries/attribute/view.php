<?
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
		
		/** 
		 * Renders a particular view for an attribute
		 */
		public function render($attributeKey, $attributeType, $view, $attributeValue) {
			$this->controller = $attributeType->getController();
			$this->controller->setAttributeKey($attributeKey);
			$this->controller->setAttributeValue($attributeValue);
			$this->attributeValue = $attributeValue;
			$this->attributeKey = $attributeKey;
			$this->controller->setupAndRun($view);
			extract($this->controller->getSets());
			extract($this->controller->getHelperObjects());
			$atHandle = $attributeType->getAttributeTypeHandle();
			
			$this->controller->set('akID', $attributeKey->getAttributeKeyID());
			
			if (file_exists(DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $view . '.php')) {
				$file = DIR_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' . DIRNAME_ATTRIBUTE_TYPES . '/' .  $atHandle . '/' . $view . '.php';
			}
			
			if (!isset($file) && $this->pkgID > 0) {
				$pkgHandle = PackageList::getHandle($pkgID);
				$dirp = is_dir(DIR_PACKAGES . '/' . $pkgHandle) ? DIR_PACKAGES . '/' . $pkgHandle : DIR_PACKAGES_CORE . '/' . $pkgHandle;
				if (file_exists($dirp . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $view . '.php')) {
					$file = $dirp . '/' . $pkgHandle . '/' . DIRNAME_MODELS . '/' . DIRNAME_ATTRIBUTES . '/' .  DIRNAME_ATTRIBUTE_TYPES . '/' . $atHandle . '/' . $view . '.php';
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
		}		
	}