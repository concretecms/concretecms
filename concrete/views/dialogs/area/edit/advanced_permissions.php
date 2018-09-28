<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Core\Permission\Checker $cp */

/* @var Concrete\Core\Area\Area $a */
/* @var Concrete\Core\Permission\Checker $ap */
/* @var Concrete\Core\Validation\CSRF\Token $token */

/* @var Concrete\Core\Permission\Key\Key $pk */
/* @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager */

View::element('permission/detail', ['permissionKey' => $pk]);

?>
<script>
var ccm_permissionDialogURL = <?= json_encode((string) $resolverManager->resolve(['/ccm/system/dialogs/area/edit/advanced_permissions?cID=' . $a->getAreaCollectionObject()->getCollectionID() . '&arHandle=' . urlencode($a->getAreaHandle())])) ?>; 
</script>
