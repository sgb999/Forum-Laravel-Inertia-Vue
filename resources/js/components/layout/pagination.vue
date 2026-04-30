<template>
    <nav v-if="links.length > 3">
        <ul class="pagination desktop">
            <li class="page-item" v-for="link in links">
                <inertia-link v-if="link.url" :href="link.url"
                        :class="{'btn button-dark' :  link.url, 'font-bold' : link.active}">
                    {{ link.label}}
                </inertia-link>
            </li>
        </ul>
        <ul class="pagination mobile">
            <li class="page-item">
                <inertia-link v-if="links[0].url" :href="links[0].url"
                        :class="{'page-link' :  links[0].url, 'font-bold' : links[0].active}">
                    {{ links[0].label}}
                </inertia-link>
            </li>
            <li class="page-item">
                <inertia-link v-if="links[links.length - 1].url" :href="links[links.length - 1].url"
                        :class="{'btn button-dark' :  links[links.length - 1].url, 'font-bold' : links[links.length - 1].active}">
                    {{ links[links.length - 1].label}}
                </inertia-link>
            </li>
        </ul>
    </nav>
</template>

<script>
import { Link as InertiaLink } from "@inertiajs/vue3";

export default {
    name: "pagination",
    components: { InertiaLink },
    props: {
        links: {
            required: true
        }
    }
};
</script>

<style scoped>
.pagination {
    display: flex;
    justify-content: center;
    list-style-type: none;
    padding: 0;
}

.page-item {
    margin: 0 3px;
}

.desktop {
    display: flex;
}

.mobile {
    display: none;
}

@media screen and (max-width: 600px) {
    .desktop {
        display: none;
    }
    .mobile {
        display: flex;
    }
}
</style>
