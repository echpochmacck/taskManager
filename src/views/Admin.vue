<template>
   <section class="container py-5">
    <h2 class="mb-4">Создание новой задачи</h2>
    <form class="card p-4 shadow-sm bg-white">
        <!-- Заголовок и дедлайн -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="title" class="form-label">Заголовок</label>
                <input type="text" class="form-control" id="title" placeholder="Введите заголовок">
            </div>
            <div class="col-md-6">
                <label for="deadline" class="form-label">Дедлайн</label>
                <input type="date" class="form-control" id="deadline">
            </div>
        </div>

        <!-- Описание -->
        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea class="form-control" id="description" rows="3" placeholder="Описание задачи"></textarea>
        </div>

        <!-- Категория -->
        <div class="mb-3">
            <label for="category" class="form-label">Категория</label>
            <select class="form-select" id="category">
                <option selected disabled>Выберите категорию</option>
                <option value="1">Общая</option>
                <option value="2">Срочная</option>
                <option value="3">Личное</option>
            </select>
        </div>

        <!-- Назначение пользователей -->
        <div class="mb-4">
            <label class="form-label">Назначение пользователей</label>
            <div class="row g-3">
                <!-- Нормальный селект -->
                <div class="col-md-6">
                    <div class="input-group">
                        <select class="form-select" id="user-select">
                            <option selected disabled>Выберите пользователя</option>
                            <option value="1">Иван Иванов</option>
                            <option value="2">Мария Петрова</option>
                            <option value="3">Алексей Смирнов</option>
                            <option value="4">Светлана Кузнецова</option>
                            <option value="5">Дмитрий Орлов</option>
                            <option value="6">Анна Васильева</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary" disabled>Добавить</button>
                    </div>
                    <div class="form-text">Выбор и добавление пользователя требует JS.</div>
                </div>

                <!-- Список добавленных пользователей -->
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Иван Иванов
                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>Удалить</button>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Мария Петрова
                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>Удалить</button>
                        </li>
                    </ul>
                    <div class="form-text mt-2">Удаление тоже работает через JS.</div>
                </div>
            </div>
        </div>

        <!-- Кнопка -->
        <button type="submit" class="btn btn-primary mt-3">Создать задачу</button>
    </form>
</section>



</template>
<script setup>

</script>