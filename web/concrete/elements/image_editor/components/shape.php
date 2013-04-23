<script class='shape-slideout' type="imageeditor/template">
	<ul class='slideOutBoxList'>
		<?php
		foreach ($fonts as $font) {
			?>
			<li style='font-family:"<?=$font?>"'>
				<?=$font?>
			</li>
			<?php
		}
		?>
	</ul>
</script>
