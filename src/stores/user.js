import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useUserStore = defineStore("user", () => {
  const token = ref(localStorage.getItem("token") || null);
  const email = ref(localStorage.getItem("email") || null);
  const role = ref(localStorage.getItem("role") || null);
  return { token, role, email }
});
