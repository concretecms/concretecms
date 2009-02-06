<?

if ($_GET['refresh'] == 1) {
	$cnt = Loader::controller('/upgrade');
	$cnt->refresh_schema();
}

$db = Loader::db();
$ab = $db->getADOSChema();
$xml = $ab->ExtractSchema();
print '<textarea style="width: 100%; height: 100%">' . $xml . '</textarea>';
