import { createApp } from 'vue';
import App from './App.vue';
import { createRouter, createWebHashHistory } from 'vue-router';
import LogIn from './components/LogIn.vue';
import GameBoard from './components/GameBoard.vue';
import RegisterForm from './components/RegisterForm.vue';


const routes = [
    { path: '/', component: LogIn },
    { path: '/game', component: GameBoard },
    { path: '/register', component: RegisterForm }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes
});

const app = createApp(App);
app.use(router); // Use the router
app.mount('#app');
