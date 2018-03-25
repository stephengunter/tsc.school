<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="admin" v-show="readOnly">
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                <button v-if="admin.canEdit" v-show="can_edit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
                </button>
                <button v-if="admin.canDelete" v-show="!hide_delete" @click.prevent="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i> 
                    刪除
                </button>
               
            </div>
        </div>  
        <div class="panel-body">
            <show v-if="readOnly"  :admin="admin" >  
            </show>
            <edit v-else ref="editComponent"  :id="id" :user="userSelector.user"
                @saved="onSaved"   @cancel="onEditCanceled" @exist-user="onExistUser" >                 
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
    

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteAdmin">        
    </delete-confirm>
</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'
    export default {
        name:'Admin',
        components: {
            Show,
            Edit,
        },
        props: {
            id: {
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
                icon:Menus.getIcon('admins') ,
                readOnly:true,

                admin:null,


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
            title(){
               
                if(this.readOnly) return this.icon + ' 管理員資料';
                if(this.creating) return this.icon + ' 新增管理員';
                return `${this.icon}  編輯管理員資料：${this.admin.user.profile.fullname}`
                
            }
           
        },
        beforeMount(){
            this.init()
        },
        watch: {
            'id': 'init',
            'version':'init'
        },
        methods: {
            getAdminId(){
                if(!this.admin) return null;
                if(this.admin.id) return this.admin.id;
                return this.admin.userId;
            },
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
              
                let getData=Admin.show(this.id);
               
                getData.then(admin => {
                   
                    this.admin = {
                        ...admin
                    }; 

                    this.$emit('loaded',this.admin);
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            }, 
             
            onBack(){
                this.$emit('back');
            },
            beginEdit() {
                this.readOnly=false;
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
                
                let getData=User.edit(id);
                getData.then(model => {
                   
                    this.userSelector.user = {
                        ...model.user
                    }; 

                    this.$refs.editComponent.init();
                    
                })
                .catch(error=> {
                    Helper.BusEmitError('無法取得使用者資料,請稍後再試.')
                })


                this.userSelector.show=false;
                this.userSelector.model=null;
            },
            onSaved(admin){
                this.$emit('saved',admin);
                this.init();
            },  
            beginDelete(){
                
                let name=this.admin.user.profile.fullname;
                let id=this.getAdminId();

                this.deleteConfirm.msg='確定要刪除管理員 ' + name + ' 嗎？'
                this.deleteConfirm.id=id
                this.deleteConfirm.show=true                
            },
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteAdmin(){
                this.closeConfirm()
                
                let id = this.deleteConfirm.id 
                let remove= Admin.remove(id)
                remove.then(() => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },

            
        }
    }
</script>
