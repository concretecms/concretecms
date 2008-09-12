<?php 
	if ($c->getCollectionPointerExternalLink() != '' && (!$_POST['processCollection'])) {
		$db = Loader::db();
		$db->disconnect();
		header('Location: ' . $c->getCollectionPointerExternalLink());
		exit;
	}
