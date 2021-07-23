<template>
    <div class="ccm-avatar-creator-container">
        <avatar ref="shadow" v-if="img !== null"
                      @mount="attachShadow"
                      :shadow="true"
                      :img="img"
                      :imageHeight="imageHeight"
                      :imageWidth="imageWidth"
                      :cropperWidth="width"
                      :cropperHeight="height" />
        <div ref='dropzone'
             class="ccm-avatar-creator"
             :style="{width: width + 'px', height: height + 'px' }"
             :class="{editing: img !== null}">
            <avatar ref="image" v-if="img"
                          :img="img"
                          :imageHeight="imageHeight"
                          :imageWidth="imageWidth"
                          :cropperWidth="width"
                          :cropperHeight="height" />
            <img class="ccm-avatar-current" v-if="!img" :src="currentimage"/>
            <div class="saving"
                 v-if="saving"
                 :style="{lineHeight: height + 'px' }">
                <i class="fa fa-spin fa-spinner fa-circle-o-notch"></i>
            </div>
        </div>
        <div class="ccm-avatar-actions" v-if="img">
            <a class="ccm-avatar-okay" :style="{width: width / 2 + 'px'}" @click="handleOkay"></a>
            <a class="ccm-avatar-cancel" :style="{width: width / 2 + 'px'}" @click="handleCancel"></a>
        </div>
        <canvas ref="canvas" style="height: 0;"></canvas>
        <div v-if="hasError" class="alert alert-danger">
            {{errorMessage}}
        </div>
    </div>
</template>

<script src='./Cropper.js'></script>
<style src='./Cropper.scss' lang="scss" scoped></style>
