<template>
  <div>
    <div class="container">
      <!-- Game board -->
      <div id="game-board">
        <div v-for="(row, rowIndex) in board" :key="rowIndex">
          <div v-for="(cell, colIndex) in row" :key="colIndex" :class="{ 'cell': true, 'snake': isSnake(rowIndex, colIndex), 'food': isFood(rowIndex, colIndex) }">
            <!-- Apply inline style binding here for the snake's background image -->
            <div :style="{ backgroundImage: 'url(' + require(`@/assets/${currentSnakeSkin}`) + ')' }"></div>
          </div>
        </div>
        <!-- Game Over popup -->
        <div v-if="gameOver" ref="gameOverPopup" class="game-over-popup">
          <div>
            Game Over!
            <span v-if="score > 1">You got {{ score }} points</span>
            <span v-else-if="score === 0">You didn't get any point</span>
            <span v-else>You got {{ score }} point</span>
          </div>
          <div class="button-container">
            <button @click="restartGame">Restart</button>
            <button @click="closeGameOverPopup">Close</button>
          </div>
        </div>
        <div class="arrow" :class="arrowDirection"></div>
      </div>
      <!-- Player's data section -->
      <div class="player-data">
        <div class="player-data-header">Player Data</div>
        <div class="player-data-info">
          <div class="info-item">
            <span class="info-label">Username:</span>
            <span class="info-value">{{ loggedInUser }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Score:</span>
            <span class="info-value">{{ score }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Highest Score:</span>
            <span class="info-value">{{ highestScore }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Current Snake Skin:</span>
            <img :src="require(`@/assets/${currentSnakeSkin}`)" alt="Snake Skin" class="snake-skin-img">
          </div>
        </div>
      </div>
    </div>
    <!-- Buttons -->
    <button class="button" @click="startGame">Start</button>
    <button class="button" @click="restartGame">Restart</button>
    <button class="button" @click="openShop">Shop</button>
    <button class="button" @click="logout()">Logout</button>

    <!-- Shop popup -->
    <div v-if="showShop" ref="shopPopup" class="shop-popup">
      <div v-for="(skin, index) in skins" :key="index">
        <img :src="require(`@/assets/${skin.image}`)" alt="Skin" class="snake-skin-img" @click="selectSkin(index)" />
        <p>Cost: {{ skin.cost }} points</p>
      </div>
      <button class="button" @click="closeShop">Close</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
    data() {
        return {
            loggedInUser: '',
            board: [],
            snake: [], // Initialize as an empty array
            direction: 'right',
            food: {},
            gameOver: false,
            score: 0,
            showShop: false,
            skins: [
                { image: 'food2.png', cost: 5 },
                { image: 'logo.png', cost: 20 },
                // Add more skins as needed
            ],
            currentSnakeSkin: 'food.jpeg', // default skin image
            highestScore: 0,

        };
    },
    computed: {
        arrowDirection() {
            return 'arrow-' + this.direction;
        },
        currentSnakeSkinImageUrl() {
          return require(`@/assets/${this.currentSnakeSkin}`);
      }
    },
    mounted() {
        this.setupBoard();
        this.placeFood();
        window.addEventListener('keydown', this.handleKeyPress);

      const sessionUsername = sessionStorage.getItem('username');
      if (sessionUsername) {
        this.loggedInUser = sessionUsername;
      } else {
        this.loggedInUser = 'User not logged in';
      }

    },
    methods: {
        setupBoard() {
          this.board = []; //pievienoju sito
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
            this.food = {row, col};
        },
        moveUp() {
            if (this.direction !== 'down') this.direction = 'up';
        },
        moveDown() {
            if (this.direction !== 'up') this.direction = 'down';
        },
        moveLeft() {
            if (this.direction !== 'right') this.direction = 'left';
        },
        moveRight() {
            if (this.direction !== 'left') this.direction = 'right';
        },
        handleKeyPress(event) {
            switch (event.key) {
                case 'ArrowUp':
                    this.direction = 'left';
                    break;
                case 'ArrowDown':
                    this.direction = 'right';
                    break;
                case 'ArrowLeft':
                    this.direction = 'up';
                    break;
                case 'ArrowRight':
                    this.direction = 'down';
                    break;
            }
        },
        moveSnake() {
            const head = {...this.snake[0]};
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
                this.handleGameOver();
            }
        },
      handleGameOver() {
        if (this.score > this.highestScore) {
          this.highestScore = this.score;
        }
        // Create a FormData object to send data to the API
        const formData = new FormData();
        formData.append("score", this.score);
        formData.append("best_result", this.highestScore);
        formData.append("logusername", this.loggedInUser);

        // Send a POST request to the API endpoint
        axios.post("http://localhost/Game_mrm_prakse/game-mrm/src/api/api.php?action=save_score", formData)
            .then(response => {
              console.log("Received data:", response.data);
              // Update highest score if necessary
              if (!response.data.error && response.data.highestScore) {
                this.highestScore = response.data.highestScore;
              }
            })
            .catch(error => {
              console.error("Error:", error);
            });

        // Remove event listener for keydown events
        window.removeEventListener('keydown', this.handleKeyPress);
        this.gameOver = true;
        setTimeout(() => {
          this.$refs.gameOverPopup.classList.add('active');
        }, 50); // Adjust the delay as needed
      },
      closeGameOverPopup() {
        // Hide game over popup
        this.$refs.gameOverPopup.classList.remove('active');
        // Remove 'active' class from the game over popup
      },

      gameLoop() {
            setInterval(() => {
                if (!this.gameOver) {
                    this.moveSnake();
                }
            }, 200);
        },
        startGame() {
            this.gameOver = false;
            this.score = 0;
            this.snake = [{row: 10, col: 10}]; // Reset the snake length
            this.currentSnakeSkin = 'food.jpeg';
            this.setupBoard();
            this.placeFood();
            if (this.gameLoopInterval) {
                clearInterval(this.gameLoopInterval);
            }
            this.gameLoopInterval = setInterval(() => {
                if (!this.gameOver) {
                    this.moveSnake();
                }
            }, 200);
            window.addEventListener('keydown', this.handleKeyPress);
        },


        restartGame() {
            this.startGame();
        },
        openShop() {
          this.showShop = true;
          setTimeout(() => {
            this.$refs.shopPopup.classList.add('active');
          }, 50); // Adjust the delay as needed
        },
        closeShop() {
          this.$refs.shopPopup.classList.remove('active');
          setTimeout(() => {
            this.showShop = false;
          }, 300); // Match the transition duration
        },
      selectSkin(index) {
        const selectedSkin = this.skins[index];
        if (this.highestScore >= selectedSkin.cost) {
          this.currentSnakeSkin = selectedSkin.image; // Update the current snake skin
          // this.score -= selectedSkin.cost;
          this.closeShop();
        } else {
          alert("You don't have enough points to purchase this skin!");
        }
        },
        logout() {
          this.$router.push({path: '/'});
        },
    },
};
</script>

<style scoped>
.container {
  display: flex;
  justify-content: space-between;
}
#game-board {
  position: relative;
  display: grid;
  grid-template-columns: repeat(20, 30px);
  border: 5px solid #393e46;
  background-color: #222831;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  margin-left:60px;
}

