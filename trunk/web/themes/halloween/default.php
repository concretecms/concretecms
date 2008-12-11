<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<link rel="stylesheet" type="text/css" href="<?=$this->getThemePath()?>/default.css" media="screen"/>
<link rel="stylesheet" type="text/css" href="<?=$this->getThemePath()?>/typography.css" media="screen"/>
<? Loader::element('header_required'); ?>
</head>

<body>

<div id="container">

<div id="content">

	<h1 id="site-title"><a href="<?=$this->url('/')?>"><?=SITE?></a></h1>

	<? $a = new Area('Main'); $a->display($c); ?>

	<div id="footer">

		<div class="left">&copy; 2007 <a href="index.html">Website.com</a>. Valid <a href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a> &amp; <a href="http://validator.w3.org/check?uri=referer">XHTML</a></div>

		<div class="right">Design by <a href="http://arcsin.se/">Arcsin</a> <a href="http://templates.arcsin.se/">Web Templates</a>. <a href="<?=$this->url('/login')?>"><?=t('Sign In to Edit this Site')?></a>.</div>

		<div class="clearer"><span></span></div>

	</div>

</div>

<div id="navigation">

	<? $a = new Area('Header Nav'); $a->display($c); ?>

	<? $a = new Area('Sidebar'); $a->display($c); ?>

</div>

</div>

</body>

</html>