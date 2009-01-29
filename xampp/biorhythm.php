<?php
	include "langsettings.php";

	// Biorhythm by Till Gerken
	// http://www.zend.com/zend/tut/dynamic.php
	//
	// Multi-language support by Kai Oswald Seidler, 2003
	//
	// Print a standard page header
	//
	function pageHeader() {
		global $TEXT;

		echo "<html><head>";
		echo '<link href="xampp.css" rel="stylesheet" type="text/css">';
		echo "<title></title>";
		echo "</head><body>";

		echo "&nbsp;<p><h1>".$TEXT['bio-head']."</h1>";
		echo $TEXT['bio-by']." Till Gerken<br><a class='black' href='http://www.zend.com/zend/tut/dynamic.php'>http://www.zend.com/zend/tut/dynamic.php</a><p>";
	}

	//
	// Print a standard page footer
	//
	function pageFooter() {
		echo "</body></html>";
	}

	//
	// Function to draw a curve of the biorythm
	// Parameters are the day number for which to draw,
	// period of the specific curve and its color
	//
	function drawRhythm($daysAlive, $period, $color) {
		global $daysToShow, $image, $diagramWidth, $diagramHeight;

		// get day on which to center
		$centerDay = $daysAlive - ($daysToShow / 2);

		// calculate diagram parameters
		$plotScale = ($diagramHeight - 25) / 2;
		$plotCenter = ($diagramHeight - 25) / 2;

		// draw the curve
		for ($x = 0; $x <= $daysToShow; $x++) {
			// calculate phase of curve at this day, then Y value
			// within diagram
			$phase = (($centerDay + $x) % $period) / $period * 2 * pi();
			$y = 1 - sin($phase) * (float)$plotScale + (float)$plotCenter;

			// draw line from last point to current point
			if ($x > 0) {
				imageLine($image, $oldX, $oldY,	$x * $diagramWidth / $daysToShow, $y, $color);
			}

			// save current X/Y coordinates as start point for next line
			$oldX = $x * $diagramWidth / $daysToShow;
			$oldY = $y;
		}
	}

	//
	// ---- MAIN PROGRAM START ----
	//

	// check if we already have a date to work with,
	// if not display a form for the user to enter one
	//
	if (!isset($_REQUEST['birthdate']))	{
		pageHeader();
?>
		<form method="post" action="<?php echo basename($_SERVER['PHP_SELF']); ?>">
			<!-- Please enter your birthday: -->
			<?php echo $TEXT['bio-ask']; ?>:
			<br>
			<input type="text" name="birthdate" value="MM/DD/YYYY"><p>
			<input type="submit" value="<?php echo $TEXT['bio-ok']; ?>">
			<input type="hidden" name="showpng" value="1">
		</form>
<?php

    	include("showcode.php");

		pageFooter();
		exit;
	}

	// get different parts of the date
	$birthMonth = substr($_REQUEST['birthdate'], 0, 2);
	$birthDay = substr($_REQUEST['birthdate'], 3, 2);
	$birthYear = substr($_REQUEST['birthdate'], 6, 4);

	// check date for validity, display error message if invalid
	if (!@checkDate($birthMonth, $birthDay, $birthYear)) {
		pageHeader();

		//print("The date '$birthMonth/$birthDay/$birthYear' is invalid.");
		echo "<h2>".$TEXT['bio-error1']." '$birthMonth/$birthDay/$birthYear' ".$TEXT['bio-error2'].".</h2>";

		pageFooter();
		exit;
	}

	if (isset($_POST['showpng']) && ($_POST['showpng'] == 1)) {
		pageHeader();

		echo "<img src=".basename($_SERVER['PHP_SELF'])."?birthdate=".urlencode($_REQUEST['birthdate'])." alt=''>";

		pageFooter();
		exit;
	}

	// specify diagram parameters (these are global)
	$diagramWidth = 710;
	$diagramHeight = 400;
	$daysToShow = 30;

	// calculate the number of days this person is alive
	// this works because Julian dates specify an absolute number
	// of days -> the difference between Julian birthday and
	// "Julian today" gives the number of days alive
	$daysGone = abs(gregorianToJD($birthMonth, $birthDay, $birthYear) - gregorianToJD(date("m"), date("d"), date("Y")));

	// create image
	$image = imageCreate($diagramWidth, $diagramHeight);

	// allocate all required colors
	$colorBackgr = imageColorAllocate($image, 192, 192, 192);
	$colorForegr = imageColorAllocate($image, 255, 255, 255);
	$colorGrid = imageColorAllocate($image, 0, 0, 0);
	$colorCross = imageColorAllocate($image, 0, 0, 0);
	$colorPhysical = imageColorAllocate($image, 0, 0, 255);
	$colorEmotional = imageColorAllocate($image, 255, 0, 0);
	$colorIntellectual = imageColorAllocate($image, 0, 255, 0);

	// clear the image with the background color
	imageFilledRectangle($image, 0, 0, $diagramWidth - 1, $diagramHeight - 1, $colorBackgr);

	// calculate start date for diagram and start drawing
	$nrSecondsPerDay = 60 * 60 * 24;
	$diagramDate = time() - ($daysToShow / 2 * $nrSecondsPerDay) + $nrSecondsPerDay;

	for ($i = 1; $i < $daysToShow; $i++) {
		$thisDate = getDate($diagramDate);
		$xCoord = ($diagramWidth / $daysToShow) * $i;

		// draw day mark and day number
		imageLine($image, $xCoord, $diagramHeight - 25, $xCoord, $diagramHeight - 20, $colorGrid);
		imageString($image, 3, $xCoord - 5, $diagramHeight - 16, $thisDate["mday"], $colorGrid);

		$diagramDate += $nrSecondsPerDay;
	}

	// draw rectangle around diagram (marks its boundaries)
	imageRectangle($image, 0, 0, $diagramWidth - 1, $diagramHeight - 20, $colorGrid);

	// draw middle cross
	imageLine($image, 0, ($diagramHeight - 20) / 2, $diagramWidth, ($diagramHeight - 20) / 2, $colorCross);
	imageLine($image, $diagramWidth / 2, 0, $diagramWidth / 2, $diagramHeight - 20,	$colorCross);

	// print descriptive text into the diagram
	imageString($image, 3, 10, 10, $TEXT['bio-birthday'].": $birthDay.$birthMonth.$birthYear", $colorCross);
	imageString($image, 3, 10, 26, $TEXT['bio-today'].":    ".date("d.m.Y"), $colorCross);
	imageString($image, 3, 10, $diagramHeight - 42, $TEXT['bio-physical'], $colorPhysical);
	imageString($image, 3, 10, $diagramHeight - 58, $TEXT['bio-emotional'], $colorEmotional);
	imageString($image, 3, 10, $diagramHeight - 74, $TEXT['bio-intellectual'], $colorIntellectual);

	// now draw each curve with its appropriate parameters
	drawRhythm($daysGone, 23, $colorPhysical);
	drawRhythm($daysGone, 28, $colorEmotional);
	drawRhythm($daysGone, 33, $colorIntellectual);

	// set the content type
	header("Content-Type: image/png");

	// create an interlaced image for better loading in the browser
	imageInterlace($image, 1);

	// mark background color as being transparent
	imageColorTransparent($image, $colorBackgr);

	// now send the picture to the client (this outputs all image data directly)
	imagePNG($image);
	exit;
?>
