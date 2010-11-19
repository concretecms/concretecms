<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$req = Request::get();
	if (($req->getRequestCollectionPath() != '') && ($req->getRequestCollectionPath() != $c->getCollectionPath())) {
		// canonnical paths do not match requested path
		header('Location: ' . Loader::helper('navigation')->getLinkToCollection($c, true), true, 301);
		exit;
	}
	if ($c->getCollectionPointerExternalLink() != '' && (!$_POST['processCollection'])) {
		$db = Loader::db();
		$db->disconnect();
		header('Location: ' . $c->getCollectionPointerExternalLink());
		exit;
	}
