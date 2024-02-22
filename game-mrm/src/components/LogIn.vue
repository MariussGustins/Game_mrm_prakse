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

            sessionStorage.setItem('username', this.Admin.username);

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
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  max-width: 400px;
  width: 100%;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
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
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  box-sizing: border-box;
}

.button-container {
  display: flex;
  justify-content: space-between;
}

.login-button,
.register-button {
  width: 48%;
  text-align: center;
  padding: 12px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease-in-out;
}

.login-button {
  background-color: #3498db;
  color: #fff;
}

.register-button {
  background-color: #ccc;
  color: #000;
}

.login-button:hover {
  background-color: #297fb8;
}

.register-button:hover {
  background-color: #bbb;
}
</style>