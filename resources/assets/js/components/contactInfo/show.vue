<template>
   <div v-if="model" class="panel-body show-data">
        <div class="row">
            <div class="col-sm-4">
                <label class="label-title">電話</label>
                <p v-text="model.tel"></p>                      
            </div>
            <div class="col-sm-4">
                <label class="label-title">傳真</label>
                <p v-text="model.fax"></p>
            </div>
            
        </div>   
        <div class="row">
            <div class="col-sm-6">
                <label class="label-title">通訊地址</label>
                <p v-text="model.address.fullText"></p>                     
            </div>
        </div> 
        <div class="row">
            <div class="col-sm-6">
                <label class="label-title">第二地址</label>
                <p v-text="model.address.ube"></p>                     
            </div>
        </div>   
    </div> 
</template>

<script>
    
    export default {
        name: 'ShowContactInfo',  
        props: {
            model:{
                type: Object,
                default: null
            },
        },
        data() {
            return {
                title:Menus.getIcon('ContactInfo') + '  聯絡資訊', 
            }
        },
        watch:{
           
        },    
        beforeMount(){
            
        },  
        methods: { 
            showUpdatedBy(){
                Bus.$emit('onShowEditor',this.model.updated_by)
            },
            
        }
        

    }
</script>