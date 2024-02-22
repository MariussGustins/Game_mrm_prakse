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
  max-width: 400px;
  margin: auto;
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

.login-button {
  width: 48%;
  text-align: center;
  background-color: #3498db; /* Blue color for login button */
  color: #fff;
  padding: 12px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease-in-out;
}

.register-button {
  width: 48%;
  text-align: center;
  background-color: #ccc; /* Gray color for register button */
  color: #000;
  padding: 12px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease-in-out;
}

.login-button:hover {
  background-color: #297fb8;
}

.register-button:hover {
  background-color: #bbb; /* Darken the gray color on hover */
}
</style>





