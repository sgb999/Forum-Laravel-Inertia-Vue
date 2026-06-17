<template>
    <div class="row">
        <div class="footer col-xs-12">
            <p>© 2026 Copyright:</p>
        </div>
    </div>
</template>

<script setup lang="ts">
// Vue
import { defineOptions } from "vue";

defineOptions({
    name: "Footer"
});
</script>
<style scoped lang="sass">
.footer
    font-family: Arial, serif
    color: #F1F3F1
    clear: both
    margin-top: 20px
    text-align: center
    background-color: #151210
</style>
