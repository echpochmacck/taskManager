<script setup>
  import {RouterLink, RouterView} from 'vue-router'
  import HelloWorld from './components/HelloWorld.vue'
</script>

<template>
  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Task Manager</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="#register">Register</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#login">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#dashboard">Tasks</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

  </header>

  <RouterView />
</template>

