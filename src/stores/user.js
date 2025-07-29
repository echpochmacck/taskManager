import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useUserStore = defineStore("user", () => {
  const token = ref(localStorage.getItem("token") || null);
  const email = ref(localStorage.getItem("email") || null);
  const name = ref(localStorage.getItem("name") || null);
  return { token, name, email }
});
