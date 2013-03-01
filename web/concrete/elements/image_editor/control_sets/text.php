<textarea class='text'></textarea>
<div class="btn-group" style='width:100%'>
  <button class="btn fontname" type="button">Open Sans</button>
  <button class="btn color" type="button">&nbsp;</button>
</div>
<br>
<br>
<div class="btn-group alignment" data-toggle="buttons-radio">
  <button class="btn active" type="button" data-alignment='left'><i class='icon-align-left'> </i></button>
  <button class="btn" type="button" data-alignment='center'><i class='icon-align-center'> </i></button>
  <button class="btn" type="button" data-alignment='right'><i class='icon-align-right'> </i></button>
</div>
<div class="btn-group style" data-toggle="buttons-checkbox">
  <button class="btn" type="button" data-style="bold"><strong>B</strong></button>
  <button class="btn" type="button" data-style="italic"><i>I</i></button>
</div>
<?php
$fonts = array(
	"Open Sans",
	"Roboto Condensed",
	"Patrick Hand SC",
	"Sintony",
	"Tauri",
	"Skranji",
	"Josefin Slab",
	"Arvo",
	"Lato",
	"Vollkorn",
	"Abril Fatface",
	"Ubuntu",
	"PT Serif",
	"PT Sans",
	"Old Standard TT",
	"Droid Sans",
	"Prociono",
	"Oleo Script Swash Caps"
);
$fontsArg = str_replace(' ','+',implode(':400,700,400italic,700italic|',$fonts));
?>
<link href='//fonts.googleapis.com/css?family=<?=$fontsArg?>' rel='stylesheet' type='text/css'>
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

<div class='settingslider sizeSlider'>
	<span>Size</span><br>
	<div class='slider'></div>
	<input>
</div>
<div class='settingslider lineHeightSlider'>
	<span>Line Height</span><br>
	<div class='slider'></div>
	<input>
</div>