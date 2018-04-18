<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="readOnly">
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                <button v-if="canCreate" @click="beginCreate" class="btn btn-primary btn-sm" >
                    <i class="fa fa-plus"></i> 新增
                </button>
               
                <button v-if="canEdit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
                </button>

                <button v-if="canDelete" @click.prevent="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i> 
                    刪除
                </button>
            </div>
        </div>  
        <div class="panel-body">
            <show :quit="signup.quit" :user="signup.user"  v-if="readOnly"  :signup="signup"
               @edit-review="onEditReview"  @edit-ps="onEditPS" >  
            </show>
            <div v-else>
                <create v-if="creating"  :signup="signup"
                    @saved="onSaved"   @cancel="onEditCanceled" >                 
                </create>
                <edit  v-else :id="signup.id"  
                    @saved="onSaved"   @cancel="onEditCanceled" >                 
                </edit>
            </div>
            
        </div>
        
    </div>
    <review-editor :showing="reviewEditor.show" :reviewed="reviewEditor.reviewed"
      @close="reviewEditor.show=false" @save="updateReview">
    </review-editor>
    <ps-editor :showing="psEditor.show" :text="psEditor.text"
      @close="psEditor.show=false" @save="updatePS">
    </ps-editor>
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteSignup">        
    </delete-confirm>
</div>
</template>
<script>
    import Create from './create.vue';
    import Show from './show.vue';
    import Edit from './edit.vue';
    export default {
        name:'Quit',
        components: {
            Create,
            Show,
            Edit,
           
        },
        props: {
            signup:{
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
                icon:Menus.getIcon('quits') ,
                readOnly:true,
               
                

                reviewEditor:{
                    show:false,
                    id:0,
                    reviewed:false,
                },

                psEditor:{
                    show:false,
                    id:0,
                    text:'',
                },

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
            }
        },
        computed:{
            canCreate(){
               
                if(!this.can_edit) return false;
                if(!this.signup)   return false;
               
                return !Signup.hasQuit(this.signup);
            },
            canEdit(){
                if(!this.can_edit) return false;
                if(!this.signup.quit)  return false;
                if( Helper.isTrue(this.signup.quit.reviewed))  return false;
                return true;
            },
            canDelete(){
                return this.canEdit;
            },
            quitId(){
                if(!this.signup) return 0;
                if(!this.signup.quit) return 0;
                return this.signup.quit.signupId;
            },
            creating(){
                if(this.readOnly) return false;
                if(this.quitId)  return false;
                return true;
            },
            title(){
                let text='退費申請';
               
                if(this.readOnly) return text;
                if(this.creating) return '新增' + text;

                return '編輯退費申請';
                
            }
           
        },
        beforeMount(){
            this.init()
        },
        watch: {
            'version':'init'
        },
        methods: {
            init() {
                
            },
            beginCreate(){
                this.readOnly=false;
            },  
            beginEdit() {
                this.readOnly=false;
            },
            onEditCanceled(){
                this.readOnly=true;
            },
            onEditReview(){
                this.reviewEditor.id=this.quitId;
                this.reviewEditor.reviewed=Helper.isTrue(this.signup.quit.reviewed);
                this.reviewEditor.show=true;
            },
            updateReview(reviewed){
                
                let quits= [{
                    id:this.reviewEditor.id,
                    reviewed:reviewed
                }];

                let form=new Form({
                    quits:quits
                });

                let save=Quit.review(form);
				save.then(() => {
                    this.reviewEditor.show=false;
                    Helper.BusEmitOK('資料已存檔');
                    this.$emit('saved');
                    
				})
				.catch(error => {
                    this.reviewEditor.show=false;
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            onEditPS(){
                this.psEditor.id=this.quitId;
                this.psEditor.text=this.signup.quit.ps;
                this.psEditor.show=true;
            },
            updatePS(ps){
               
                let form=new Form({
                     id:this.psEditor.id,
                     ps:ps
                });

                let save=Quit.updatePS(form);
				save.then(() => {
                    this.psEditor.show=false;
                    Helper.BusEmitOK('資料已存檔');
                    this.$emit('saved');
                    
				})
				.catch(error => {
                    this.psEditor.show=false;
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            beginDelete(){
               
                let id=this.quitId;
                this.deleteConfirm.msg='確定要刪除這筆退費申請嗎?';
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;     
            },
            closeConfirm(){
                this.deleteConfirm.show=false;
            },
            deleteSignup(){
                this.closeConfirm();
                
                let id = this.deleteConfirm.id ;
                let remove= Quit.remove(id);
                remove.then(() => {
                    Helper.BusEmitOK('刪除成功');
                    this.$emit('deleted');
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                    this.closeConfirm();
                })
            },
            onSaved(){
                this.readOnly=true;
                this.$emit('saved');
            },  
            onBack(){
                this.$emit('back');
            }

            
        }
    }
</script>
