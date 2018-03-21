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
                <button v-if="canEdit" @click="beginEdit" class="btn btn-primary btn-sm" >
                        <i class="fa fa-edit"></i> 編輯
                </button>
                <button v-if="canDelete" v-show="can_edit" @click="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i>  刪除
                </button>
               
            </div>
        </div>  
        <div class="panel-body">
            <show v-if="readOnly"  :user="user" >  
            </show>
            <edit v-else :id="userId" 
            @saved="onSaved"   @cancel="onEditCanceled" >                 
            </edit>
        </div>
        
    </div>
    
</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'
    export default {
        name:'User',
        components: {
            Show,
            Edit,
        },
        props: {
            model:{
                type: Object,
                default: null
            },
            id: {
                type: String,
                default: ''
            },
            can_edit:{
                type: Boolean,
                default: true
            },            
            can_back:{
                type: Boolean,
                default: true
            },
            can_delete:{
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
                icon:Menus.getIcon('users') ,
                readOnly:true,

                user:null,
            }
        },
        computed:{
            canEdit(){
                if(!this.user) return false;

                if(this.model){
                    return this.can_edit;
                }else{
                    if(!this.can_edit) return false;
                    return this.user.canEdit;
                }
            },
            canDelete(){
                if(!this.canEdit) return false;

                if(!this.user) return false;

                if(this.model){
                    return this.can_delete;
                }else{
                    if(!this.can_delete) return false;
                    return this.user.canDelete;
                }
            },
            userId(){
                if(this.user) return this.user.id;
                return 0;
            },
            title(){
               
                if(this.readOnly) return this.icon + ' 個人資料';
                return this.icon + ' 編輯個人資料';
            }
           
        },
        beforeMount(){
            this.init();
        },
        watch: {
            model: {
                handler: function () {
                
                   this.init();
                },
                deep: true
            },
        },
        methods: {
            init() {
               
                if(this.model){
                    this.loadModel();
                }else{
                    this.fetchData();
                } 

                this.readOnly=true;
                
            },
            loadModel(){
                
                this.user={
                    ...this.model
                };
            },
            fetchData() {
               
                let getData=User.show(this.id);
               
                getData.then(user => {
                   
                    this.user = {
                        ...user
                    }; 

                    this.$emit('loaded',this.user);
                })
                .catch(error=> {
                    
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
                this.readOnly=true;
            },
            onSaved(){
                this.init();
              
            }, 

            
        }
    }
</script>
