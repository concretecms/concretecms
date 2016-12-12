if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.ccmPath = CKEDITOR.basePath.replace('vendor/','core/');
    CKEDITOR.plugins.addExternal('concrete5uploadimage', CKEDITOR.ccmPath + 'concrete5uploadimage/');
}