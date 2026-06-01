<template>
    <div class="page-loader">
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
        <div class="cube"></div>
    </div>
</template>

<script>
export default {
    name: "PageLoader"
};
</script>

<style lang="scss" scoped>
    $colours: #8CC271, #69BEEB, #FBAA39, #E9643B;

    .page-loader{
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
        z-index: 999;
        height: 200px;
        width: 400px;
    }
    .cube{
        width: 40px;
        height: 40px;
        margin-right: 10px;

        @for $i from 1 through length($colours){
            &:nth-child(#{$i}){
                background-color: nth($colours, $i);
            }
        }
        &:first-child{
            animation: left 1s infinite;
        }
        &:last-child{
            animation: right 1s infinite 0.5s;
        }
    }

    @keyframes left {
        40%{
            transform: translateX(-60px);
        }
        50% {
            transform: translateX(0);
        }
    }
    @keyframes right {
        40%{
            transform: translateX(60px);
        }
        50% {
            transform: translateX(0);
        }
    }
</style>
