if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.ccmPath = CKEDITOR.basePath.replace('vendor/', 'core/')
    CKEDITOR.plugins.addExternal('concreteinline', CKEDITOR.ccmPath + 'concreteinline/')
}
