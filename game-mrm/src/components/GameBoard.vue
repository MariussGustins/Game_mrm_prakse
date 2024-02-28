<template>
  <div>
    <div class="container">
      <!-- Burger menu icon -->
      <div class="burger-menu-icon" @click="toggleSideMenu">
        <i class="fas fa-bars"></i>
      </div>
      <!-- Side menu -->
      <div class="side-menu" :class="{ 'show': isSideMenuOpen }">
        <!-- Close button -->
        <div class="close-button" @click="toggleSideMenu">
          <i class="fas fa-times"></i>
        </div>
        <!-- Menu content -->
        <div class="menu-content">
          <button class="button" @click="startGameAndCloseMenu">Start</button>
          <button class="button" @click="restartGameAndCloseMenu">Restart</button>
          <button class="button" @click="openShopAndCloseMenu">Shop</button>
          <button class="button" @click="logoutAndCloseMenu">Logout</button>
        </div>
      </div>
      <!-- Game board -->
      <div id="game-board">
        <div v-for="(row, rowIndex) in visibleBoard" :key="rowIndex">
          <div v-for="(cell, colIndex) in row" :key="colIndex" :class="{ 'cell': true, 'snake': isSnake(rowIndex, colIndex), 'food': isFood(rowIndex, colIndex) }">
            <!-- Apply inline style binding here for the snake's background image -->
            <div
                v-if="isSnake(rowIndex, colIndex)"
                :style="{ backgroundImage: 'url(' + require(`@/assets/${currentSnakeSkin}`) + ')' }"
                class="snake-cell"
            ></div>
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
      <div class="onscreen-controls" v-if="isMobile">
        <button @click="changeDirection('left')">Up</button>
        <button @click="changeDirection('up')">Left</button>
        <button @click="changeDirection('down')">Right</button>
        <button @click="changeDirection('right')">Down</button>
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
        <div class="player-data-header">Top 10 Score Board</div>
        <div class="player-data-info">
          <div class="info-item">
            <span class="info-label"></span>
            <ul class="top-scores-list">
              <li v-for="(topScore, index) in topScores" :key="index">
                <span class="score-place">{{ index + 1 }}.</span>
                <span class="score-details">
            <span class="username">{{ topScore.username }}:</span>
            <span class="score">{{ topScore.highest_score }}</span>
          </span>
              </li>
            </ul>
          </div>
        </div>

      </div>

      <!-- Shop popup -->
      <div v-if="showShop" ref="shopPopup" class="shop-popup">
        <div class="skin-container">
          <div v-for="(skin, index) in skins" :key="index">
            <img :src="require(`@/assets/${skin.image}`)" alt="Skin" class="snake-skin-img" @click="selectSkin(index)" />
            <p>Cost: {{ skin.cost }} points</p>
          </div>
        </div>
        <button class="button" @click="closeShop">Close</button>
      </div>
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
      snake: [],
      direction: 'right',
      food: {},
      gameOver: false,
      score: 0,
      showShop: false,
      skins: [
        {image: 'food.jpeg', cost: 0 },
        { image: 'food2.png', cost: 1 },
        { image: 'skin.jpeg', cost: 20 },
        // Add more skins as needed
      ],
      currentSnakeSkin: 'food.jpeg', // default skin image
      highestScore: 0,
      topScores: [],
      showMenu: false,
      isMobile: false,
      isSideMenuOpen: false,
      screenWidth: window.innerWidth,
      screenHeight: window.innerHeight,

    };
  },
  computed: {
    arrowDirection() {
      return 'arrow-' + this.direction;
    },
    visibleBoard() {
      let rows, cols;
      if (this.screenWidth <= 320) {
        rows = 9;
        cols = 8;
      } else if (this.screenWidth <= 424) {
        rows = 11;
        cols = 9;
      } else if (this.screenWidth <= 425) {
        rows = 13;
        cols = 12;
      } else if (this.screenWidth <= 768) {
        rows = 20;
        cols = 18;
      } else {
        rows = 20;
        cols = 20;
      }
      return this.board.slice(0, rows).map(row => row.slice(0, cols));
    },

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

    this.getHighestScore();
    this.getTopScores();
    this.pollTopScores();

    window.addEventListener('resize', this.handleResize);
    this.checkMobile(); // Check if the screen is mobile on mount
    window.addEventListener('resize', this.checkMobile); // Listen for resize events

  },
  methods: {
    checkMobile() {
      this.isMobile = window.innerWidth <= 768; // Adjust breakpoint as needed
    },
    changeDirection(newDirection) {
      // Handle direction change based on button clicks
      this.direction = newDirection;
    },
    handleResize() {
      this.screenWidth = window.innerWidth;
      this.screenHeight = window.innerHeight;
      this.setupBoard();
      this.placeFood();
    },
    toggleSideMenu() {
      this.isSideMenuOpen = !this.isSideMenuOpen;
    },
    startGameAndCloseMenu() {
      this.startGame();
      this.toggleSideMenu();
    },
    restartGameAndCloseMenu() {
      this.restartGame();
      this.toggleSideMenu();
    },
    openShopAndCloseMenu() {
      this.openShop();
      this.toggleSideMenu();
    },
    logoutAndCloseMenu() {
      this.logout();
      this.toggleSideMenu();
    },
    setupBoard() {
      this.board = [];
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
      // Ensure that the food is placed within the visible board boundaries
      do {
        row = Math.floor(Math.random() * this.visibleBoard.length);
        col = Math.floor(Math.random() * this.visibleBoard[0].length);
      } while (this.isSnake(row, col));
      this.food = {row, col};
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
      if (head.row < 1 || head.row >= this.visibleBoard.length -1 || head.col < 1 || head.col >= this.visibleBoard[0].length -1 || this.snake.slice(1).some(segment => segment.row === head.row && segment.col === head.col)) {
        this.gameOver = true;
        this.handleGameOver();
      }
    },
    getHighestScore() {
      const sessionUsername = sessionStorage.getItem('username');
      if (sessionUsername) {
        // Fetch the highest score for the logged-in user from the database
        axios.get(`http://localhost/Game_mrm_prakse/game-mrm/src/api/api.php?action=get_highest_score&username=${sessionUsername}`)
            .then(response => {
              console.log("Response from API:", response.data);
              if (response.data && !response.data.error && response.data.highestScore !== null) {
                console.log("Highest Score:", response.data.highestScore);
                // Update highestScore data property
                this.highestScore = response.data.highestScore;
              }
            })
            .catch(error => {
              console.error("Error fetching highest score:", error);
            });
      } else {
        console.error("Session username is missing.");
      }
    },
    getTopScores() {
      axios.get(`http://localhost/Game_mrm_prakse/game-mrm/src/api/api.php?action=get_top_scores`)
          .then(response => {
            if (!response.data.error) {
              this.topScores = response.data.topScores;
            }
          })
          .catch(error => {
            console.error("Error fetching top scores:", error);
          });
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
      // Save the current score
      const currentScore = this.score;

      if (currentScore > this.highestScore) {
        this.highestScore = currentScore;
      }

      this.getTopScores();

      // Remove event listener for keydown events
      window.removeEventListener('keydown', this.handleKeyPress);
      this.gameOver = true;
      setTimeout(() => {
        this.$refs.gameOverPopup.classList.add('active');
      }, 50); // Adjust the delay as needed
    },
    pollTopScores() {
      setInterval(() => {
        this.getTopScores();
      }, 600);
    },
    closeGameOverPopup() {
      // Hide game over popup
      this.$refs.gameOverPopup.classList.remove('active');
      // Remove 'active' class from the game over popup
    },
    startGame() {
      this.gameOver = false;
      this.score = 0;
      // Clear the snake cells
      const snakeCells = document.querySelectorAll('.snake-cell');
      snakeCells.forEach(cell => {
        cell.style.backgroundImage = '';
      });
      // Reset the snake with new skin
      this.snake = [{ row: Math.floor(this.visibleBoard.length / 2), col: Math.floor(this.visibleBoard[0].length / 2) }];        // Update the default skin image to the newly selected skin
      this.currentSnakeSkin = this.skins.find(skin => skin.image === this.currentSnakeSkin)?.image || this.skins[0].image;
      this.setupBoard();
      this.placeFood();
      if (this.gameLoopInterval) {
        clearInterval(this.gameLoopInterval);
      }
      if (this.screenWidth <= 425) {
        this.gameLoopInterval = setInterval(() => {
          if (!this.gameOver) {
            this.moveSnake();
          }
        }, 350);
      }else{
        this.gameLoopInterval = setInterval(() => {
          if (!this.gameOver) {
            this.moveSnake();
          }
        }, 200);
      }
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
        // Clear the container holding the skin images
        const skinContainer = this.$refs.shopPopup.querySelector('.skin-container');
        skinContainer.innerHTML = '';
        // Close the shop popup
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

/* Styling for the burger menu icon */
.burger-menu-icon {
  cursor: pointer;
  color: #222831; /* Change the color as needed */
}

/* Styling for the side menu */
.side-menu {
  position: fixed;
  top: 0;
  left: 0;
  width: 250px; /* Adjust the width as needed */
  height: 100%;
  background-color: #333; /* Adjust the background color as needed */
  transform: translateX(-100%);
  transition: transform 0.3s ease-in-out;
  z-index: 1000; /* Ensure the side menu appears on top */
}

.side-menu.show {
  transform: translateX(0);
}

/* Styling for the close button */
.close-button {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
  color: #fff; /* Close button color */
}

/* Menu content styling */
.menu-content {
  padding: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;

}

/* Button styling */
.button {
  display: block;
  width: 100%;
  margin-bottom: 10px;
  background-color: #222;
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 10px;
  cursor: pointer;
  text-align: center;
  font-size: 16px;
  transition: background-color 0.3s;
}

.button:hover {
  background-color: #555;
}

/* Style for burger menu icon */
.fa-bars {
  font-size: 24px;
  color: #222831;
}

.snake-cell {
  width: 25px;
  height: 28px;
  border: 2px solid #393e46;
  background-size: cover;
}

.container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
}

#game-board {
  position: relative;
  display: grid;
  grid-template-columns: repeat(20, 30px);
  border: 5px solid #393e46;
  background-color: #222831;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  margin-left:5px;
}

