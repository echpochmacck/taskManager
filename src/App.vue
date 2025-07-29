<script setup>
  import {RouterLink, RouterView} from 'vue-router'
  import {useUserStore} from '@/stores/user.js'
  import HelloWorld from './components/HelloWorld.vue'
  const ws = new WebSocket('ws://localhost:8080');
  const user = useUserStore();
  ws.addEventListener('open', () => {
    console.log('opened')
  })
  ws.addEventListener('message', (data) => {
    console.log('got message')
  })
</script>

<template>


  <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top  border-bottom border-primary">
    <div class="container">
      <router-link class="navbar-brand" to="/">Task Manager</router-link>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item" v-if="!user.token">
            <router-link class="nav-link" to="/register">Register</router-link>
          </li>
          <li class="nav-item" v-if="!user.token">
            <router-link class="nav-link" :to="{name:
              'login'}">Login</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" :to="{name:'tasks'}">Tasks</router-link>
          </li>
          <li class="nav-item" v-if="user.token">
            <router-link class="nav-link" :to="{name:'user-tasks'}">My tasks</router-link>
          </li>

          <!-- <li class="nav-item" v-if="user.token">
            <router-link class="nav-link" :to="{name:'admin-tasks'}">Admin</router-link> -->
          <!-- </li> -->
        </ul>
      </div>
    </div>
  </nav>

  <RouterView />
</template>

<style scoped>
  header {
    line-height: 1.5;
    max-height: 100vh;
  }

  .logo {
    display: block;
    margin: 0 auto 2rem;
  }

  nav {
    font-size: 12px;
    text-align: center;
    margin-top: 0rem !important;
  }

  nav a.router-link-exact-active {
    color: var(--color-text);
  }

  nav a.router-link-exact-active:hover {
    background-color: transparent;
  }

  nav a {
    display: inline-block;
    padding: 0 1rem;
    border-left: 1px solid var(--color-border);
  }

  nav a:first-of-type {
    border: 0;
  }

  @media (min-width: 1024px) {
    header {
      display: flex;
      place-items: center;
      padding-right: calc(var(--section-gap) / 2);
    }

    .logo {
      margin: 0 2rem 0 0;
    }

    header .wrapper {
      display: flex;
      place-items: flex-start;
      flex-wrap: wrap;
    }

    nav {
      text-align: left;
      margin-left: -1rem;
      font-size: 1rem;

      padding: 1rem 0;
      margin-top: 1rem;
    }
  }
</style>