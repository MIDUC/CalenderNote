<template>
    <div class="flex bg-gray-100 overflow-hidden">
        <main class="flex-1 p-1 overflow-y-auto">
            <RouterView />
        </main>
    </div>
</template>

<script setup>

</script>

<style scoped>
.menu-item {
    display: block;
    padding: 3px 3px;
    border-radius: 5px;
    color: #444;
    font-weight: 300;
}

.menu-item.router-link-exact-active {
    background-color: #6366f1;
    color: white;
}
</style>