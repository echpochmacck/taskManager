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

  <div class="d-flex flex-column align-items-center w-100 my-body">


    <nav class="navbar header navbar-expand-lg navbar-light sticky-top  border-bottom border-primary w-100">
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

    <section class="router-view">
      <RouterView />
    </section>

    <footer class="text-center text-lg-start w-100">
      <div class="container">
        <div class="row">
          <!-- Контакты слева -->
          <div class="col-md-6 text-start">
            <h5 class="text-uppercase mb-4">Контакты</h5>
            <ul class="list-unstyled">
              <li class="mb-3">
                <a href="https://t.me/Echpochmaacck" target="_blank">
                  <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg" class="contact-icon"
                    alt="Telegram">
                  Telegram
                </a>
              </li>
              <li class="mb-3">
                <a href="https://github.com/echpochmacck/" target="_blank">
                  <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png"
                    class="contact-icon" alt="GitHub">
                  GitHub
                </a>
              </li>
              <li>
                <a href="mailto:max22062006@gmail.conm">
                  <img src="https://img.icons8.com/ios-filled/24/ffffff/mail.png" class="contact-icon" alt="Email">
                  Email
                </a>
              </li>
            </ul>
          </div>
          <!-- Пустая правая часть -->
          <div class="col-md-6 text-end">
            <!-- Здесь можно добавить что-то позже -->
          </div>
        </div>
        <div class="text-center mt-4">
          <p>&copy; 2025 Echochmak</p>
        </div>
      </div>
    </footer>
  </div>

</template>

<style scoped>
  .router-view {
    flex: 1;
  }

  .header {
    line-height: 1.5;
    max-height: 100vh;
    backdrop-filter: blur(10px) !important;
    z-index: 9 !important;
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


  footer {
    background-color: #1a1a1a;
    color: #aaaaaa;
    padding: 40px 0;
  }

  footer a {
    color: #aaaaaa;
    display: block;
    border: 2px solid transparent;
    transition: .4s;
    text-decoration: none;
  }

  footer a:hover {
    color: #eeeeee;
  }

  .contact-icon {
    width: 24px;
    height: 24px;
    margin-right: 10px;
    vertical-align: middle;
  }
</style>