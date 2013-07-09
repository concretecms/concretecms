<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html lang="en">
<head>

<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet">

<? Loader::element('header_required'); ?>

<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>

</head>
<body>
<!-- adding temporary styles for gathering --> 

<style>
	.ccm-gathering-vimeo {
		height: 100%;
		margin: 30px 0px 60px 0px;
	}
	
	.ccm-gathering-vimeo  p{
		display: block;
		margin: 15px auto;
		width: 80%;
	}
	
	.ccm-gathering-vimeo  p,
	.ccm-gathering-vimeo  p a {
		color: #999;
	}
	
	.ccm-gathering-vimeo iframe {
		width: 80%;
		height: 80%;
		display: block;
		margin: 0 auto;
	}
	
	.ccm-gathering-twitter p a {
		color: #23bbe5;
	}
	
	.ccm-gathering-twitter p {
		font-size: 24px;	
		font-weight: 300;
		line-height: 1.4em;
	}
	
	.ccm-gathering-twitter span.who-from,
	.ccm-gathering-twitter span.elapsed {
		display: inline-block;
		font-size: 13px;
		color: #999;
	}
	
	.ccm-gathering-twitter span.elapsed {
		margin-right: 20px;
	}
	
	.ccm-gathering-twitter .twitter-logo {
		width: 20%;
		max-width: 50px;
		display: block;
		float: left;
	}
	
	.ccm-gathering-twitter .tweet {
		float: left;
		width: 75%;
		margin-left: 5%;
	}
	
	.ccm-gathering-masthead-byline-description {
		width: 80%;
		padding: 10%;
	}
	
	.ccm-gathering-masthead-byline-description .ccm-gathering-tile-headline, 
	.ccm-gathering-masthead-byline-description .ccm-gathering-tile-headline a {
		font-size: 26px;
		color: #333;
		font-weight: bold;
		margin-bottom: 15px;
		line-height: 1.3em;
	}
	
	.ccm-gathering-masthead-byline-description .ccm-gathering-tile-byline {
		color: #999;
		margin-bottom: 20px;
		display: block;
	}
	
	.ccm-gathering-masthead-byline-description .ccm-gathering-tile-description {
		font-size: 16px;
		margin-bottom: 20px;
	}
	
	.ccm-gathering-masthead-byline-description .ccm-gathering-tile-read-more a {
			color: #23bbe5;
	}
	
	
	.ccm-gathering-image-overlay-headline-byline img {
		max-width: none;
	}
	
	.w1 .ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description,
	.h1 .ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description {
		padding-left: 10px;
		padding-top: 10px;
		padding-bottom: 5px;
	}
	
	.w1 .ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description .overlay-title, 
	.h1 .ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description .overlay-title {
		font-size: 15px;
		margin-bottom: 5px;
		
	}
	
	.w1 .ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description .overlay-byline, 
	.h1 .ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description .overlay-byline {
		font-size: 10px;
	}
	
	
	.ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description {
		position: absolute;
		bottom: 0;
		background: black;
		opacity: .9;
		width: 100%;
		padding-left: 40px;
		padding-top: 35px;
		padding-bottom: 35px;
	}
	
	.ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description .overlay-title{
		font-size: 24px;
		color: #23bbe5;
		display: block;
		margin-bottom: 15px;
	}
	
	.ccm-gathering-image-overlay-headline-byline .ccm-gathering-tile-image-overlay-headline-byline-description .overlay-byline {
		color: #aaa;
		display: block;
	}

	
</style>

<!-- end temporary gathering styles --> 
<div style="height: 200px"></div>