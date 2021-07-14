<template>
    <div class="ccm-gallery-image-details">
        <div class="image-preview text-center">
            <div class="image-container">
                <img :src="this.$props.image.imageUrl" />
            </div>
            <IconButton
              icon="trash-alt"
              icon-type="far"
              @click="$emit('delete')"
              type="outline"
            >
              Remove from Gallery
            </IconButton>
            <!-- <button type="button" class="btn btn-secondary" @click="$emit('delete')">Remove from Gallery</button> -->
        </div>
        <div class="image-details">
            <section>
                <strong>Custom Attributes</strong>

                <p class="image-title">{{this.$props.image.title}}</p>
                <p class="image-description">{{this.$props.image.description}}</p>
                <p class="image-attribute" v-for="([key, value], idx) of image.attributes" :key="idx">
                    <strong>{{key}}:</strong> {{value}}
                </p>

                <div class="mb-4 text-end">
                    <IconButton
                      icon="pencil-alt"
                      icon-type="fas"
                      @click="goToDetails($props.image.detailUrl)"
                      type="outline"
                      v-if="$props.image.detailUrl"
                    >
                      Edit Attributes
                    </IconButton>
                </div>
            </section>

            <section v-if="!this.$props.image.displayChoices.length">
                <div class="mb-2">
                    <strong>Display Options</strong>
                </div>
                <div v-for="(choice, index) in this.$props.image.displayChoices" :key="index">
                    <input v-if="choice.type === 'text'"
                        :placeholder="choice.title"
                        :name="index"
                        class="form-control mb-3"
                        v-model="choice.value"
                    />
                    <select v-if="choice.type === 'select'"
                        :name="index"
                        v-model="choice.value"
                        class="form-select mb-3">
                        <option selected disabled value="0">{{ choice.title }}</option>
                        <option v-for="(option, index ) in choice.options"
                            :key="index"
                            :value="index">
                            {{ option }}
                        </option>
                    </select>
                </div>
            </section>

        </div>
    </div>
</template>

<style lang="scss" scoped>
.ccm-gallery-image-details {
  border-top: 1px solid #979797;
  display: flex;
  padding-top: 20px;

  .image-preview,
  .image-details {
    flex: 1;
    padding: 10px;
    width: 50%;
  }

  .image-preview {
    .image-container {
      align-items: center;
      display: flex;
      height: 100%;
      justify-content: center;
    }

    img {
      height: auto;
      margin-bottom: 10px;
      max-height: 100%;
      max-width: 100%;
      width: auto;
    }
  }

  .image-details {
    section {
      clear: both;
      margin-bottom: 10px;

      p {
        color: #005164;
        margin: 15px 0 15px 15px;

        &.image-title {
          font-weight: bold;
        }
      }

    }
  }
}
</style>

<script>
import IconButton from '../IconButton'

export default {
    components: {
        IconButton,
        ...IconButton.components
    },
    props: {
        image: {
            type: Object,
            required: true
        }
    },
    methods: {
        goToDetails: (url) => {
            window.open(url, '_blank')
        }
    }
}
</script>
