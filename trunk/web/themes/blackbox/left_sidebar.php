<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php'); ?>

<body class="left_sidebar">

	      <?php 
		$a = new Area('Header Nav');
		$a->display($c);
		?>

<div class="main">

	<div class="gfx">
	      <?php 
		$a = new Area('Header');
		$a->display($c);
		?>	
	</div>

	<div class="content">

        	<div class="menu">
	            <?php 
		    $a = new Area('Sidebar');
		    $a->display($c);
		    ?>
	        </div>

		<div class="item">
			    <?php
				$a = new Area('Main');
				$a->display($c);
			    ?>
		</div>

	</div>

<?php $this->inc('elements/footer.php'); ?>


</div>

</body>

</html>