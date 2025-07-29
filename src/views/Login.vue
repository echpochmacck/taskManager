<template>
    <section id="login" class="py-5">
        <div class="container">
            <h2 class="mb-4">Login</h2>
            <form>
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="loginEmail" v-model="email">
                </div>
                <div class="text-danger">{{errors?.email}}</div>

                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" v-model="password">
                </div>
                <div class="text-danger">{{errors?.password}}</div>
                <button type="submit" class="btn btn-success" @click.prevent="login">Login</button>
            </form>
        </div>
    </section>
</template>
<script setup>
    import {useUserStore} from '@/stores/user.js';
    import {useUrlStore} from '@/stores/url.js';
    import {ref} from 'vue';
    import {useRouter} from 'vue-router';
    import {downloadError, clear} from '@/composable/error.js'
    const user = useUserStore();
    const router = useRouter();
    const url = useUrlStore();
    const email = ref('admin@admin.com');
    const password = ref('123Da');
    const errors = ref({});
    async function login() {
        clear(errors)
        const myHeaders = new Headers();
        myHeaders.append("Content-Type", "application/json");

        const raw = JSON.stringify({
            "email": email.value,
            "password": password.value
        });

        const requestOptions = {
            method: "POST",
            headers: myHeaders,
            body: raw,
            redirect: "follow"
        };

        const result = await fetch(`${url.url}/api/login`, requestOptions)
        const data = await result.json();
        if (result.status > 199 && result.status < 300) {
            localStorage.setItem('token', data.data.token);
            user.token = data.data.token;
            router.push({name: 'admin-tasks'})
        } else {
            if (result.status == 422) {
                downloadError(data.error.errors, errors)
            }
            if (result.status == 401) {
                errors.value.password = 'login failed'
            }
        }

    }

</script>