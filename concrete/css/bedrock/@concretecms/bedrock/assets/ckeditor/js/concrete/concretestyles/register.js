if (typeof CKEDITOR !== 'undefined') {
    CKEDITOR.ccmPath = CKEDITOR.basePath.replace('vendor/', 'core/')
    CKEDITOR.plugins.addExternal('concretestyles', CKEDITOR.ccmPath + 'concretestyles/')
}
