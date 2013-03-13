<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="en">
<head>

<? 
$this->addHeaderItem(Loader::helper('html')->css($this->getThemePath() . '/bootstrap.css'));
//$this->addFooterItem(Loader::helper('html')->javascript($this->getThemePath() . '/bootstrap.min.js'));

Loader::element('header_required'); ?>
	
</head>
<body>

<div style="height: 200px"></div>