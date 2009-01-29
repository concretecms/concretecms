<?php
	if (@mysql_connect("localhost", "pma", "")) {
		echo "OK";
	} else {
		echo "NOK";
	}
?>
