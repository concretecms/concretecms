<?php
	      $lang=@file_get_contents("../../lang.tmp");
        @include("../../lang/languages.php");
        @include("../../lang/en.php");
        @include("../../lang/$lang.php");
        if($lang=="zh")
        {
                header("Content-Type: text/html; charset=gb2312");
        }
        else if($lang=="jp")
        {
                header("Content-Type: text/html; charset=shift-jis");
        }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
		<link href="../../xampp.css" rel="stylesheet" type="text/css">
		<title></title>
	</head>

	<body>
		&nbsp;<p>
		<h1><?php echo $TEXT['ps-head']; ?></h1>

<?php
// Some path variables


$curdir = getcwd();
list($xamppdir, $docdir) = split ('xampp\\\\htdocs', $curdir);
$htdir=ereg_replace("\\\\", "/", $docdir);
$fontdir="htdocs".$htdir;

$psdoc="htdocs".$htdir."/draw.ps";
$newpsdoc=ereg_replace("/", "\\", $psdoc);
#$newpsdoc = "/tmp/draw.ps";
#$fontdir = "/home/steinm/sourceforge/pslib/tests/c";

// Begin creating ps doc			
if(isset($_REQUEST['submit']) && $_REQUEST['submit']=="OK")
{
define("LEFT_BORDER", 50);
define("EXAMPLE_BOX_HEIGHT", 120);
define("EXAMPLE_BOX_WIDTH", 100);
define("EXAMPLE_BOX_TITLE_HEIGHT", 20);

function begin_example_box($p, $llx, $lly, $title, $font) {
	ps_save($p);
	ps_translate($p, $llx, $lly);
	ps_setcolor($p, "fill", "gray", 0.5, 0.0, 0.0, 0.0);
	ps_rect($p, 0, EXAMPLE_BOX_HEIGHT-EXAMPLE_BOX_TITLE_HEIGHT,
	           EXAMPLE_BOX_WIDTH, EXAMPLE_BOX_TITLE_HEIGHT);
	ps_fill($p);
	ps_setcolor($p, "stroke", "gray", 1.0, 0.0, 0.0, 0.0);
	ps_setfont($p, $font, 12.0);
	ps_show_xy($p, $title, 10, EXAMPLE_BOX_HEIGHT-EXAMPLE_BOX_TITLE_HEIGHT+5);
	ps_setlinewidth($p, 1.0);
	ps_setcolor($p, "stroke", "gray", 0.0, 0.0, 0.0, 0.0);
	ps_rect($p, 0, 0, EXAMPLE_BOX_WIDTH, EXAMPLE_BOX_HEIGHT);
	ps_stroke($p);
	ps_moveto($p, 0, EXAMPLE_BOX_HEIGHT-EXAMPLE_BOX_TITLE_HEIGHT);
	ps_lineto($p, EXAMPLE_BOX_WIDTH, EXAMPLE_BOX_HEIGHT-EXAMPLE_BOX_TITLE_HEIGHT);
	ps_stroke($p);
}

function end_example_box($p) {
	ps_restore($p);
}

$ps = ps_new();

if (!ps_open_file($ps, $newpsdoc)) {
	print "Cannot open PostScript file\n";
	exit;
}

ps_set_parameter($ps, "warning", "true");

ps_set_info($ps, "Creator", "draw.php");
ps_set_info($ps, "Author", "Uwe Steinmann");
ps_set_info($ps, "Title", "Many examples");

$pstemplate = ps_begin_template($ps, 30.0, 30.0);
ps_moveto($ps, 0, 0);
ps_lineto($ps, 30, 30);
ps_moveto($ps, 0, 30);
ps_lineto($ps, 30, 0);
ps_stroke($ps);
ps_end_template($ps);

$pspattern1 = ps_begin_pattern($ps, 10.0, 10.0, 10.0, 10.0, 1);
ps_setlinewidth($ps, 0.2);
ps_setcolor($ps, "stroke", "rgb", 0.0, 0.0, 1.0, 0.0);
ps_moveto($ps, 0, 0);
ps_lineto($ps, 7, 7);
ps_stroke($ps);
ps_moveto($ps, 0, 7);
ps_lineto($ps, 7, 0);
ps_stroke($ps);
ps_end_pattern($ps);

$pspattern2 = ps_begin_pattern($ps, 10.0, 10.0, 10.0, 10.0, 2);
ps_moveto($ps, 0, 0);
ps_lineto($ps, 5, 5);
ps_stroke($ps);
ps_end_pattern($ps);


ps_begin_page($ps, 596, 842);

ps_set_parameter($ps, "transition", "wipe");
ps_set_value($ps, "duration", 0.5);
	

$psfont = ps_findfont($ps, "$fontdir/Helvetica", "", 0);

$x = 0;
$y = 675;
if(isset($_REQUEST['box1']) && $_REQUEST['box1']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Lines", $psfont);
$polydash = array(5.0, 3.0, 1.0, 4.0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 90, 10);
ps_stroke($ps);

ps_setdash($ps, 5.0, 5.0);
ps_moveto($ps, 10, 15);
ps_lineto($ps, 90, 30);
ps_stroke($ps);

ps_setdash($ps, 0.0, 0.0);
ps_setlinewidth($ps, 2.0);
ps_moveto($ps, 10, 20);
ps_lineto($ps, 90, 50);
ps_stroke($ps);

ps_setlinewidth($ps, 1.0);
ps_setcolor($ps, "stroke", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_moveto($ps, 10, 25);
ps_lineto($ps, 90, 70);
ps_stroke($ps);

ps_setcolor($ps, "stroke", "gray", 0.0, 0.0, 0.0, 0.0);
ps_setpolydash($ps, $polydash);
ps_moveto($ps, 10, 30);
ps_lineto($ps, 90, 90);
ps_stroke($ps);
end_example_box($ps);
}
if(isset($_REQUEST['box2']) && $_REQUEST['box2']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Rectangles", $psfont);
ps_rect($ps, 10, 10, 35, 35);
ps_stroke($ps);
ps_setcolor($ps, "both", "rgb", 0.0, 1.0, 0.0, 0.0);
ps_rect($ps, 50, 10, 35, 35);
ps_stroke($ps);
ps_setcolor($ps, "both", "rgb", 1.0, 1.0, 0.0, 0.0);
ps_rect($ps, 10, 50, 35, 35);
ps_fill($ps);
ps_setlinewidth($ps, 3.0);
ps_setcolor($ps, "both", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_setdash($ps, 5.0, 5.0);
ps_rect($ps, 50, 50, 35, 35);
ps_stroke($ps);
end_example_box($ps);
}
if(isset($_REQUEST['box3']) && $_REQUEST['box3']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Circles, Arcs", $psfont);
ps_circle($ps, 30, 30, 20);
ps_stroke($ps);
ps_circle($ps, 70, 30, 20);
ps_fill($ps);
ps_arc($ps, 30, 70, 20, 0, 270);
ps_fill($ps);
ps_setlinewidth($ps, 3.0);
ps_setcolor($ps, "both", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_setdash($ps, 5.0, 5.0);
ps_arc($ps, 70, 70, 20, 0, 270);
ps_stroke($ps);
end_example_box($ps);
}

if(isset($_REQUEST['box4']) && $_REQUEST['box4']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Line joins", $psfont);
ps_setlinewidth($ps, 5.0);
ps_setlinejoin($ps, 0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 25, 40);
ps_lineto($ps, 40, 10);
ps_stroke($ps);
ps_setlinejoin($ps, 1);
ps_moveto($ps, 60, 10);
ps_lineto($ps, 75, 40);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_setlinejoin($ps, 2);
ps_moveto($ps, 10, 60);
ps_lineto($ps, 25, 90);
ps_lineto($ps, 40, 60);
ps_stroke($ps);

ps_setlinewidth($ps, 0.5);
ps_setcolor($ps, "both", "gray", 1.0, 0.0, 0.0, 0.0);
ps_setlinejoin($ps, 0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 25, 40);
ps_lineto($ps, 40, 10);
ps_stroke($ps);
ps_moveto($ps, 60, 10);
ps_lineto($ps, 75, 40);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_moveto($ps, 10, 60);
ps_lineto($ps, 25, 90);
ps_lineto($ps, 40, 60);
ps_stroke($ps);
end_example_box($ps);
}
if(isset($_REQUEST['box5']) && $_REQUEST['box5']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Line caps", $psfont);
ps_setlinewidth($ps, 5.0);
ps_setlinecap($ps, 0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 25, 40);
ps_lineto($ps, 40, 10);
ps_stroke($ps);
ps_setlinecap($ps, 1);
ps_moveto($ps, 60, 10);
ps_lineto($ps, 75, 40);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_setlinecap($ps, 2);
ps_moveto($ps, 10, 60);
ps_lineto($ps, 25, 90);
ps_lineto($ps, 40, 60);
ps_stroke($ps);

ps_setlinewidth($ps, 0.5);
ps_setcolor($ps, "both", "gray", 1.0, 0.0, 0.0, 0.0);
ps_setlinejoin($ps, 0);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 25, 40);
ps_lineto($ps, 40, 10);
ps_stroke($ps);
ps_moveto($ps, 60, 10);
ps_lineto($ps, 75, 40);
ps_lineto($ps, 90, 10);
ps_stroke($ps);
ps_moveto($ps, 10, 60);
ps_lineto($ps, 25, 90);
ps_lineto($ps, 40, 60);
ps_stroke($ps);
end_example_box($ps);
}
if(isset($_REQUEST['box6']) && $_REQUEST['box6']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Miter limit", $psfont);
ps_show_xy($ps, "10", 13, 10);
ps_setmiterlimit($ps, 10);
ps_moveto($ps, 11, 10);
ps_lineto($ps, 20, 90);
ps_lineto($ps, 29, 10);
ps_stroke($ps);
ps_show_xy($ps, "5", 47, 10);
ps_setmiterlimit($ps, 5);
ps_moveto($ps, 41, 10);
ps_lineto($ps, 50, 90);
ps_lineto($ps, 59, 10);
ps_stroke($ps);
ps_show_xy($ps, "1", 77, 10);
ps_setmiterlimit($ps, 1);
ps_moveto($ps, 71, 10);
ps_lineto($ps, 80, 90);
ps_lineto($ps, 89, 10);
ps_stroke($ps);
end_example_box($ps);
}
if(isset($_REQUEST['box7']) && $_REQUEST['box7']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Curves", $psfont);
ps_moveto($ps, 10, 10);
ps_curveto($ps, 10, 40, 40, 20, 50, 20);
ps_curveto($ps, 50, 20, 80, 90, 20, 70);
ps_curveto($ps, 20, 70, 80, 20, 10, 10);
ps_stroke($ps);
ps_setcolor($ps, "both", "rgb", 1.0, 0.0, 0.0, 0.0);
ps_moveto($ps, 50, 10);
ps_curveto($ps, 50, 10, 110, 40, 90, 90);
ps_curveto($ps, 90, 90, 30, 90, 80, 70);
ps_curveto($ps, 80, 70, 100, 10, 50, 10);
ps_fill($ps);
end_example_box($ps);
}

if(isset($_REQUEST['box8']) && $_REQUEST['box8']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "EPS-Image", $psfont);
$psimage = ps_open_image_file($ps, "eps", "picture.eps", NULL, 0);
ps_place_image($ps, $psimage, 10, 10, 0.3);
ps_close_image($ps, $psimage);
end_example_box($ps);
}
if(isset($_REQUEST['box9']) && $_REQUEST['box9']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Path (1)", $psfont);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 50, 50);
ps_moveto($ps, 20, 10);
ps_lineto($ps, 60, 50);
ps_circle($ps, 60, 60, 20);
ps_lineto($ps, 90, 70);
ps_stroke($ps);
end_example_box($ps);
}
if(isset($_REQUEST['box10']) && $_REQUEST['box10']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Path (2)", $psfont);
ps_moveto($ps, 10, 10);
ps_lineto($ps, 20, 20);
ps_rect($ps, 30, 30, 10, 20);
ps_lineto($ps, 50, 10);
ps_stroke($ps);
ps_moveto($ps, 70, 10);
ps_lineto($ps, 70, 50);
ps_arc($ps, 70, 60, 20, 60, 210);
ps_stroke($ps);
end_example_box($ps);
}
if(isset($_REQUEST['box11']) && $_REQUEST['box11']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_show_xy($ps, "This is text in 8.0", 10, 10);
ps_setfont($ps, $psfont, 12.0);
ps_show_xy($ps, "and in 12.0", 10, 23);
ps_setfont($ps, $psfont, 18.0);
ps_show_xy($ps, "in 18.0", 10, 40);
ps_setfont($ps, $psfont, 36.0);
ps_show_xy($ps, "huge", 10, 65);
end_example_box($ps);
}
if(isset($_REQUEST['box12']) && $_REQUEST['box12']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text outlined", $psfont);
ps_setfont($ps, $psfont, 36.0);
ps_set_value($ps, "textrendering", 1);
ps_show_xy($ps, "huge", 10, 15);
ps_setlinewidth($ps, 0.5);
ps_show_xy($ps, "huge", 10, 55);
ps_set_value($ps, "textrendering", -1);
end_example_box($ps);
}
if(isset($_REQUEST['box13']) && $_REQUEST['box13']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "invisible Text", $psfont);
ps_setfont($ps, $psfont, 10.0);
ps_show_xy($ps, "This is ", 5, 15);
ps_set_value($ps, "textrendering", 3);
ps_show($ps, "invisible");
ps_set_value($ps, "textrendering", -1);
ps_show($ps, " text.");
ps_show_xy($ps, "This is ", 5, 35);
ps_show($ps, "invisible");
ps_show($ps, " text.");
end_example_box($ps);
}
if(isset($_REQUEST['box14']) && $_REQUEST['box14']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text in a box (1)", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_set_value($ps, "leading", 11);
$str1 = "Text can be put into a box of any size. This one is 80x80 pixels and its lower left corner ist at (10, 10). The text is left justified. The font size is 8.0.";
ps_show_boxed($ps, $str1, 10, 10, 80, 80, "left", NULL);
end_example_box($ps);
}
if(isset($_REQUEST['box15']) && $_REQUEST['box15']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text in a box (2)", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_set_value($ps, "leading", 11);
$str2 = "Text can be put into a box of any size. This one is 80x80 pixels and its lower left corner ist at (10, 10). The text is left and right justified. The font size is 8.0.";
ps_show_boxed($ps, $str2, 10, 10, 80, 80, "justify", NULL);
end_example_box($ps);
}

if(isset($_REQUEST['box16']) && $_REQUEST['box16']=="true")
{
if($x > 3) {$y -= 150; $x = 0;}
begin_example_box($ps, LEFT_BORDER+(EXAMPLE_BOX_WIDTH+30)*($x++), $y, "Text with CR", $psfont);
ps_setfont($ps, $psfont, 8.0);
ps_set_value($ps, "leading", 11);
ps_set_parameter($ps, "linebreak", "true");
$str4 = "If the parameter linebreak is turned on\neach line will be ended with a carriage return.\nCR are marked with a '\\n' in this paragraph.";
ps_show_boxed($ps, $str4, 10, 10, 80, 80, "center", NULL);
ps_set_parameter($ps, "linebreak", "false");
end_example_box($ps);
}
ps_end_page($ps);
ps_close($ps);
ps_delete($ps);
}