.cell {
  width: 30px; /* Original size */
  height: 30px; /* Original size */
  border: 2px solid #393e46; /* Border for each cell */
}

.snake {
  background-image: url('../assets/food.jpeg');
  background-size: cover;
}

.food {
    background-image: url("../assets/food3.png");
  background-size: cover;
}

.arrow {
    position: absolute;
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-bottom: 10px solid black;
    transition: transform 0.2s;
}

.arrow-up {
    top: -10px;
    left: 50%;
    transform: translateX(-50%) rotate(180deg);
}

.arrow-down {
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
}

.arrow-left {
    left: -10px;
    top: 50%;
    transform: translateY(-50%) rotate(90deg);
}

.arrow-right {
    right: -10px;
    top: 50%;
    transform: translateY(-50%) rotate(-90deg);
}

.player-data {
  background-color: #f0f0f0;
  border-radius: 10px;
  padding: 20px;
  margin-top: 40px;
  margin-right: 70px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.player-data-header {
  font-size: 30px;
  font-weight: bold;
  margin-bottom: 10px;
}

.player-data-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.info-item {
  display: flex;
  align-items: center;
}

.info-label {
  font-weight: bold;
  margin-right: 10px;
}

.info-value {
  color: #555;
}


.shop-popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  padding: 20px;
  z-index: 999;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
}

.shop-popup.active {
  opacity: 1;
}
.button {
  margin: 20px;
  display: inline-block;
  background-color: #222831;
  border: none;
  color: white;
  text-align: center;
  font-size: 16px;
  padding: 10px 20px;
  cursor: pointer;
  border-radius: 5px;
  text-decoration: none;
  transition: background-color 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.button:hover {
  background-color: #222831;
}

.button:active {
  background-color: #222831;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.game-over-popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #f0f0f0;
  color: #222831;
  padding: 20px;
  border-radius: 10px;
  z-index: 999;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
}

.game-over-popup.active {
  opacity: 1;
}

.game-over-popup span {
  display: block;
  margin-top: 10px;
}

.game-over-popup button {
  background-color: #222831;
  border: none;
  color: white;
  padding: 10px 20px;
  cursor: pointer;
  border-radius: 5px;
  text-decoration: none;
  transition: background-color 0.3s;
  margin-top: 20px;
}

.game-over-popup button:hover {
  background-color: #222831;
}

.game-over-popup button:active {
  background-color: #222831;
}

.button-container {
  margin-top: 20px;
}

.game-over-popup .button-container button {
  margin-right: 10px;
}

.snake-skin-img {
  max-width: 40px; /* Adjust the max width as needed */
  max-height: 40px; /* Adjust the max height as needed */
}
</style>
