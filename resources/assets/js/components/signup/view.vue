<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="signup" >
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                <button v-if="canPay" @click.prevent="beginPay" class="btn btn-success btn-sm" >
                    <i class="fa fa-check"></i> 
                    繳費
                </button>
                <button v-if="canDelete" @click.prevent="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i> 
                    刪除
                </button>
               
            </div>
            <div v-else>
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
            </div>
        </div>  
        <div class="panel-body">

            <show v-if="readOnly"  :signup="signup" @edit-ps="onEditPS" @unPay="beginUnpay">  
            </show> 
            <edit v-else ref="editComponent"  :id="id" :params="params" :user="userSelector.user"
                    @saved="onSaved"   @cancel="onEditCanceled" @exist-user="onExistUser" @user-saved="loadUser">                 
            </edit>
            
        </div>
        
    </div>
   
    <modal :showbtn="false"  :show.sync="userSelector.show"  @closed="userSelector.show=false" 
        effect="fade" :width="1200">
		<div slot="modal-header" class="modal-header modal-header-danger">
            
			<button id="close-button" type="button" class="close"  @click="userSelector.show=false">
					x
			</button>
			<h3 style="color:white">
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
				相同資料的使用者已經存在
			</h3>
		</div>
	
		<div slot="modal-body" class="modal-body">
			<user-selector v-if="userSelector.show" :model="userSelector.model"
             @selected="onExistUserSelected">
            </user-selector>
		</div>
    </modal>
    
    <pay-editor ref="payEditor" :showing="payEditorSettings.showing" :signup="signup" :payways="payways"
      @close="payEditorSettings.showing=false" @saved="onPaySaved">        
    </pay-editor>

    <ps-editor ref="psEditor"  :showing="psEditor.show" :text="psEditor.text"
      @close="psEditor.show=false" @save="updatePS">
    </ps-editor>

    <delete-confirm  :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteSignup">        
    </delete-confirm>

    <delete-confirm  :showing="unpayConfirm.show" :message="unpayConfirm.msg"
      @close="closeUnpayConfirm" @confirmed="unpaySignup">        
    </delete-confirm>
</div>
</template>
<script>
   

    import Show from './show.vue';
    import Edit from './edit.vue';
    import PayEditor from './pay-editor.vue';
   
    export default {
        name:'Signup',
        components: {
            Show,
            Edit,
            'pay-editor':PayEditor
           
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            params:{
               type:Object,
               default: null
            },
            payways: {
              type: Array,
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
                icon:Menus.getIcon('signups') ,
                readOnly:true,

                signup:null,


                userSelector:{
                    model:null,
                    show:false,
                    user:null,
                    
                },

                payEditorSettings:{
                  
                    showing:false,
                    
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

                },

                unpayConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
            }
        },
        computed:{
            creating(){
                if(this.readOnly) return false;
                if(this.id)  return false;
                return true;
            },
            canEdit(){
                if(!this.can_edit) return false;
                if(!this.readOnly) return false;
                if(!this.signup) return false;
                
                return this.signup.canEdit;
            },
            payed(){
                if(!this.signup) return false;
                return Signup.hasPayed(this.signup);
            },
            canPay(){
                if(!this.can_edit) return false;
                if(this.payed) return false;
                return true;
            },
            canDelete(){
                if(!this.canEdit) return false;
                return this.signup.canDelete;
            },
           
            title(){
               
                if(this.readOnly) return this.icon + ' 報名資料';
                if(this.creating) return this.icon + ' 新增報名';
               
                return this.icon + ' 編輯報名資料';
            },
            
           
        },
        beforeMount(){
            this.init()
        },
        watch: {
            'id': 'init',
            'version':'init'
        },
        methods: {
            init() {
                if(this.id){
                    this.fetchData();
                    this.readOnly=true;
                }else{
                    this.readOnly=false;                    
                }
                
                this.payEditorSettings={
                    showing:false
                };

                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }; 

                this.unpayConfirm={
                    id:0,
                    show:false,
                    msg:''
                }; 
            },
            fetchData() {
              
                let getData=Signup.show(this.id);
               
                getData.then(signup => {
                   
                    this.signup = {
                        ...signup
                    }; 

                    this.$emit('loaded',this.signup);
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            }, 
            isPayed(signup){
                return Helper.isTrue(signup.bill.payed);
            },
            onBack(){
                this.$emit('back');
            },
            onEditCanceled(){
                if(this.creating){
                    this.onBack();
                }else{
                    this.init();
                }
                
            },
            onExistUser(model){
                this.userSelector.model={
                    ...model
                };
                this.userSelector.show=true;
            },
            onExistUserSelected(id){
                this.loadUser(id);
               
                this.userSelector.show=false;
                this.userSelector.model=null;
            },
            loadUser(id){
                
                if(!id) id= this.form.user.id;
                let getData=User.edit(id);
                getData.then(model => {
                   
                    this.userSelector.user = {
                        ...model.user
                    }; 

                    this.$refs.editComponent.setUser(model.user);
                    
                })
                .catch(error=> {
                    Helper.BusEmitError('無法取得使用者資料,請稍後再試.');
                })
            },
            onEditPS(){
               
                this.psEditor.id=this.signup.id;
                this.psEditor.text=this.signup.ps;
               
                this.$refs.psEditor.init(this.signup.ps);

                this.psEditor.show=true;
            },
            updatePS(ps){
               
                let form=new Form({
                     id:this.psEditor.id,
                     ps:ps
                });

                let save=Signup.updatePS(form);
				save.then(() => {
                    this.psEditor.show=false;
                    Helper.BusEmitOK('資料已存檔');
                    this.init();
				})
				.catch(error => {
                    this.psEditor.show=false;
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            onSaved(signup){
                if(this.creating)this.$emit('saved',signup);
                else  this.init();
            },
            onPaySaved(){
                this.init();
            },  
            beginPay(){
                this.$refs.payEditor.init(this.signup.bill);
                this.payEditorSettings.showing=true;
            },
            beginDelete(){
                
                let name=this.signup.user.profile.fullname;
                let id=this.signup.id;
                this.deleteConfirm.msg=`確定要刪除 ${name} 的報名資料嗎?`;
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;       
            },
            closeConfirm(){
                this.deleteConfirm.show=false;
            },
            beginUnpay(){
                
                let id=this.signup.id;
                this.unpayConfirm.msg='確定要將狀態改為未繳費嗎?';
                this.unpayConfirm.id=id;
                this.unpayConfirm.show=true;       
            },
            closeUnpayConfirm(){
                this.unpayConfirm.show=false;
            },
            deleteSignup(){
                this.closeConfirm();
                
                let id = this.deleteConfirm.id ;
                let remove= Signup.remove(id);
                remove.then(() => {
                    Helper.BusEmitOK('刪除成功');
                    this.$emit('deleted');
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                    this.closeConfirm();
                })
            },
            unpaySignup(){

                let id = this.unpayConfirm.id ;
                let save= Bill.unpay(id);
                save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.init();
                    this.unpayConfirm.show=false;       
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                    this.closeConfirm();
                })
            },

            
        }
    }
</script>