.cell {
  width: 30px; /* Original size */
  height: 30px; /* Original size */
  border: 2px solid #393e46; /* Border for each cell */
  max-width: 100%; /* Ensure cells do not extend beyond the game board */
  max-height: 100%;
}

.snake {
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

.player-data {
  background-color: #f0f0f0;
  border-radius: 10px;
  padding: 10px;
  margin-top: 2px;
  margin-right: 10px;
  margin-left: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  flex-basis: calc(50% - 20px); /* Adjust width and margin as needed */
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .container {
    flex-direction: column;
    align-items: center;
  }

  .player-data {
    flex-basis: calc(100% - 20px);
  }
}

@media (min-width: 992px) {
  .container {
    justify-content: space-between; /* Adjust the space between as needed */
  }
}

@media (width: 1024px){
  .container {
    display: flex;
    justify-content: center; /* Center the container horizontally */
    align-items: center; /* Center the container vertically */
  }
  .player-data {
    background-color: #f0f0f0;
    border-radius: 10px;
    padding: 10px;
    margin-top: 8px;
    margin-right: -17px;
    margin-left: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    flex-basis: calc(50% - -86px); /* Adjust width and margin as needed */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
  }

}

/* Adjust game board size for smaller screens */
@media (max-width: 768px) {
  #game-board {
    width: 78vw; /* Adjust width for smaller screens */
    height: 81vw; /* Adjust height to maintain aspect ratio */
  }

  .container {
    display: flex;
    justify-content: center; /* Center the container horizontally */
    align-items: center; /* Center the container vertically */
    flex-direction: row-reverse;
  }

  .cell {
    width: calc(80vw / 20); /* Adjust cell width */
    height: calc(80vw / 20); /* Adjust cell height */
  }
  .snake-cell {
    width: calc(70vw / 20); /* Adjust cell width */
    height: calc(75vw / 20); /* Adjust cell height */
  }
  .player-data {
    background-color: #f0f0f0;
    border-radius: 10px;
    padding: 162px;
    margin-top: 20px;
    margin-right: -17px;
    margin-left: -32px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    flex-basis: calc(50% - 90px); /* Adjust width and margin as needed */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
  }
}

