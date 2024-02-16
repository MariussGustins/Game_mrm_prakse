<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
import { createRouter } from 'vue-router'
import LogIn from './components/LogIn.vue';
import GameBoard from './components/GameBoard.vue';
import RegisterForm from './components/RegisterForm.vue';

const routes = [
  { path: '/', component: LogIn },
  { path: '/game', component: GameBoard },
  { path: '/register', component: RegisterForm }
];

const router = new createRouter({
  routes
});

export default {
  name: 'App',
  router
};
</script>

<style>
#app {
  text-align: center;
}
</style>
