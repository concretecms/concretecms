<?php

###############################################################
# cPanel WHM Account Creator 1.0
###############################################################
# Visit http://www.zubrag.com/scripts/ for updates
###############################################################
# Required parameters:
# - domain - new account domain
# - user - new account username
# - password - new account password
# - package - new account hosting package (plan)
#
# Sample run: create-whm-account.php?domain=reseller.com&user=hosting&password=manager&package=unix_500
#
# If no parameters passed then input form will be shown to enter data.
#
# This script can also be run from another PHP script. This may
# be helpful if you have some user interface already in place and 
# want to automatically create WHM accounts from there.
# In this case you have to setup following variables instead of
# passing them as parameters:
# - $user_domain - new account domain
# - $user_name - new account username
# - $user_pass - new account password
# - $user_plan - new account hosting package (plan)
#
###############################################################

///////  YOUR WHM LOGIN DATA
$whm_user   = "root";      // reseller username
$whm_host   = "localhost"; // your WHM host (usually localhost)
$whm_usessl = 0;           // set to 1 if you are able to run this PHP page via https
                           // set to 0 if you are not able to run this PHP page via https

// Add below your cPanel access hash (key to tell cPanel you do have rights to run this script)
// You should be able to get this key from your WHM account (Setup Remote Access Key option)
// Please note each key line starts with dot.  You can add as many lines as you need according
// to your cPanel WHM key length. Line lenght is not important.
$whm_key = ''
// ---BEGIN CPANEL WHM ACCESS KEY---
. 'c3ccw1fccfd8e4609766838f59a831c1'
. 'cdsgsdfecfd8e460976s838f59a831c2'
. 'dfgdfdfccfd8e460976f838f59a831c3'
. 'c3cdfsdfcfd8e4a09767a38f59a831c4'
. 'kjljkwfccfd8e4609767838f59a831c5'
// ---END CPANEL WHM ACCESS KEY---
;

// Path to cPanel WHM interface functions (Accounting.php.inc)
// Most likely you will not need to change below parameter.
// If script cannot find it ask your hoster for file location.
$whm_interface_path = '/usr/local/cpanel/Cpanel/Accounting.php.inc';

#####################################################################################
##############          END OF SETTINGS. DO NOT EDIT BELOW    #######################
#####################################################################################

function getVar($name, $def = '') {
  if (isset($_REQUEST[$name]))
    return $_REQUEST[$name];
  else
    return $def;
}

// Domain name of new hosting account
// To create subdomain just pass full subdomain name
// Example: newuser.zubrag.com
if (!isset($user_domain)) {
  $user_domain = getVar('domain');
}

// Username of the new hosting account
if (!isset($user_name)) {
  $user_name = getVar('user');
}

// Password for the new hosting account
if (!isset($user_pass)) {
  $user_pass = getVar('password');
}

// New hosting account Package
if (!isset($user_plan)) {
  $user_plan = getVar('package');
}

if (!file_exists($whm_interface_path)) {
  die($whm_interface_path . " does not exist. Please update program with correct path to your WHM interface file.");
}

// if parameters passed then create account
if (!empty($user_name)) {
  // load cPanel WHM interface functions
  require_once $whm_interface_path;

  // create account on the cPanel server
  $result = createacct ($whm_host,$whm_user,$whm_key,$whm_usessl,$user_domain,$user_name,$user_pass,$user_plan);

  // output result
  echo "RESULT: " . $result;
}
// otherwise show input form
else {
$frm = <<<EOD
<html>
<head>
  <title>cPanel/WHM Account Creator</title>
  <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
  <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
</head>
<body>
  <style>
    input { border: 1px solid black; }
  </style>
<form method="post">
<h3>cPanel/WHM Account Creator</h3>
<table border="0">
<tr><td>Domain:</td><td><input name="domain" size="30"></td><td>Subdomain or domain, without www</td></tr>
<tr><td>Username:</td><td><input name="user" size="30"></td><td>Username to be created</td></tr>
<tr><td>Password:</td><td><input name="password" size="30"></td><td></td></tr>
<tr><td>Package:</td><td><input name="package" size="30"></td><td>Package (hosting plan) name. Make sure you cpecify existing package</td></tr>
<tr><td colspan="3"><br /><input type="submit" value="Create Account"></td></tr>
</table>
</form>
</body>
</html>
EOD;
echo $frm;
}

?>