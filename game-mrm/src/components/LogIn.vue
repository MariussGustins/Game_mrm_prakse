<template>
  <div class="log-in-container">
    <form>
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" v-model="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" required />
      </div>

      <div class="button-container">
        <button class="log-in-button" @click.prevent="logIn">Log In</button>
        <button class="register-button" @click.prevent="goToRegister">Register</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    logIn() {
      // Implement your login logic here
      console.log('Logging in with username:', this.username);

      // Assuming successful login, navigate to the GameBoard
      this.$router.push('/game-board');
    },
    goToRegister() {
      // Navigate to the Register component
      this.$router.push('/register');
    }
  }
};
</script>

<style scoped>
.log-in-container {
  max-width: 400px;
  margin: auto;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.button-container {
  display: flex;
  justify-content: space-between;
}

.log-in-button {
  background-color: #3498db;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.register-button {
  background-color: #ccc;
  color: #000;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
