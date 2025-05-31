import { defineStore } from 'pinia'

export const usePopupStore = defineStore('popup', {
  state: () => ({
    show: false,
    message: ''
  }),
  actions: {
    open(message) {
      this.message = message
      this.show = true
    },
    close() {
      this.show = false
      this.message = ''
    }
  }
})
