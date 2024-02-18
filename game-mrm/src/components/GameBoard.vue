<template>
    <div>
        <div>
            <!-- Buttons -->
            <button @click="startGame">Start</button>
            <button @click="restartGame">Restart</button>
            <button @click="openShop">Shop</button>
            <button @click="logout">Logout</button>

            <!-- Game board -->
            <div id="game-board">
            <div v-for="(row, rowIndex) in board" :key="rowIndex">
                <div v-for="(cell, colIndex) in row" :key="colIndex" :class="{ 'cell': true, 'snake': isSnake(rowIndex, colIndex), 'food': isFood(rowIndex, colIndex) }"></div>
            </div>
            <div v-if="gameOver" class="game-over">Game Over!
                <span v-if="score > 1">You got {{ score }} points</span>
                <span v-if="score === 0">You didn't get any point</span>
                <span v-if="score === 1">You got {{ score }} point</span>
            </div>
            <div class="arrow" :class="arrowDirection"></div>
        </div>

            <!-- Player's data section -->
            <div class="player-data">
                <p>Score: {{ score }}</p>
                <p>Highest Score: {{ highestScore }}</p>
                <p>Current Snake Skin: {{ currentSnakeSkin }}</p>
            </div>

            <!-- Shop popup -->
            <div v-if="showShop" class="shop-popup">
                <div v-for="(skin, index) in skins" :key="index">
                    <img :src="skin.image" alt="Skin" @click="selectSkin(index)" />
                    <p>Cost: {{ skin.cost }} points</p>
                </div>
                <button @click="closeShop">Close</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            board: [],
            snake: [], // Initialize as an empty array
            direction: 'right',
            food: {},
            gameOver: false,
            score: 0,
            showShop: false,
            skins: [
                { image: 'food.jpeg', cost: 5 },
                { image: 'skin2.png', cost: 20 },
                // Add more skins as needed
            ],
            currentSnakeSkin: 'food3.png', // default skin image
            highestScore: 0,
        };
    },
    computed: {
        arrowDirection() {
            return 'arrow-' + this.direction;
        }
    },
    mounted() {
        this.setupBoard();
        this.placeFood();
        window.addEventListener('keydown', this.handleKeyPress);
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
            window.removeEventListener('keydown', this.handleKeyPress);
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
            this.currentSnakeSkin = 'food3.png';
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
        },
        closeShop() {
            this.showShop = false;
        },
        selectSkin(index) {
            const selectedSkin = this.skins[index];
            if (this.score >= selectedSkin.cost) {
                this.currentSnakeSkin = selectedSkin.image;
                this.score -= selectedSkin.cost;
                this.closeShop();
            } else {
                alert("You don't have enough points to purchase this skin!");
            }
        },
        logout() {
            // Add any logic needed for user logout
            // Redirect to the login page or perform other logout actions
        },
    },
};
</script>

<style scoped>
#game-board {
    position: relative;
    display: grid;
    grid-template-columns: repeat(20, 20px);
    border: 2px solid black;
    background: lightgreen;
}

.cell {
    width: 20px;
    height: 20px;
    border: 1px solid gray;
}

.snake {
    background-image: url('food3.png'); /* Default snake skin */
    background-size: cover;
}


.food {
    background-color: red;
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

/* ... existing styles ... */

.player-data {
    position: absolute;
    top: 100px;
    right: 35px;
    text-align: center;
    color: white;
    font-size: 16px;
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
}

/* Add styles for shop content as needed */
</style>
