<template>
    <div>
        <upload-from-computer
            :upload-directory-id="uploadDirectoryId"
            @uploadProgressStateChange="isUploadInProgress = $event"/>

        <concrete-file-directory-input
            input-label="Upload files to"
            input-name="uploadDirectoryId"
            :show-add-directory-button="true"
            :disabled="isUploadInProgress"
            @change="uploadDirectoryId = $event"/>
    </div>
</template>

<script>
import UploadFromComputer from './Uploader/UploadFromComputer'
import ConcreteFileDirectoryInput from '../form/ConcreteFileDirectoryInput'

export default {
    components: {
        UploadFromComputer,
        ConcreteFileDirectoryInput
    },
    data: () => ({
        uploadDirectoryId: 0,
        isUploadInProgress: false
    })
}
</script>
