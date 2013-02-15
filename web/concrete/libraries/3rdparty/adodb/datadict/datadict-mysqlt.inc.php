<?php

/**
  V5.10 10 Nov 2009   (c) 2000-2009 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
	
  Set tabs to 4 for best viewing.
 
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();
include_once('datadict-mysql.inc.php');
class ADODB2_mysqlt extends ADODB2_mysql {
	var $databaseType = 'mysqlt';
}
?>