<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <router-link class="navbar-brand" :to="{ name: 'home' }">EasyVHost Virtual Hosts Manager</router-link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'home' }">Hosts</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'host_edit', params: { id: '0' } }">Add Host</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'tags' }">Tags</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'settings' }">Settings</router-link>
                    </li>
                </ul>
                <span class="navbar-text">
                    <!-- TEXT -->
                </span>
            </div>
        </div>
    </nav>
    <div class="container-fluid main mt-3">

        <router-view></router-view>

    </div>
</template>