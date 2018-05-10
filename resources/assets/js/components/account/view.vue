<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="readOnly">
                <button v-if="canCreate" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-plus"></i> 新增
                </button>
                <button v-if="canEdit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 編輯
                </button>
                <button v-if="canDelete"  @click="beginDelete" class="btn btn-danger btn-sm" >
                   <i class="fa fa-trash"></i>  刪除
                </button>
            </div>
        </div>  
        <div class="panel-body">
            <show v-if="readOnly"  :account="account" @edit-account-photo="photoEditor.show=true"
                @view-account-photo="viewAccountPhoto" @delete-account-photo="deleteAccountPhoto">  
            </show>
            <edit v-else  :id="accountId" :user_id="user_id" 
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

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
        @close="deleteConfirm.show=false" @confirmed="deleteAccount">        
    </delete-confirm>
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
            user_id:{
                type: Number,
                default: 0
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
                },
                deleteConfirm:{
                    id:this.accountId,
                    show:false,
                    msg:'',

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
            },

            canCreate(){
                if(!this.can_edit) return false;
                if(this.account) return false;
                return true;
            },
            canEdit(){
                if(!this.can_edit) return false;
                if(!this.account) return false;
                return true;
            },
            accountId(){
                if(this.account) return this.account.id;
                return 0;

            },
            canDelete(){
                return this.canEdit;
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
                this.deleteConfirm.show=false;
                
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

               
            },
            beginDelete(){
                let id=this.accountId;
                this.deleteConfirm.msg='確定要刪除此銀行帳號資料嗎?';
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;                
            },
            deleteAccount(){
                let id = this.deleteConfirm.id; 
                let remove= Account.remove(id);
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功');
                    this.init();
                    this.$emit('saved');
                    
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                    this.init();
                })
            },

            
        }
    }
</script>
