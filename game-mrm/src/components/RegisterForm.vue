<template>
  <div class="register-form-container">
    <form action="" method="POST" >
      <div class="form-group">
        <label for="firstName">First Name:</label>
        <input type="text" v-model="Admin.first_name" id="firstName" name="first_name" required />
      </div>
      <div class="form-group">
        <label for="lastName">Last Name:</label>
        <input type="text" v-model="Admin.last_name" id="lastName" name="last_name" required />
      </div>
      <div class="form-group">
        <label for="email">Email Address:</label>
        <input type="email" v-model="Admin.email" id="email" name="email" required/>
      </div>
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" v-model="Admin.username" id="username" name="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" v-model="Admin.password" id="password" name="password" required />
      </div>
      <div class="button-container">
        <button class="register-button" type="submit" @click.prevent="registerUser()">Register</button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "RegisterForm",
  data() {
    return {
      Admin: {
        first_name: null,
        last_name: null,
        email: null,
        username: null,
        password: null
      }
    };
  },
  methods: {
    registerUser() {
      const formData = new FormData();
      formData.append("first_name", this.Admin.first_name);
      formData.append("last_name", this.Admin.last_name);
      formData.append("email", this.Admin.email);
      formData.append("username", this.Admin.username);
      formData.append("password", this.Admin.password);

      axios.post("http://localhost/Game_mrm_prakse/game-mrm/src/api/api.php?action=register", formData)
          .then(response => {
            console.log(response.data);
            alert("Registration successful!");
            this.$router.push({path: '/'});
          })
          .catch(error => {
            console.error(error);
            alert("Registration failed. Please try again.");
          });
    }
  }
};
</script>

<style scoped>
.register-form-container {
  max-width: 400px;
  margin: auto;
  padding: 20px;
  border-radius: 12px; /* Increased border-radius for rounded corners */
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

input,
select {
  width: 100%;
  padding: 12px; /* Increased padding for better spacing */
  border: 1px solid #ddd; /* Lighter border color for a softer look */
  border-radius: 6px; /* Slightly rounded corners for input fields */
  box-sizing: border-box;
}

.button-container {
  text-align: center;
}

.register-button {
  background-color: #3498db;
  color: #fff;
  padding: 12px 20px; /* Increased padding for better spacing */
  border: none;
  border-radius: 6px; /* Slightly rounded corners for buttons */
  cursor: pointer;
  transition: background-color 0.3s ease-in-out;
}

.register-button:hover {
  background-color: #297fb8; /* Darken the color on hover */
}
</style>

