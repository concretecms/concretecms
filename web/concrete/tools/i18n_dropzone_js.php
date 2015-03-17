<?php
header('Content-type: text/javascript; charset=' . APP_CHARSET);
?>
Dropzone.prototype.defaultOptions.dictDefaultMessage = <?php echo json_encode(t("Drop files here to upload")); ?>;
Dropzone.prototype.defaultOptions.dictFallbackMessage = <?php echo json_encode(t("Your browser does not support drag'n'drop file uploads.")); ?>;
Dropzone.prototype.defaultOptions.dictFallbackText = <?php echo json_encode(t("Please use the fallback form below to upload your files like in the olden days.")); ?>;
