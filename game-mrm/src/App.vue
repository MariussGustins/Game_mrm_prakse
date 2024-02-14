<template>
  <div id="app">
    <GameBoard />
    <p v-if="gameOver">Game Over! Score: {{ score }}</p>
  </div>
</template>

<script>
import GameBoard from './components/GameBoard.vue';

export default {
  components: {
    GameBoard
  },
  data() {
    return {
      gameOver: false,
      score: 0
    };
  }
};
</script>

<style>
#app {
  text-align: center;
}
</style>
