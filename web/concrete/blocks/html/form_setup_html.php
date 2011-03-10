<? defined('C5_EXECUTE') or die("Access Denied."); ?>  

<h2><?=t('Add your HTML below:')?></h2>

<div style="text-align: center" >
<textarea id="ccm-HtmlContent" name="content" style="width:98%; margin:auto; height:410px;"><?=htmlentities($controllerObj->content, ENT_COMPAT, APP_CHARSET) ?></textarea>
</div>