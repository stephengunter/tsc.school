<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="student" v-show="readOnly">
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                <button v-if="student.canTrans" v-show="can_edit" @click.prevent="beginTrans" class="btn btn-warning btn-sm" >
                    <i class="fa fa-share-square"></i>
                    轉班
                </button>
                <button v-if="student.canEdit" v-show="can_edit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
                </button>
            </div>
        </div>  
        <div class="panel-body">
            <show v-if="readOnly"  :student="student" >  
            </show>
            <div v-else >
           
                <tran-edit v-if="trans" :student_id="this.id"  @saved="onSaved"   @cancel="onEditCanceled" > 

                </tran-edit>
                <edit  v-else :id="id"
                    @saved="onSaved"   @cancel="onEditCanceled" >                 
                </edit>
            </div>
        </div>
        
    </div>
   
   
</div>
</template>
<script>
    import Show from './show.vue';
    import Edit from './edit.vue';
    import TranEdit from '../tran/edit.vue';
    export default {
        name:'Student',
        components: {
            Show,
            Edit,
            'tran-edit':TranEdit
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
                icon:Menus.getIcon('students') ,
                readOnly:true,
                trans:false,
                student:null,

            }
        },
        computed:{
            creating(){
                if(this.readOnly) return false;
                if(this.id)  return false;
                return true;
            },
            title(){
                let text='學生資料';
              
               
                if(this.readOnly) return this.icon + ' ' + text;
                if(this.creating) return this.icon + ' 新增' + text;

                if(this.trans) return `${this.icon}  學生轉班：${this.student.user.profile.fullname} ${this.student.course.fullName}`
              
                return `${this.icon}  編輯學生資料：${this.student.user.profile.fullname}`
                
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
            getStudentId(){
                if(this.student.userId) return  this.student.userId;
                return this.student.id;
            },
            init() {
                if(this.id){
                    this.fetchData();
                    this.readOnly=true;
                }else{
                    this.readOnly=false;                    
                }

                this.trans=false;
            },
            fetchData() {
                let getData=Student.show(this.id);
               
                getData.then(student => {
                   
                    this.student = {
                        ...student
                    }; 


                    this.$emit('loaded',this.student);
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
                this.trans=false;
            },
            beginTrans(){
                this.readOnly=false;
                this.trans=true;
            },
            onEditCanceled(){

                if(!this.id) this.$emit('back');
                this.init();
            },
            onSaved(){
                if(!this.id) this.$emit('back');
                this.init();
            },  

            
        }
    }
</script>
