<template>
    <div class="modal" tabindex="-1" id="confirmModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--<slot></slot>-->
                    {{ content }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="setResult('no')">No</button>
                    <button type="button" class="btn btn-primary" @click="setResult('yes')">Yes</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import { Modal } from 'bootstrap';
    export default {
        name: 'Confirm',
        props: {
            title: {
                type: String,
                default: 'Confirm'
            },
            content: {
                type: String,
                default: 'Are you sure?'
            },
        },
        data() {
            return {
                modal: null,
            }
        },
        methods: {
            show(message) {
                if (message) {
                    this.content = message
                }
                this.modal = new Modal(document.getElementById('confirmModal'), {
                    keyboard: false
                })
                this.modal.show()
            },
            setResult(result) {
                this.modal.hide()
                if (result == 'yes') {
                    this.$emit('confirmed')
                }
                if (result == 'no') {
                    this.$emit('canceled')
                }
            },
        }
    }
</script>