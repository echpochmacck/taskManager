<template>
    <section class="container py-5">
        <h2 class="mb-4">Создание новой задачи</h2>
        <form class="card p-4 shadow-sm bg-white">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="title" class="form-label">Заголовок</label>
                    <input type="text" class="form-control" id="title" placeholder="Введите заголовок" v-model="title">
                </div>
                <div class="col-md-6">
                    <label for="deadline" class="form-label">Дедлайн</label>
                    <input type="date" class="form-control" id="deadline" v-model="deadline">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea class="form-control" id="description" rows="3" placeholder="Описание задачи"
                    v-model="description"></textarea>
            </div>

            <!-- <div class="mb-3">
                <label for="category" class="form-label">Категория</label>
                <select class="form-select" id="category">
                    <option selected disabled>Выберите категорию</option>
                    <option value="1">Общая</option>
                    <option value="2">Срочная</option>
                    <option value="3">Личное</option>
                </select>
            </div> -->

            <!-- Назначение пользователей -->
            <div class="mb-4" v-if="users">
                <label class="form-label">Назначение пользователей</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <select class="form-select" id="user-select" v-model="selected">
                                <option selected disabled>Выберите пользователя</option>
                                <option v-for="user in users" :key="user.id" :value="user">{{user.email}}
                                </option>
                            </select>
                            <div class="text-danger">{{errors?.selected}}</div>
                            <button type="button" class="btn btn-outline-secondary"
                                @click.prevent="addUser">Добавить</button>
                        </div>
                    </div>

                    <!-- Список добавленных пользователей -->
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                v-for="user, index in taskUsers">
                                {{user.email}}
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    @click.prevent="removeUser(index)">Удалить</button>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <!-- Кнопка -->
            <button type="submit" class="btn btn-primary mt-3" @click.prevent="saveTask">Создать задачу</button>
        </form>
    </section>



</template>
<script setup>
    import {useUserStore} from '@/stores/user.js';
    import {useUrlStore} from '@/stores/url.js';
    import {ref, onMounted} from 'vue';
    import {useRouter} from 'vue-router';
    import {clear} from '@/composable/error.js'
    const title = ref('test vue');
    const description = ref('test vue');
    // дописать форматирование дат + категории мб сделать надо все таки
    const deadline = ref('');
    const users = ref('');
    const taskUsers = ref([]);
    const selected = ref('');
    const errors = ref({});
    const url = useUrlStore();
    const user = useUserStore();
    onMounted(() => {
        getUsers()
    })
    async function getUsers() {
        try {
            const myHeaders = new Headers();
            const requestOptions = {
                method: "GET",
                headers: myHeaders,
                redirect: "follow"
            };
            const result = await fetch(`${url.url}/api/users`, requestOptions)
            const data = await result.json()
            if (result.status > 199 && result.status < 300) {
                users.value = data.data.users
            }

        } catch (e) {
            console.log(e)
        }
    }
    function addUser() {
        if (selected.value) {
            clear(errors)
            taskUsers.value.push(selected.value);
            selected.value = false;
        } else {
            errors.value.selected = 'Выберите пользователя'
        }
    }
    function removeUser(index) {
        taskUsers.value.splice(index, 1);
        console.log(users.value)
    }
    async function saveTask() {
        try {
            const myHeaders = new Headers();
            myHeaders.append("Content-Type", "application/json");
            myHeaders.append("Authorization", "Bearer " + user.token);

            const raw = JSON.stringify({
                "category_id": 2,
                "deadline": formatDateToSQL(deadline.value),
                "description": description.value,
                "title": title.value,
                "users": taskUsers.value.length ? taskUsers.value.map((x) => x.id) : []
            });

            const requestOptions = {
                method: "POST",
                headers: myHeaders,
                body: raw,
                redirect: "follow"
            };

            const result = await fetch(`${url.url}/api/tasks/new`, requestOptions)
            const data = await result.json();
            if (result.status > 199 && result.status < 300) {
                alert("кайф")
            }
        } catch (e) {
            console.log(e)
        }
        function formatDateToSQL(str) {
            const date = new Date(str);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }


    }

</script>