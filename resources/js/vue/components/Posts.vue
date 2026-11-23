<template>
    <Head><title>View Topics</title></Head>
    <div class="container">
        <page-loader  v-if="!topics"/>
        <div v-if="topics"
           v-for="(topic, index) in topics"
            class="card"
            :style="{ '--card-index': Math.min(index, 8) }">
            <div class="card-header">
                <inertia-link class="card-link" :href="route('post.index', topic.id)"><h3 class="topic-title">{{topic.title}}</h3></inertia-link>
            </div>
            <div class="card-body">
                <inertia-link v-if="topic.user" class="meta-link" :href="route('user.profile', topic.user.username)">
                    <p><span class="meta-label">Author:</span> <span class="meta-value">{{ topic.user.username }}</span></p>
                </inertia-link>
                <inertia-link v-if="topic.category" class="meta-link" :href="route('post.show', { category_id : topic.category.id })">
                    <p><span class="meta-label">Category:</span> <span class="meta-value">{{ topic.category.name }}</span></p>
                </inertia-link>
                <p><span class="meta-label">Posted:</span> <time :datetime="topic.createdAt">{{ formatDate(topic.createdAt) }}</time></p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">

// Components
import PageLoader from "./PageLoader.vue";

// Types
import { Post } from "../types/Post";

defineOptions({
    name: "Posts"
});

defineProps<{
    topics: Post[] | []
}>();
</script>

<style lang="sass" scoped>
a, a:hover, a:focus, a:active
    text-decoration: none
    color: inherit
.topic-title
    font-size: 1.25rem
    margin: 0
    display: -webkit-box
    -webkit-line-clamp: 2
    line-clamp: 2
    -webkit-box-orient: vertical
    overflow: hidden
    overflow-wrap: anywhere
.container
    display: grid
    grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr))
    grid-column-gap: 20px
    grid-row-gap: 15px
    height: fit-content
    .card-body
        padding: 12px 16px 16px
        p
            display: grid
            grid-template-columns: 5.5rem auto
            gap: 8px
            margin: 0 0 8px
            &:last-child
                margin-bottom: 0
    .meta-label
        color: var(--color-text-muted)
    // The title link stretches over the whole card, so the card is one click target.
    .card-link
        &::after
            content: ""
            position: absolute
            inset: 0
            border-radius: 25px
        &:focus-visible
            outline: none
            &::after
                outline: 2px solid var(--color-text)
                outline-offset: 2px
    // Author and category links sit above the stretched title link and keep their own routes.
    .meta-link
        position: relative
        z-index: 1
        display: block
        width: fit-content
        padding-block: 4px
        margin-block: -4px
        &:focus-visible
            outline: 2px solid var(--color-text)
            outline-offset: 2px
            border-radius: 4px
    .meta-value
        text-decoration: underline
        text-decoration-color: var(--color-text-muted)
        text-underline-offset: 3px
        transition: text-decoration-color 0.15s
    .meta-link:hover .meta-value,
    .meta-link:focus-visible .meta-value
        text-decoration-color: var(--color-text)
    .card
        position: relative
        background: var(--color-surface-alt)
        color: var(--color-text)
        border-radius: 25px
        animation: posts-load 400ms ease-out backwards
        animation-delay: calc(var(--card-index, 0) * 60ms)
        transition: transform 0.15s, box-shadow 0.15s
        &:hover
            transform: translateY(-5px)
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3)
    @keyframes posts-load
        0%
            opacity: 0
            transform: translateX(-24px)
        100%
            opacity: 1
            transform: translateX(0)
@media screen and (max-width: 600px)
    .container
        .grid:hover
            transform: none
@media (prefers-reduced-motion: reduce)
    .container
        .card
            animation: none
            transition: none
            &:hover
                transform: none
        .meta-value
            transition: none
</style>
