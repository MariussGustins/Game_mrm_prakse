<template>
  <div class="log-in-container">
    <form @submit.prevent="login">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" v-model="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" required />
      </div>

      <div class="button-container">
        <button class="login-button">Login</button>
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
    async login() {
      try {
        const response = await fetch('login.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            username: this.username,
            password: this.password
          })
        });
        const data = await response.json();
        if (data.success) {
          // Successful login, redirect or perform any necessary action
          console.log('Login successful');
        } else {
          console.log('Login failed');
        }
      } catch (error) {
        console.error('Error:', error);
      }
    },
    goToRegister() {
      // Trigger the goToRegister event
      this.$emit('goToRegister');
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
