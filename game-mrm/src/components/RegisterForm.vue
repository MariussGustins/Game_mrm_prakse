methods: {
async register() {
try {
await firebase.auth().createUserWithEmailAndPassword(this.email, this.password);
// User registered successfully
} catch (error) {
// Handle registration errors
}
}
}