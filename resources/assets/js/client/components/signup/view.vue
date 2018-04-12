<template>
<div>
    <div class="panel">
        <div class="panel-heading panel-title heading" >
            {{ title }}
            <div v-if="signup" style="float: right">
                <button v-show="can_back"  @click.prevent="onBack" class="button is-outlined" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
               
                <button v-if="canDelete" @click.prevent="beginDelete" class="button is-danger" >
                    <i class="fa fa-times-circle"></i> 
                    取消
                </button>
               
            </div>
            <div v-else style="float: right">
                <button v-show="can_back"  @click.prevent="onBack" class="button is-outlined" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
            </div>
        </div>  
        <div class="panel-block">

            <show v-if="readOnly"  :signup="signup" >  
            </show> 
            <edit v-else ref="editComponent"  :id="id" :course_id="course_id" :user="userSelector.user"
                @saved="onSaved"   @cancel="onEditCanceled" @exist-user="onExistUser" @user-saved="loadUser">                 
            </edit>
        </div>
        
    </div>
    
    <!-- <bill-print v-if="signup" v-show="!isPayed(signup)" :signup="signup" ref="billPrint"></bill-print>

     -->
    

    <!-- <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteSignup">        
    </delete-confirm> -->
</div>
</template>
<script>
    // import html2Canvas from 'html2Canvas';

    import Show from './show.vue';
    import Edit from './edit.vue';
    // import BillPrint from './bill-print';

    export default {
        name:'Signup',
        components: {
            Show,
            Edit,
            // 'bill-print':BillPrint
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            course_id: {
              type: Number,
              default: 0
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
             
                readOnly:true,

                signup:null,


                userSelector:{
                    model:null,
                    show:false,
                    user:null,
                    
                },

               

                deleteConfirm:{
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
            canDelete(){
                return this.canEdit;
            },
            title(){
               
                if(this.readOnly) return '報名資料';
                if(this.creating) return '線上報名';
                return '編輯報名資料';
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
                

                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }; 
            },
            fetchData() {
                alert('fetchData');
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
            beginPrint() {
                this.$refs.billPrint.print();
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
                alert(id);
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
            onSaved(signup){
                if(this.creating)this.$emit('saved',signup);
                else  this.init();
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

            
        }
    }
</script>
