<template>
    <nav v-if="links.length > 3" aria-label="Pagination">
        <ul class="pagination desktop">
            <li class="page-item" v-for="(link, i) in links" :key="i">
                <inertia-link v-if="link.url" :href="link.url" preserveState
                              class="btn button-dark"
                              :class="{'font-bold' : link.active}"
                              :aria-current="link.active ? 'page' : undefined">
                    {{ label(link.label) }}
                </inertia-link>
                <span v-else-if="isGap(link.label)" aria-hidden="true">&hellip;</span>
                <span v-else class="btn button-dark disabled" aria-disabled="true">{{ label(link.label) }}</span>
            </li>
        </ul>
        <ul class="pagination mobile">
            <li class="page-item" v-for="link in [links[0], links[links.length - 1]]" :key="link.label">
                <inertia-link v-if="link.url" :href="link.url" preserveState class="btn button-dark">
                    {{ label(link.label) }}
                </inertia-link>
                <span v-else class="btn button-dark disabled" aria-disabled="true">{{ label(link.label) }}</span>
            </li>
        </ul>
    </nav>
</template>

<script setup lang="ts">

// Inertia
import { Link as InertiaLink } from "@inertiajs/vue3";

// Types
import { PaginationLink } from "../types/Pagination";

defineOptions({
    name: "Pagination"
});

defineProps<{
    links: PaginationLink[]
}>();

// Laravel sends "&laquo; Previous" / "Next &raquo;" as HTML entities
const label = (text: string) => text.replace(/&laquo;|&raquo;/g, "").trim();
const isGap = (text: string) => text.trim() === "...";

</script>

<style scoped lang="sass">
.pagination
    display: flex
    justify-content: center
    list-style-type: none
    padding: 0
.page-item
    margin: 0 3px
.disabled
    opacity: 0.5
    cursor: not-allowed
.desktop
    display: flex
.mobile
    display: none
@media screen and (max-width: 600px)
    .desktop
        display: none
    .mobile
        display: flex
</style>
