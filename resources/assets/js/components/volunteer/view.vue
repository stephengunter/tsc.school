<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="volunteer" v-show="readOnly">
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                <button v-if="volunteer.canEdit" v-show="can_edit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
                </button>
                <button v-if="volunteer.canDelete" v-show="!hide_delete" @click.prevent="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i> 
                    刪除
                </button>
               
            </div>
        </div>  
        <div class="panel-body">
            <show v-if="readOnly"  :volunteer="volunteer" >  
            </show>
            <edit v-else :id="id" 
            @saved="onSaved"   @cancel="onEditCanceled" >                 
            </edit>
        </div>
        
    </div>
   
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteVolunteer">        
    </delete-confirm>
</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'
    export default {
        name:'Volunteer',
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
                icon:Menus.getIcon('volunteers') ,
                readOnly:true,

                volunteer:null,

                reviewEditor:{
                    show:false,
                    id:0,
                    reviewed:false,
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
                let text='志工資料';
              
               
                if(this.readOnly) return this.icon + ' ' + text;
                if(this.creating) return this.icon + ' 新增' + text;

              
                return `${this.icon}  編輯志工資料：${this.volunteer.user.profile.fullname}`
                
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
            getVolunteerId(){
                if(this.volunteer.userId) return  this.volunteer.userId;
                return this.volunteer.id;
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
                let getData=Volunteer.show(this.id);
               
                getData.then(volunteer => {
                   
                    this.volunteer = {
                        ...volunteer
                    }; 

                

                    this.$emit('loaded',this.volunteer);
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

                if(!this.id) this.$emit('back');
                this.init();
            },
            onSaved(){
                if(!this.id) this.$emit('back');
                this.init();
            },  
            beginDelete(){
                let name=this.volunteer.user.profile.fullname;
                let id=this.getVolunteerId();

                this.deleteConfirm.msg='確定要刪除 ' + name + ' 嗎?'
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;                
            },
            closeConfirm(){
                this.deleteConfirm.show=false;
            },
            deleteVolunteer(){
             
                let id = this.deleteConfirm.id; 
             
                let remove=Volunteer.remove(id);

                this.closeConfirm();

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