@media (max-width: 425px) {
  #game-board {
    width: 92vw; /* Adjust width for smaller screens */
    height: 91vw; /* Adjust height to maintain aspect ratio */
  }

  .container {
    display: flex;
    justify-content: center; /* Center the container horizontally */
    align-items: center; /* Center the container vertically */
    flex-direction: row-reverse;
  }

  .cell {
    width: calc(90vw / 13.5); /* Adjust cell width */
    height: calc(90vw / 13.5); /* Adjust cell height */
  }
  .snake-cell {
    width: calc(79vw / 13.5); /* Adjust cell width */
    height: calc(84vw / 13.5); /* Adjust cell height */
  }
  .player-data {
    background-color: #f0f0f0;
    border-radius: 10px;
    padding: 82px;
    margin-top: 20px;
    margin-right: -17px;
    margin-left: -12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    flex-basis: calc(50% - 90px); /* Adjust width and margin as needed */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
  }
}

@media (max-width: 375px) {
  #game-board {
    width: 89vw; /* Adjust width for smaller screens */
    height: 95vw; /* Adjust height to maintain aspect ratio */
  }

  .container {
    display: flex;
    justify-content: center; /* Center the container horizontally */
    align-items: center; /* Center the container vertically */
    flex-direction: row-reverse;
  }

  .cell {
    width: calc(95vw / 10); /* Adjust cell width */
    height: calc(95vw / 10); /* Adjust cell height */
  }
  .snake-cell {
    width: calc(74vw / 10); /* Adjust cell width */
    height: calc(87vw / 10); /* Adjust cell height */
  }
  .player-data {
    background-color: #f0f0f0;
    border-radius: 10px;
    padding: 55px;
    margin-top: 20px;
    margin-right: -17px;
    margin-left: -12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    flex-basis: calc(50% - 90px); /* Adjust width and margin as needed */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
  }

}

