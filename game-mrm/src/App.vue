<template>
  <div id="app">
    <LogIn v-if="!loggedIn" @login="handleLogin" />
    <GameBoard v-if="loggedIn" />
  </div>
</template>

<script>
import LogIn from './components/LogIn.vue';
import GameBoard from './components/GameBoard.vue';

export default {
  name: 'App',
  data() {
    return {
      loggedIn: false,
      gameOver: false,
      score: 0
    };
  },
  methods: {
    handleLogin() {
      // Implement your login logic here
      console.log('Login successful');

      // Set loggedIn to true after successful login
      this.loggedIn = true;
    }
  },
  components: {
    LogIn,
    GameBoard
  }
};
</script>

<style>
#app {
  text-align: center;
}
</style>
