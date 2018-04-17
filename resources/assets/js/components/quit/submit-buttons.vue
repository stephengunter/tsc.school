<template>
    <div>
        <div v-if="submitting"  class="row">
            <div class="col-sm-4">
                <div class="form-group">                           
                    <button class="btn btn-default">
                        <i class="fa fa-spinner fa-spin"></i> 
                        處理中
                    </button> 
                </div>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-sm-12">
                <div class="form-group">   
                    <button type="submit" class="btn btn-success" >
                        <i class="fa fa-save"></i>
                        確認存檔
                    </button> 
                   
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-default" @click.prevent="cancel">
                        取消
                    </button>  
                    <small class="text-danger" v-text="error_text"></small>
                </div>
            </div>
        </div>
    
    </div>
    
</template>



<script>
    export default {
        name: 'QuitSubmitButtons',
        props: {
            form: {
                type: Object,
                default: null
            },
            submitting: {
                type: Boolean,
                default: false
            },
            error_text: {
                type: String,
                default: ''
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