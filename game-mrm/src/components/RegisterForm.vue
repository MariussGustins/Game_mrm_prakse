<template>
  <div class="register-form-container">
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" v-model="firstName" required />
      </div>
      <div class="form-group">
        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" v-model="lastName" required />
      </div>
      <div class="form-group">
        <label for="email">Email Address:</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" v-model="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <div class="form-group">
        <label for="gender">Gender:</label>
        <select id="gender" v-model="gender" required>
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
      </div>
      <div class="form-group">
        <label for="age">Age:</label>
        <input type="number" id="age" v-model="age" required />
      </div>

      <div class="button-container">
        <button class="register-button" @click.prevent="handleSubmit">Register</button>
        <button class="back-button" @click.prevent="backToLogin">Back to Login</button>
      </div>
    </form>
  </div>
</template>

<script>
import { createClient } from '@supabase/supabase-js';

export default {
  data() {
    return {
      firstName: '',
      lastName: '',
      email: '',
      username: '',
      password: '',
      gender: 'male',
      age: null
    };
  },
  created() {
    this.supabase = createClient('https://nnbuvyjitnopekbbmzme.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5uYnV2eWppdG5vcGVrYmJtem1lIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MDgwNzEwMzIsImV4cCI6MjAyMzY0NzAzMn0.jjwC_4FURg2xwkj3-jmsDBINVutFSxNOnHUIvfHa8dE');
  },
  methods: {
    async handleSubmit() {
      try {
        const { data, error } = await fetch('https://nnbuvyjitnopekbbmzme.supabase.co', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'apikey': 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5uYnV2eWppdG5vcGVrYmJtem1lIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MDgwNzEwMzIsImV4cCI6MjAyMzY0NzAzMn0.jjwC_4FURg2xwkj3-jmsDBINVutFSxNOnHUIvfHa8dE'
          },
          body: JSON.stringify({
            first_name: this.firstName,
            last_name: this.lastName,
            email: this.email,
            username: this.username,
            password: this.password,
            gender: this.gender,
            age: this.age
          })
        });


        if (error) {
          throw error;
        }

        console.log('User registered successfully!');
        // Reset form fields
        this.firstName = '';
        this.lastName = '';
        this.email = '';
        this.username = '';
        this.password = '';
        this.gender = 'male';
        this.age = null;
      } catch (error) {
        console.error('Error registering user:', error.message);
      }
    },
    // other methods remain the same
  }
};
</script>

<style scoped>
.register-form-container {
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

input,
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.button-container {
  text-align: center;
}

.register-button {
  background-color: #3498db;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
