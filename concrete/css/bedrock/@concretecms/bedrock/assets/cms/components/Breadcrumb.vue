<template>
    <div class="ccm-dashboard-breadcrumb">
        <ol v-if="breadcrumbItems.length > 1" class="breadcrumb">
            <li v-for="(breadcrumbItem, index) in breadcrumbItems" :class="{'breadcrumb-item': true, 'active': isItemActive(index)}">
                <span v-if="breadcrumbItem.children.length" class="dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        {{ breadcrumbItem.name }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li v-for="child in breadcrumbItem.children">
                            <a class="dropdown-item" href="#" @click.prevent="onItemClick(child)">{{child.name}}</a>
                        </li>
                    </ul>
                </span>
                <span v-else-if="isItemActive(index)">{{breadcrumbItem.name}}</span>
                <a v-else href="#" @click.prevent="onItemClick(breadcrumbItem)">{{breadcrumbItem.name}}</a>
            </li>
        </ol>
    </div>
</template>
<style lang='scss' scoped>
@import '../../../assets/cms/scss/bootstrap-overrides';

.ccm-dashboard-breadcrumb {
  .breadcrumb {
    margin-bottom: 0;
    padding-bottom: 0;
    padding-top: 0;

    a {
      color: $blue;
    }
  }
}
</style>
<script>

export default {
    props: {
        breadcrumbItems: {
            type: Array,
            required: true
        }
    },
    methods: {
        isItemActive(itemIndex) {
            return (this.breadcrumbItems.length - itemIndex) === 1
        },
        onItemClick(item) {
            this.$emit('itemClick', item)
        }
    }
}
</script>
