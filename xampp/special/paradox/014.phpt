--TEST--
Update a paradox database
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$fp = fopen($dirname."/px014.db", "w+");
$pxdoc = new paradox_db();
$fields = array(array("col1", "S"), array("col2", "I"));
@$pxdoc->create_fp($fp, $fields);
$pxdoc->set_tablename("testtabelle");
for($i=-3; $i<=3; $i++) {
	$pxdoc->insert_record(array($i, $i));
}
$pxdoc->update_record(array(100, 100), 1);
print_r($pxdoc->retrieve_record(1));
$pxdoc->close();
fclose($fp);
unlink($dirname."/px014.db");
?>
--EXPECT--
Array
(
    [col1] => 100
    [col2] => 100
)

