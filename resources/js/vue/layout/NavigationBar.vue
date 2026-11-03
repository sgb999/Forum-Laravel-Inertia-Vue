<template>
    <nav
        aria-label="Main navigation"
        :class="{ 'nav--open': isMenuOpen }"
        @keydown.esc="closeMenu(true)"
    >
        <ul class="nav-home" @click="closeMenu()">
            <li>
                <inertia-link
                    :href="route('post.show')"
                    :class="{ active: isActive('post/Show') }"
                    :aria-current="isActive('post/Show') ? 'page' : undefined"
                ><span class="bi bi-house-fill me-1"></span>Home</inertia-link>
            </li>
        </ul>
        <button
            ref="menuButton"
            type="button"
            class="nav-toggle"
            :aria-expanded="isMenuOpen"
            :aria-label="isMenuOpen ? 'Close menu' : 'Open menu'"
            aria-controls="main-navigation-panel"
            @click="toggleMenu"
        >
            <span class="nav-toggle__bar" aria-hidden="true"></span>
            <span class="nav-toggle__bar" aria-hidden="true"></span>
            <span class="nav-toggle__bar" aria-hidden="true"></span>
        </button>
        <div
            v-if="isMenuOpen"
            class="nav-backdrop"
            aria-hidden="true"
            @click="closeMenu()"
        ></div>
        <div
            id="main-navigation-panel"
            ref="menuPanel"
            class="nav-panel"
            :class="{ 'is-open': isMenuOpen }"
            @click="closeMenu()"
        >
            <ul class="nav-links">
                <li>
                    <inertia-link
                        :href="route('categories.show')"
                        prefetch
                        :class="{ active: isActive('categories') }"
                        :aria-current="isActive('categories') ? 'page' : undefined"
                    >View Categories</inertia-link>
                </li>
            </ul>
            <ul v-if="isLoggedIn" class="nav-actions">
                <li >
                    <inertia-link
                        :href="route('user.profile', username)"
                        prefetch
                        :class="{ active: isActive('user/Profile') }"
                        :aria-current="isActive('user/Profile') ? 'page' : undefined"
                    >Profile</inertia-link>
                </li>
                <li>
                    <inertia-link
                        :href="route('chat.show')"
                        prefetch
                        :class="{ active: isActive('messages') }"
                        :aria-current="isActive('messages') ? 'page' : undefined"
                    ><span class="bi bi-envelope-fill yellow me-1"></span>Messages</inertia-link>
                </li>
                <li>
                    <button type="button" @click="logout">
                        <span class="bi bi-box-arrow-right red me-1"></span> Sign out
                    </button>
                </li>
            </ul>
            <ul v-if="!isLoggedIn" class="nav-actions">
                <li>
                    <inertia-link
                        :href="route('login.index')"
                        prefetch
                        :class="{ active: isActive('user/Login') }"
                        :aria-current="isActive('user/Login') ? 'page' : undefined"
                    ><span class="bi bi-box-arrow-in-right me-1 green"></span>Login</inertia-link>
                </li>
                <li>
                    <inertia-link
                        :href="route('register.index')"
                        prefetch
                        :class="{ active: isActive('user/Register') }"
                        :aria-current="isActive('user/Register') ? 'page' : undefined"
                    ><span class="bi bi-person-fill me-1 green"></span>Sign Up</inertia-link>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script setup lang="ts">
// Vue
import { computed, nextTick, ref, watch } from "vue";

// Inertia
import { usePage, router } from "@inertiajs/vue3";

defineOptions({
    name: "NavigationBar"
});

const page = usePage();

const isLoggedIn = computed(() => page.props.auth.login);
const username = computed(() => page.props.auth.user.username ?? undefined);

const isMenuOpen = ref(false);
const menuButton = ref<HTMLButtonElement | null>(null);
const menuPanel = ref<HTMLElement | null>(null);

function isActive(component: string): boolean
{
    return page.component.startsWith(component);
}

