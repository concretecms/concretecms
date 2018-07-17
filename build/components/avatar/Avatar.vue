<template>
    <div>
        <img ref='image'
             :src="img"
             :width="imageWidth" :height="imageHeight"
             :class="{
                 shadow: shadow
             }"
             :style="{
                 width: resizeWidth + 'px',
                 height: resizeHeight + 'px',
                 transform: 'translate(' + (x + adjX) + 'px, ' + (y + adjY) + 'px)',
             }"/>
    </div>
</template>
<script src="./Avatar.js"></script>
<style src="./Avatar.less"></style>
