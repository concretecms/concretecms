<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>

<body class="view">

<div id="paper_left">
<div id="paper_right">
<div id="layout_wrapper">
<div id="layout_container">
<div id="layout_content">

	<div id="site_title">
		<h1><a href="<?php echo DIR_REL?>/"><?php echo SITE?></a></h1>
	      <?php 
		$a = new Area('Header Nav');
		$a->display($c);
	      ?>
	</div>

	<div id="header_image">
	    <?php 			
		$ah = new Area('Header');
		$ah->display($c);			
	    ?>
	</div>

	<div class="navigation" id="subnav">

	      <?php 
		$a = new Area('Sidebar');
		$a->display($c);
		?>

		<div class="clearer">&nbsp;</div>

	</div>

	<div id="main">
		
		<div class="post">

			<?php 

			print $innerContent;
			
			?>
      
		</div>

	</div>

	<?php $this->inc('elements/footer.php'); ?>

</div>
</div>
</div>
</div>
</div>

</body>
</html>