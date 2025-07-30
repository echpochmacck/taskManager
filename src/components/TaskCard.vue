<template>
    <div class="p-3 border rounded-3 task-box" :style="{
    backgroundColor:
      task.status_title === 'active'
        ? 'rgba(0, 0, 255, 0.1)'
        : task.status_title === 'finished'
        ? 'rgba(0, 128, 0, 0.1)'
        : 'rgba(255, 0, 0, 0.1)'
  }">

        <div class="mb-3">
            <h5 class="mb-1" style="font-weight: 600;">{{ task.title }}</h5>
            <div class="text-muted" style="font-size: 0.9rem;">
                Дедлайн: {{ task.deadline }}
            </div>
        </div>

        <div class="mb-3" style="font-size: 0.95rem;">
            <p class="mb-2">{{ task.description }}</p>
            <div><strong>Статус:</strong> {{ task.status_title }}</div>
        </div>

        <div class="text-muted" style="font-size: 0.8rem;">
            Создано: {{ task.created_at }}
        </div>

        <div class="mt-2">
            <div class="text-muted" style="font-size: 0.8rem;">Пользователи:</div>
            <div class="d-flex flex-wrap gap-2 mt-1">
                <span class="d-flex align-items-center user-span" v-for="user in users" :key="user.email">
                    {{ user}}
                </span>
                <button class="btn btn-outline-primary"
                    v-if="task.status_title != 'finished' && (!users || !users.includes(email))"
                    @click.prevent="emit('fetchSub', task.id)">join task</button>
            </div>
        </div>
    </div>



</template>
<script setup>
    import {defineProps, ref, defineEmits} from 'vue';
    const props = defineProps([
        'users',
        'task',
        'email',
        'url'
    ]);
    const emit = defineEmits(['fetchSub'])


</script>
<style scoped>
    .task-box {
        transition: .5s;

        &:hover {
            transform: scale(1.05);
        }
    }

    .user-span {
        font-size: 0.85rem;
        padding: 2px 8px;
        border-radius: 999px;
        background-color: #f2f2f2;
    }
</style>