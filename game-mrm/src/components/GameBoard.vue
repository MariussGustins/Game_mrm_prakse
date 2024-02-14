<template>
  <div id="game-board">
    <div v-for="(row, rowIndex) in board" :key="rowIndex">
      <div v-for="(cell, colIndex) in row" :key="colIndex" :class="{ 'cell': true, 'snake': isSnake(rowIndex, colIndex), 'food': isFood(rowIndex, colIndex) }"></div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      board: [],
      snake: [{ row: 10, col: 10 }],
      direction: 'right',
      food: {}
    };
  },
  mounted() {
    this.setupBoard();
    this.placeFood();
    this.gameLoop();
    window.addEventListener('keydown', this.handleKeyPress);
  },
  methods: {
    setupBoard() {
      for (let i = 0; i < 20; i++) {
        this.board.push(new Array(20).fill(0));
      }
    },
    isSnake(row, col) {
      return this.snake.some(segment => segment.row === row && segment.col === col);
    },
    isFood(row, col) {
      return this.food.row === row && this.food.col === col;
    },
    placeFood() {
      let row, col;
      do {
        row = Math.floor(Math.random() * 20);
        col = Math.floor(Math.random() * 20);
      } while (this.isSnake(row, col));
      this.food = { row, col };
    },
    handleKeyPress(event) {
      switch (event.key) {
        case 'ArrowUp':
          if (this.direction !== 'down') this.direction = 'up';
          break;
        case 'ArrowDown':
          if (this.direction !== 'up') this.direction = 'down';
          break;
        case 'ArrowLeft':
          if (this.direction !== 'right') this.direction = 'left';
          break;
        case 'ArrowRight':
          if (this.direction !== 'left') this.direction = 'right';
          break;
      }
    },
    moveSnake() {
      const head = { ...this.snake[0] };
      switch (this.direction) {
        case 'up':
          head.row--;
          break;
        case 'down':
          head.row++;
          break;
        case 'left':
          head.col--;
          break;
        case 'right':
          head.col++;
          break;
      }
      this.snake.unshift(head);
      if (head.row === this.food.row && head.col === this.food.col) {
        this.score++;
        this.placeFood();
      } else {
        this.snake.pop();
      }
      if (head.row < 0 || head.row >= 20 || head.col < 0 || head.col >= 20 || this.snake.slice(1).some(segment => segment.row === head.row && segment.col === head.col)) {
        this.gameOver = true;
        window.removeEventListener('keydown', this.handleKeyPress);
      }
    },
    gameLoop() {
      setInterval(() => {
        if (!this.gameOver) {
          this.moveSnake();
        }
      }, 200);
    }
  }
};
</script>

<style scoped>
#game-board {
  display: grid;
  grid-template-columns: repeat(20, 20px);
  border: 2px solid black;
}

.cell {
  width: 20px;
  height: 20px;
  border: 1px solid gray;
}

.snake {
  background-color: green;
}

.food {
  background-color: red;
}
</style>
