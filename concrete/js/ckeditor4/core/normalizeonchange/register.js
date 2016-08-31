if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.ccmPath = CKEDITOR.basePath.replace('vendor/','core/');
    CKEDITOR.plugins.addExternal('normalizeonchange', CKEDITOR.ccmPath + 'normalizeonchange/');
}