<?=Loader::helper('concrete/asset_library')->file("addImageToImageEditor_$editorid",'','Select Image');?>
<a class='image'>Select Image</a>
<a class='webcam' href='#'>From Webcam</a>
<script class='template' type='text/html'>
	<div class='ccm-ui capture_image'>
		<video></video>
		<canvas></canvas>
		<br>
		<button class='btn btn-primary'>Capture</button>
	</div>
</script>