<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByPath('/dashboard/blocks/types');
$cp = new Permissions($c);
if ($cp->canViewPage()) { 
	Loader::element('permission/details/block_type');
}
