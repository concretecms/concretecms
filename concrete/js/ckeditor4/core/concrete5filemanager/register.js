if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.ccmPath = CKEDITOR.basePath.replace('vendor/','core/');
    CKEDITOR.plugins.addExternal('concrete5filemanager', CKEDITOR.ccmPath + 'concrete5filemanager/');
}