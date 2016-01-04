<?php
namespace Concrete\Core\Attribute;

interface AttributeInterface
{

	public function getAttributeKey();
	public function setAttributeKey(AttributeKeyInterface $key);

}
