<template>
    <div v-if="submitting" class="control" style="padding-top:1em">
        <button type="submit" class="button is-outlined">
            <i class="fa fa-spinner fa-spin"></i> &nbsp;處理中
        </button>
    </div>
    <div v-else class="control" style="padding-top:1em">
        <button type="submit" class="button is-primary" >確認送出</button>
        &nbsp;
        <button @click.prevent="cancel" class="button is-outlined">取消</button>
        <p class="help is-danger" v-if="form.errors.has('courseIds')" v-text="form.errors.get('courseIds')"></p>        
       
    </div>
   
    
</template>



<script>
    export default {
        name: 'SignupSubmitButtons',
        props: {
            form: {
                type: Object,
                default: null
            },
            submitting: {
                type: Boolean,
                default: false
            }
        },
        computed:{
            

        },
        methods: {
            cancel() {
                this.$emit('cancel')      
            },
        }
    }
</script>