for($x=1; $x<=16; $x++)
	$checked[$x]="";          
foreach ($_GET as $key => $value) {
	list($nothing, $number) = split ('box', $key); 
	if ($value=="true"){ 
		$checked[$number]="checked"; } else {
		$checked[$number]="";          
	}
}
$x=1; 
?>
<form name="ff" action="ps.php" method="get">
			
			 <p class=small>
			<?=$TEXT['ps-text1']?>
		
			<p>
			
			<?php 
      if(isset($_REQUEST['submit']) && $_REQUEST['submit']=="OK") {
      
       
        echo "<br /><i>Successfully published new PostScript file";
        if (getenv('REMOTE_ADDR') == "127.0.0.1") {
         echo " as ".$xamppdir."xampp\\".$newpsdoc."</i>";
         }
         echo "!<p /><br />".$TEXT["ps-text2"]."</p>";         
      }
			?>
			<table border="0" width="100%">
			<tr>
			   <td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Lines
			<p></td>
			<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Rectangles
			<p></td>
			<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Circle,Args
			<p></td>
				<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Line joins
			<p></td>
			</tr>
				<tr>
			   <td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Line caps
			<p></td>
			<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Miter limit
			<p></td>
			<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Curves
			<p></td>
				<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example EPS-Image
			<p></td>
			</tr>
			<tr>
			   <td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Path (1)
			<p></td>
			<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Path (2)
			<p></td>
			<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Text
			<p></td>
				<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Text outlined
			<p></td>
			</tr>
				<tr>
			   <td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box invisible Text
			<p></td>
			<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Text in a box (1)
			<p></td>
			<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Text in a box (2)
			<p></td>
				<td>	<p class=small>
			<input name="box<?=$x++?>" value="true" type="checkbox" <?=$checked[$x-1]?>> Example Box Text with CR
			<p></td>
			</tr>
			
			</table>
			
			

		
		<p /> <p />
			<input type="submit" name="submit" value="<?=$TEXT['flash-ok']?>">
		</form>
	    <?php include("../../showcode.php"); ?>
	</body>
</html>
