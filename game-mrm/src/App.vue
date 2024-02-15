<template>
  <div id="app">
    <LogIn v-if="!loggedIn" @login="handleLogin" @goToRegister="showRegister" />
    <GameBoard v-if="loggedIn" />
    <RegisterForm v-if="showRegisterForm" @backToLogin="backToLogin" />
  </div>
</template>

<script>
import LogIn from './components/LogIn.vue';
import GameBoard from './components/GameBoard.vue';
import RegisterForm from './components/RegisterForm.vue';

export default {
  name: 'App',
  data() {
    return {
      loggedIn: false,
      gameOver: false,
      score: 0,
      showRegisterForm: false
    };
  },
  methods: {
    handleLogin() {
      console.log('Login successful');
      this.loggedIn = true;
      this.showRegisterForm = false;
    },
    showRegister() {
      this.showRegisterForm = true;
    },
    backToLogin() {
      this.loggedIn = false;
      this.showRegisterForm = false;
    }
  },
  components: {
    LogIn,
    GameBoard,
    RegisterForm
  }
};
</script>

<style>
#app {
  text-align: center;
}
</style>