@media (max-width: 320px) {
  #game-board {
    width: 85vw; /* Adjust width for smaller screens */
    height: 90vw; /* Adjust height to maintain aspect ratio */
  }
  .container {
    display: flex;
    justify-content: center; /* Center the container horizontally */
    align-items: center; /* Center the container vertically */
    flex-direction: row-reverse;
  }

  .cell {
    width: calc(100vw / 10); /* Adjust cell width */
    height: calc(100vw / 10); /* Adjust cell height */
  }
  .snake-skin-img {
    width: calc(100vw / 10); /* Adjust cell width */
    height: calc(100vw / 10); /* Adjust cell height */
  }
  .snake-cell {
    width: calc(83vw / 10); /* Adjust cell width */
    height: calc(95vw / 10); /* Adjust cell height */
  }
  .player-data {
    background-color: #f0f0f0;
    border-radius: 10px;
    padding: 23px;
    margin-top: 20px;
    margin-right: -17px;
    margin-left: -10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    flex-basis: calc(50% - 70px); /* Adjust width and margin as needed */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
  }

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

.shop-popup {
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
.top-scores-list {
  list-style-type: none;
  padding: 0;
  text-align: left;
  font-weight: bold;
}
.score-place {
  font-weight: bold;
  margin-right: 5px;
}
.score-details {
  margin: 0; /* Reset margin */
}
.username {
  color: #007bff; /* Highlight username */
  margin-right: 5px;
}
.score {
  color: #28a745; /* Highlight score */
}

.onscreen-controls {
  display: flex;
  justify-content: center;
  margin-top: 10px;
}

.onscreen-controls button {
  background-color: #222831;
  border: none;
  color: white;
  font-size: 14px;
  padding: 5px 10px;
  cursor: pointer;
  border-radius: 5px;
  margin: 0 5px;
  position: relative;
}

.onscreen-controls button:nth-child(1) {
  transform: rotate(0deg);
}

.onscreen-controls button:nth-child(2) {
  transform: rotate(0deg);
}

.onscreen-controls button:nth-child(3) {
  transform: rotate(0deg);
}

.onscreen-controls button:nth-child(4) {
  transform: rotate(0deg);
}

</style>
