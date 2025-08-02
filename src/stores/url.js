import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useUrlStore = defineStore("url", () => {
  const url = ref("http://taskapi");
  return { url };
});
