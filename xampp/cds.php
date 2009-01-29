<?
	// Requests allowed only from localhosz
	extract($_POST);
	extract($_SERVER);
	$host = "127.0.0.1";
	$timeout = "1";

	if ($REMOTE_ADDR) {
		if ($REMOTE_ADDR != $host) {
			echo "<p><h2> FORBIDDEN FOR CLIENT $REMOTE_ADDR <h2></p>";
			exit;
		}
}
	
	if($_REQUEST['action']=="getpdf")
	{
		mysql_connect("localhost","root","");
		mysql_select_db("cdcol");

		include ('class.ezpdf.php');
		$pdf = new Cezpdf();
		$pdf->selectFont('../../php/extras/pdf-related/Helvetica.afm');

		$pdf->ezText('CD Collection',14);
		$pdf->ezText('© 2002/2003 Kai Seidler, oswald@apachefriends.org, GPL',10);
		$pdf->ezText('',12);

		$result=mysql_query("SELECT id,titel,interpret,jahr FROM cds ORDER BY interpret;");
		
		$i=0;
		while( $row=mysql_fetch_array($result) )
		{
			$data[$i]=array('interpret'=>$row['interpret'],'titel'=>$row['titel'],'jahr'=>$row['jahr']);
			$i++;
		}

		$pdf->ezTable($data,"","",array('width'=>500));

		$pdf->ezStream();
		exit;
	}
?>
<? include("langsettings.php"); ?>
<html>
<head>
<title>apachefriends.org cd collection</title>
<link href="xampp.css" rel="stylesheet" type="text/css">
</head>

<body>

&nbsp;<p>
<h1><?=$TEXT['cds-head']?></h1>
<i>(Requests allowed from localhost only)</i><br/><br/>
<?=$TEXT['cds-text1']?><p>
<?=$TEXT['cds-text2']?><p>

<?

//    Copyright (C) 2002/2003 Kai Seidler, oswald@apachefriends.org
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.


	if(!mysql_connect("localhost","root",""))
	{
		echo "<h2>".$TEXT['cds-error']."</h2>";
		die();
	}
	mysql_select_db("cdcol");
?>

<h2><?=$TEXT['cds-head1']?></h2>

<table border=0 cellpadding=0 cellspacing=0>
<tr bgcolor=#f87820>
<td><img src=img/blank.gif width=10 height=25></td>
<td class=tabhead><img src=img/blank.gif width=200 height=6><br><b><?=$TEXT['cds-attrib1']?></b></td>
<td class=tabhead><img src=img/blank.gif width=200 height=6><br><b><?=$TEXT['cds-attrib2']?></b></td>
<td class=tabhead><img src=img/blank.gif width=50 height=6><br><b><?=$TEXT['cds-attrib3']?></b></td>
<td class=tabhead><img src=img/blank.gif width=50 height=6><br><b><?=$TEXT['cds-attrib4']?></b></td>
<td><img src=img/blank.gif width=10 height=25></td>
</tr>


<?
	if($_REQUEST['interpret']!="")
	{
		if($jahr=="")$jahr="NULL";
		$titel=htmlentities($_REQUEST['titel']);
		$interpret=htmlentities($_REQUEST['interpret']);
		$jahr=htmlentities($_REQUEST['jahr']);
		mysql_query("INSERT INTO cds (titel,interpret,jahr) VALUES('$titel','$interpret',$jahr);");
	}

	if($_REQUEST['action']=="del")
	{
		mysql_query("DELETE FROM cds WHERE id={$_REQUEST['id']};");
	}

	$result=mysql_query("SELECT id,titel,interpret,jahr FROM cds ORDER BY interpret;");
	
	$i=0;
	while( $row=mysql_fetch_array($result) )
	{
		if($i>0)
		{
			echo "<tr valign=bottom>";
			echo "<td bgcolor=#ffffff background='img/strichel.gif' colspan=6><img src=img/blank.gif width=1 height=1></td>";
			echo "</tr>";
		}
		echo "<tr valign=center>";
		echo "<td class=tabval><img src=img/blank.gif width=10 height=20></td>";
		echo "<td class=tabval><b>".$row['interpret']."</b></td>";
		echo "<td class=tabval>".$row['titel']."&nbsp;</td>";
		echo "<td class=tabval>".$row['jahr']."&nbsp;</td>";

		echo "<td class=tabval><a onclick=\"return confirm('".$TEXT['cds-sure']."');\" href=cds.php?action=del&id=".$row['id']."><span class=red>[".$TEXT['cds-button1']."]</span></a></td>";
		echo "<td class=tabval></td>";
		echo "</tr>";
		$i++;

	}

	echo "<tr valign=bottom>";
        echo "<td bgcolor=#fb7922 colspan=6><img src=img/blank.gif width=1 height=8></td>";
        echo "</tr>";


?>

</table>

<h2><?=$TEXT['cds-head2']?></h2>

<form action=cds.php method=get>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td><?=$TEXT['cds-attrib1']?>:</td><td><input type=text size=30 name=interpret></td></tr>
<tr><td><?=$TEXT['cds-attrib2']?>:</td><td> <input type=text size=30 name=titel></td></tr>
<tr><td><?=$TEXT['cds-attrib3']?>:</td><td> <input type=text size=5 name=jahr></td></tr>
<tr><td></td><td><input type=submit border=0 value="<?=$TEXT['cds-button2']?>"></td></tr>
</table>
</form>
<? include("showcode.php"); ?>

</body>
</html>
