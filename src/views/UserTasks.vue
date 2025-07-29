<template>
    <section class="container py-5">

        <div v-if="tasks && !isLoading">

            <div class="d-flex align-items-center gap-3">
                <h2 class="mb-4">Список моих задач</h2>
                <div class="d-flex align-items-center gap-3">
                    <div class="finished-box"></div> Завершена
                    <div class="active-box"></div> В работе
                    <div class="new-box"></div> Новая
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                <div class="col" v-for="(task, index) in tasks" :key="index">
                    <TaskCard :task="task" :users="task.users" />
                </div>
            </div>

        </div>

        <Loader v-if="isLoading" />
    </section>

</template>
<script setup>
    import TaskCard from '@/components/TaskCard.vue';
    import Loader from '@/components/Loader.vue'
    import {useUserStore} from '@/stores/user.js';
    import {useUrlStore} from '@/stores/url.js';
    import {ref, onMounted} from 'vue';
    import {useRouter} from 'vue-router';
    const user = useUserStore();
    const router = useRouter();
    const url = useUrlStore();
    const tasks = ref('');
    const isLoading = ref(false);
    async function fetchTasks() {
        try {
            isLoading.value = true;
            const myHeaders = new Headers();
            myHeaders.append("Authorization", `Bearer ` + user.token);
            const requestOptions = {
                method: "GET",
                redirect: "follow",
                headers: myHeaders
            };

            const result = await fetch(`${url.url}/api/tasks/user`, requestOptions);
            const data = await result.json();
            if (result.status > 199 && result.status < 300) {
                tasks.value = data.data.tasks
                isLoading.value = false
            }
        } catch {
            console.log(e)
        } finally {
            isLoading.value = false
        }
    }
    onMounted(() => {
        fetchTasks()
    })
</script>
<style scoped>
    .active-box {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: rgba(0, 0, 255, 0.1);
    }

    .finished-box {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: rgba(0, 128, 0, 0.1);
    }

    .new-box {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: rgba(239, 220, 16, 0.1);
    }
</style>