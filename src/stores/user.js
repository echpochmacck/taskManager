import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useUserStore = defineStore("user", () => {
  const token = ref(localStorage.getItem("token") || null);
  const email = ref(localStorage.getItem("email") || null);
  const role = ref(localStorage.getItem("role") || null);
  function clear() {
    token.value = "";
    email.value = "";
    role.value = "";
  }
  return { token, role, email, clear };
});
