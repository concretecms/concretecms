if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.ccmPath = CKEDITOR.basePath.replace('vendor/','core/');
    CKEDITOR.plugins.addExternal('concrete5inline', CKEDITOR.ccmPath + 'concrete5inline/');
}