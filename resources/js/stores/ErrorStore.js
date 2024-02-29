import { defineStore } from 'pinia';

export const useErrorStore = defineStore('errors', {
    state: () => ({
        errors: {},
    }),
    actions: {
        addErrors(errors) {
            this.errors = errors;
        },
        clearErrors() {
            this.errors = {};
        },
        fieldHasError(field) {

            return Object.hasOwn(this.errors, field) && this.errors[field].length > 0
        },
        getFieldError(field) {
            return this.errors[field].join(', ') || null;
        },
    },
});