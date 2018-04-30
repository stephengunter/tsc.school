<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="readOnly">
                
                <button v-if="can_edit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 編輯
                </button>

            </div>
        </div>  
        <div class="panel-body">
            <show v-if="readOnly"  :account="account" @edit-account-photo="photoEditor.show=true"
                @view-account-photo="viewAccountPhoto" @delete-account-photo="deleteAccountPhoto">  
            </show>
            <edit v-else  :id="account.id" 
            @saved="onSaved"   @cancel="onEditCanceled" >                 
            </edit>
        </div>
        
    </div>
    <modal :showbtn="false"  title="上傳圖片"  :show.sync="photoEditor.show" 
        @closed="photoEditor.show=false" effect="fade">
      
        <div slot="modal-body" class="modal-body">
            <image-upload :type="photoEditor.type" :user_id="userId"
             @uploaded="onPhotoUploaded">
            </image-upload>
        </div>
    </modal>
</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'
    export default {
        name:'Account',
        components: {
            Show,
            Edit,
        },
        props: {
            account:{
                type: Object,
                default: null
            },
            can_edit:{
                type: Boolean,
                default: true
            },  
            version: {
                type: Number,
                default: 0
            },
        },
        data() {
            return {
                readOnly:true,
                photoEditor:{
                    show:false,
                    type:'account',
                }
            }
        },
        computed:{
           
            title(){
               
                if(this.readOnly) return '銀行帳戶';
                return ' 編輯銀行帳戶';
            },
            userId(){
                if(this.account) return parseInt(this.account.userId);
                return 0;
            }
           
        },
        beforeMount(){
            this.init();
        },
        watch: {
           
        },
        methods: {
            init() {
                
                this.readOnly=true;
                
            },
            beginEdit() {
                this.readOnly=false;
            },
            onEditCanceled(){
                this.readOnly=true;
            },
            onSaved(){
             
                this.init();
                this.$emit('saved');
            }, 
            onPhotoUploaded(){
                this.photoEditor.show=false;
                this.$emit('saved');
            },
            viewAccountPhoto(){
                
                let url=Photo.showUrl(this.account.photoId);
                window.open(url);
            },
            deleteAccountPhoto(){
                let action= Photo.delete(this.account.photoId);
                action.then(() => {
                    this.$emit('saved');
				})
				.catch(error => {
					
					Helper.BusEmitError(error,'刪除失敗');
				})

               
            }

            
        }
    }
</script>
