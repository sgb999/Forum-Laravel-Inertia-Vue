<template>
    <nav v-if="links.length > 3">
        <ul class="pagination desktop">
            <li class="page-item" v-for="link in links">
                <button v-if="link.url" @click="$emit('nextPage', link.url)"
                        :class="{'btn button-dark' :  link.url, 'font-bold' : link.active}">
                    {{ link.label}}
                </button>
            </li>
        </ul>
        <ul class="pagination mobile">
            <li class="page-item">
                <button v-if="links[0].url" @click="$emit('nextPage', links[0].url)"
                        :class="{'page-link' :  links[0].url, 'font-bold' : links[0].active}">
                    {{ links[0].label}}
                </button>
            </li>
            <li class="page-item">
                <button v-if="links[links.length - 1].url" @click="$emit('nextPage', links[links.length - 1].url)"
                        :class="{'btn button-dark' :  links[links.length - 1].url, 'font-bold' : links[links.length - 1].active}">
                    {{ links[links.length - 1].label}}
                </button>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    name: "pagination",
    props: {
        links: {
            required: true
        }
    }
};
</script>

<style scoped>
.desktop{
    display: flex;
}
.mobile{
    display: none;
}
@media screen and (max-width: 600px) {
    .desktop{
        display: none;
    }
    .mobile{
        display: flex;
    }
}
</style>
