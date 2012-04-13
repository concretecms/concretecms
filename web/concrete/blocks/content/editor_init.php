<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $th = $c->getCollectionThemeObject(); ?>
<? $this->inc('editor_config.php', array('theme' => $th)); ?> 
<? Loader::element('editor_controls'); ?>