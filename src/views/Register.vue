<template>
    <section id="register" class="py-5 bg-light">
        <div class="container">
            <h2 class="mb-4">Register</h2>
            <form>
                <div class="mb-3">
                    <label for="registerEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="registerEmail" v-model="email">
                    <div class="text-danger">{{errors?.email}}</div>
                </div>
                <div class="mb-3">
                    <label for="registerPassword" class="form-label">Password</label>
                    <div class="text-danger">{{errors?.password}}</div>

                    <input type="password" class="form-control" id="registerPassword" v-model="password">
                </div>
                <button type="submit" class="btn btn-primary" @click.prevent="register">Register</button>
            </form>
        </div>
    </section>
</template>

<script setup>
    import {ref} from 'vue';
    import {useRouter, useRoute} from 'vue-router';
    import {useUrlStore} from '@/stores/url';
    const urlStore = useUrlStore()
    const router = useRouter();
    const email = ref('test@test.com');
    const password = ref('123');
    const errors = ref({});
    async function register() {
        clear()
        try {
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

            const result = await fetch(`${urlStore.url}/api/register`, requestOptions);
            const data = await result.json();
            if (result.status > 199 && result.status < 300) {
                alert('user created');
                router.push({name: 'login'})
            } else {
                if (result.status == 422) {
                    downloadError(data.error.errors);
                }
            }
        } catch (e) {
            console.log(e)
        }
    }

    function downloadError(arr) {
        for (let attr in arr) {
            errors.value[attr] = arr[attr][0]
        }
        console.log(errors.value)
    }
    function clear() {
        errors.value = {};
    }
</script>