function closeMenu(returnFocus = false): void
{
    if (!isMenuOpen.value) {
        return;
    }

    isMenuOpen.value = false;

    if (returnFocus) {
        nextTick(() => menuButton.value?.focus());
    }
}

function toggleMenu(): void
{
    if (isMenuOpen.value) {
        closeMenu();

        return;
    }

    isMenuOpen.value = true;

    nextTick(() => menuPanel.value?.querySelector<HTMLElement>("a, button")?.focus());
}

watch(() => page.url, () => closeMenu());

function logout(): void
{
    router.post(route('logout'));
}
</script>

<style scoped lang="sass">
nav
    position: sticky
    top: 0
    z-index: 1000
    display: flex
    flex-wrap: wrap
    align-items: center
    justify-content: space-between
    padding: 0 8px
    background-color: var(--color-surface)
    ul
        display: flex
        flex-wrap: wrap
        list-style-type: none
        margin: 0
        padding: 0
    li
        display: flex
        padding: 0 2px
    a, button
        display: flex
        align-items: center
        justify-content: center
        padding: 10px 16px
        margin: 4px 0
        border: 0
        border-radius: 10px
        background: none
        color: var(--color-text-muted)
        text-align: center
        text-decoration: none
        cursor: pointer
        &:hover,
        &:focus-visible
            background-color: var(--color-raised)
        &:focus-visible
            outline: 2px solid var(--color-text-muted)
            outline-offset: 2px
        &.active
            background-color: var(--color-raised)
            color: var(--color-text-strong)
        .green
            color: var(--color-success)
        .yellow
            color: var(--color-warning)
        .red
            color: var(--color-danger)
.nav-panel
    display: flex
    flex: 1
    flex-wrap: wrap
    align-items: center
    justify-content: space-between
.nav-backdrop
    position: fixed
    top: 52px
    right: 0
    bottom: 0
    left: 0
    background-color: rgba(0, 0, 0, 0.5)
    @media (min-width: 601px)
        display: none
.nav-toggle
    display: none
    flex-direction: column
    align-items: center
    justify-content: center
    gap: 4px
    width: 40px
    height: 40px
    padding: 0
    margin: 0
    border: 0
    border-radius: 10px
    background: none
    color: var(--color-text-muted)
    cursor: pointer
    &:hover,
    &:focus-visible
        background-color: var(--color-raised)
    &:focus-visible
        outline: 2px solid var(--color-text-muted)
        outline-offset: 2px
    &__bar
        display: block
        width: 20px
        height: 2px
        border-radius: 2px
        background-color: currentColor
        transition: transform 150ms ease, opacity 150ms ease
.nav--open .nav-toggle
    .nav-toggle__bar:nth-child(1)
        transform: translateY(6px) rotate(45deg)
    .nav-toggle__bar:nth-child(2)
        opacity: 0
    .nav-toggle__bar:nth-child(3)
        transform: translateY(-6px) rotate(-45deg)
@media (max-width: 600px)
    nav
        flex-wrap: nowrap
        justify-content: space-between
        min-height: 52px
        padding: 0 8px
    .nav-toggle
        display: inline-flex
    .nav-panel
        position: absolute
        top: 100%
        right: 0
        left: 0
        flex: none
        flex-direction: column
        align-items: stretch
        max-height: calc(100vh - 52px)
        max-height: calc(100dvh - 52px)
        padding: 4px 8px 8px
        overflow-y: auto
        overscroll-behavior: contain
        border-top: 1px solid var(--color-raised)
        background-color: var(--color-surface)
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.35)
        opacity: 0
        visibility: hidden
        transform: translateY(-8px)
        transition: opacity 150ms ease, transform 150ms ease, visibility 0s linear 150ms
        &.is-open
            opacity: 1
            visibility: visible
            transform: translateY(0)
            transition: opacity 150ms ease, transform 150ms ease
        ul
            flex-direction: column
            width: 100%
        li
            width: 100%
            padding: 0
        a, button
            width: 100%
            padding: 10px 12px
            margin: 2px 0
            white-space: nowrap
@media (prefers-reduced-motion: reduce)
    .nav-panel
        transition: none
</style>
