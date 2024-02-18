<template>
  <div class="log-in-container">
    <form action="" method="POST">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" v-model="Admin.username" required />
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="Admin.password" required />
      </div>

      <div class="button-container">
        <button class="login-button" @click.prevent="login()">Login</button>
        <button class="register-button" @click.prevent="goToRegister()">Register</button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "LogIn",
  data() {
    return {
      Admin: {
        username: null,
        password: null
      }
    };
  },
  methods: {
    login() {
      const formData = new FormData();
      formData.append("username", this.Admin.username);
      formData.append("password", this.Admin.password);
      axios.post("http://localhost/Game_mrm_prakse/game-mrm/src/api/api.php?action=login", formData)
          .then(response => {
            console.log(response.data);
            alert("login successful!");
            this.$router.push({path: '/game'});
          })
          .catch(error => {
            console.error(error);
            alert("login failed. Please try again.");
          });
    },

    goToRegister(){
      this.$router.push({path: '/register'});
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
