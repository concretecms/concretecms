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

<style src='./Avatar.scss' lang="scss" scoped></style>
<script src='./Avatar.js'></script>
