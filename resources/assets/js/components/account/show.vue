<template>
<div>  
    <div v-if="account" class="show-data">
        
        <div class="row">
            <div class="col-sm-3">
                <div class="text-center">
                    <photo></photo>
                    <h5>存摺影本</h5>
                   
                    <button v-if="hasPhoto" @click.prevent="viewPhoto" title="查看" class="btn btn-primary btn-xs"> 
                        <i class="fa fa-search"></i>                                    
                        
                    </button> 
                    <button @click.prevent="editPhoto" title="上傳" class="btn btn-info btn-xs">   
                        <i class="fa fa-edit"></i>                          
                        
                    </button>
                    <button v-if="hasPhoto" @click.prevent="deletePhoto" title="刪除" class="btn btn-danger btn-xs">   
                        <i class="fa fa-trash"></i>                          
                        
                    </button> 
                </div>
            </div> 
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="label-title">銀行名稱</label>
                        <p> {{ account.bank }} </p>
                    </div>  
                    <div class="col-sm-4">
                        <label class="label-title">分行</label>
                        <p> {{ account.branch }} </p>                         
                    </div> 
                    <div class="col-sm-4">
                        <label class="label-title">戶名</label>
                        <p> {{ account.owner }} </p>      
                    </div>    
                </div> <!-- End Row -->
                <div class="row">
                    <div class="col-sm-12">
                        <label class="label-title">銀行帳號</label>
                        <p> {{ account.number }} </p>
                    </div>  
                    
                </div>  <!-- End Row -->
                
                
            </div>     
        </div>
        
    </div>
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="deleteConfirm.show=false" @confirmed="onDeletePhotoConfirmed">        
    </delete-confirm>
</div>     
</template>

<script>
    export default {
        name: 'ShowAccount', 
        props: {
            account: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
            }
        },
        computed:{
            hasPhoto(){
                return Helper.tryParseInt(this.account.photoId) > 0;
            }
        }, 
        methods: { 
            editPhoto(){
                this.$emit('edit-account-photo');
            },
            viewPhoto(){
                this.$emit('view-account-photo');
            },
            deletePhoto(){
                this.deleteConfirm.show=true;
                this.deleteConfirm.msg='確定要刪除存摺封面影本嗎?';
               
            },
            onDeletePhotoConfirmed(){
                this.deleteConfirm.show=false;
                this.$emit('delete-account-photo');
            }
            
            
        }
    }
</script>
