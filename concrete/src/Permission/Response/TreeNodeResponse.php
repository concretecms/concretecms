<?php
namespace Concrete\Core\Permission\Response;
abstract class TreeNodeResponse extends Response {

	abstract public function canViewTreeNode();
	abstract public function canDeleteTreeNode();
	abstract public function canEditTreeNodePermissions();
	abstract public function canEditTreeNode();
	abstract public function canAddTreeSubNode();

}
