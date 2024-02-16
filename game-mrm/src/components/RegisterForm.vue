<template>
  <div class="register-form-container">
    <form id="contact-form">
      <div class="form-group">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="first_name" required />
      </div>
      <div class="form-group">
        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="last_name" required />
      </div>
      <div class="form-group">
        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required />
      </div>
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />
      </div>
      <div class="form-group">
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
      </div>
      <div class="form-group">
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required />
      </div>

      <div class="button-container">
        <button class="register-button">Register</button>
        <button class="back-button" @click.prevent="backToLogin">Back to Login</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  mounted() {
    const { createClient } = supabase;
    let supabase = createClient("https://nnbuvyjitnopekbbmzme.supabase.co", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5uYnV2eWppdG5vcGVrYmJtem1lIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MDgwNzEwMzIsImV4cCI6MjAyMzY0NzAzMn0.jjwC_4FURg2xwkj3-jmsDBINVutFSxNOnHUIvfHa8dE");

    const form = document.querySelector('#contact-form');
    form.addEventListener('submit', async (event) => {
      event.preventDefault();

      const formInputs = form.querySelectorAll('input, select');

      let submission = {};

      formInputs.forEach(element => {
        const { value, name } = element;
        if (value) {
          submission[name] = value;
        }
      });

      const { error } = await supabase.from('register_data').insert([submission], { returning: 'minimal' });

      if (error) {
        alert('There was an error, please try again');
      } else {
        alert('Thanks for contacting us');
      }

      formInputs.forEach(element => element.value = '');
    });
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
