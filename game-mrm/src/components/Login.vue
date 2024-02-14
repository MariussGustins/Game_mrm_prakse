methods: {
async login() {
try {
await firebase.auth().signInWithEmailAndPassword(this.email, this.password);
// User logged in successfully
} catch (error) {
// Handle login errors
}
}
}