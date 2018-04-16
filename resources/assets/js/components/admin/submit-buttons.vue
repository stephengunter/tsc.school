<template>
   <div v-if="is_create">
        <div  v-if="submitting" class="form-group">
            <div class="col-md-2">
            </div>  
            <div class="col-md-10">
                <button type="submit" class="btn btn-success" :disabled="disabled">
                    <i class="fa fa-save"></i>
                    確認存檔
                </button> 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-default" @click.prevent="cancel">
                    取消
                </button> 
            </div>   
        </div> 
        <div v-else class="form-group">
            <div class="col-md-2">
            </div>  
            <div class="col-md-10">
                <button type="submit" class="btn btn-success" :disabled="disabled">
                    <i class="fa fa-save"></i>
                    確認存檔
                </button> 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class="btn btn-default" @click.prevent="cancel">
                    取消
                </button> 
            </div>   
        </div> 
    
    </div>
    <div v-else>
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
            <div class="col-sm-4">
                <div class="form-group">   
                    <button type="submit" class="btn btn-success" :disabled="disabled">
                        <i class="fa fa-save"></i>
                        確認存檔
                    </button> 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-default" @click.prevent="cancel">
                        取消
                    </button>  
                </div>
            </div>
        </div>
    </div>
</template>



<script>
    export default {
        name: 'AdminSubmitButtons',
        props: {
            is_create: {
                type: Boolean,
                default: false
            },
            submitting: {
                type: Boolean,
                default: false
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            cancel() {
                this.$emit('cancel')      
            },
        }
    }
</script>