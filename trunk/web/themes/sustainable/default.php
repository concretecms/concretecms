<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<style type="text/css">@import "<?php echo $this->getThemePath()?>/css/global.css";</style>
	<?php  Loader::element('header_required'); ?>	  
</head>

<body>

  <div id="masthead">
    
    <h1><?php $ahmt = new Area('Header Main Text'); $ahmt->display($c);?></h1>
    
    <p><?php $ahst = new Area('Header Sub Text'); $ahst->display($c);?></p>
    
  </div>
  
  <div id="content" class="clearfix">
  
    <div id="main_content">
    
      <div class="body">
    
        <?php $a = new Area('Main'); $a->display($c);?>
    
        <ul class="body_footer">
          <?php $abf = new Area('Body Footer'); $abf->display($c);?>
        </ul>
        
      </div>
    
    </div>
  
    <div id="secondary_content">
  
      <div id="nav">
        <?php $a = new Area('Header Nav'); $a->display($c);?>
      </div>
    
      <div id="search">
 		<?php $ast = new Area('Sidebar Text'); $ast->display($c);?>
      </div>
    
      <div id="secondary_nav">
        <?php $asn = new Area('Secondary Nav'); $asn->display($c);?>        
      </div>
    
    </div>
  
  </div>
  
  <ul id="footer">
	<?php echo date('Y')?> <a href="<?php echo DIR_REL?>/"><?php echo SITE?></a>&nbsp;|&nbsp;<?php echo t('All rights reserved.')?>&nbsp;|&nbsp;
    <span class="sign-in"><a href="<?php echo $this->url('/login')?>"><?php echo t('Sign In to Edit this Site')?></a></span>&nbsp;|&nbsp;
	<a href="http://validator.w3.org/check/referer">Valid XHTML</a>&nbsp;|&nbsp;<a href="http://gmpg.org/xfn/">XFN</a>
  </ul>

</body>
</html>
