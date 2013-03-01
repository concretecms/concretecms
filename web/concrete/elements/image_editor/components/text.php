<textarea class='text'></textarea>
<div class="btn-group" style='width:100%'>
  <button class="btn fontname" type="button">Roboto Condensed</button>
  <button class="btn color" type="button"></button>
</div>
<br>
<br>
<div class="btn-group" data-toggle="buttons-radio">
  <button class="btn active" type="button"><i class='icon-align-left'> </i></button>
  <button class="btn" type="button"><i class='icon-align-center'> </i></button>
  <button class="btn" type="button"><i class='icon-align-right'> </i></button>
</div>
<div class="btn-group">
  <button class="btn" type="button"><strong>B</strong></button>
  <button class="btn" type="button"><i>I</i></button>
</div>
<?php
$fonts = array(
	"Roboto Condensed",
	"Patrick Hand SC",
	"Sintony",
	"Tauri",
	"Molle",
	"Skranji"
);
$fontsArg = str_replace(' ','+',implode('|',$fonts));
?>
<link href='http://fonts.googleapis.com/css?family=<?=$fontsArg?>' rel='stylesheet' type='text/css'>
<script class='font-slideout' type="imageeditor/template">
	<ul class='slideOutList'>
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