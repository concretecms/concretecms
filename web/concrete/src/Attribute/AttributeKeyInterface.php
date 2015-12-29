<?php
namespace Concrete\Core\Attribute;

interface AttributeKeyInterface
{

	public function getAttributeKeyID();
	public function getAttributeKeyHandle();
	public function getAttributeType();
	public function isAttributeKeySearchable();

	/**
	 * @return Controller
	 */
	public function getController();

}
