<template functional>
    <div class="ccm-image-cell-grid">
      <div class="ccm-image-cell text-center" :class="{ active: props.isActive }">
          <button type="button" class="delete" @click="listeners.delete">
              <Icon icon="times" type="fas" color="#fff"/>
          </button>
          <div @click="listeners.click">
              <img :src="props.src" :style="{ width: props.size + 'px', height: props.size + 'px' }" />
              <p>{{ props.fileSize }}</p>
          </div>
      </div>
    </div>
</template>

<style lang="scss" scoped>
  .ccm-image-cell {
    cursor: pointer;
    display: inline-flex;
    flex-direction: column;
    margin: 0.625rem;
    position: relative;

    &:hover {
      .delete {
        opacity: 1;
        transition: opacity 0.2s ease-out;
      }
    }

    p {
      font-size: 1rem;
      letter-spacing: 0;
      margin-top: 0.625rem;
      text-transform: uppercase;
    }

    img {
      padding: 0.5rem;
    }

    .delete {
      background-color: #6a6f7b;
      border: 0;
      border-radius: 20px;
      box-shadow: 2px 2px 4px 0 rgba(0, 0, 0, 0.2);
      float: left;
      height: 30px;
      margin-right: -10px;
      margin-top: -10px;
      opacity: 0;
      position: absolute;
      right: 0;
      width: 30px;
      z-index: 90;
    }
  }
</style>

<script>
import Icon from '../Icon'

export default {
    components: {
        Icon
    },
    props: {
        isActive: {
            type: Boolean,
            default: false
        },
        src: {
            type: String,
            required: true
        },
        fileSize: {
            type: String,
            required: true
        },
        size: {
            type: String,
            required: true
        }
    }
}
</script>
