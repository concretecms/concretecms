<template functional>
    <div :class="{'dropdown-menu': true, show: props.show, ...props.class}" aria-labelledby="dropdownMenuButton">
        <slot></slot>
    </div>
</template>
