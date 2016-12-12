if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.ccmPath = CKEDITOR.basePath.replace('vendor/','core/');
    CKEDITOR.plugins.addExternal('concrete5styles', CKEDITOR.ccmPath + 'concrete5styles/');
}