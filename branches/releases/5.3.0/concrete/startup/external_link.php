<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	if ($c->getCollectionPointerExternalLink() != '' && (!$_POST['processCollection'])) {
		$db = Loader::db();
		$db->disconnect();
		header('Location: ' . $c->getCollectionPointerExternalLink());
		exit;
	}
