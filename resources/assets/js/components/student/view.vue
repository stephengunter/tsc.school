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
           
                <tran-edit v-if="trans" :model="tranCreate.model" :student_id="student.id"  @saved="onSaved"   @cancel="onEditCanceled" > 

                </tran-edit>
                <edit v-else :id="id"
                    @saved="onSaved"   @cancel="onEditCanceled" >                 
                </edit>
            </div>
        </div>
        
    </div>
    <modal :showbtn="false"  :show.sync="courseSelector.show"  @closed="courseSelector.show=false" 
			effect="fade" :width="1200">
        <div slot="modal-body" class="modal-body">
            <tran-course-selector v-if="courseSelector.show" :student_id="student.id"
                :centers="courseSelector.centerOptions" 
                :center_id="courseSelector.center_id" :init_courses="courseSelector.init_courses"
                @submit="onSubmitCoursesToTran">
            </tran-course-selector>
        </div>
	</modal>
   
</div>
</template>
<script>
    import Show from './show.vue';
    import Edit from './edit.vue';
    import TranEdit from '../tran/edit.vue';
    import TranCourseSelector from '../tran/course-selector.vue';
    export default {
        name:'Student',
        components: {
            Show,
            Edit,
            'tran-edit':TranEdit,
            'tran-course-selector':TranCourseSelector
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

                tranCreate:{
                    model:null,
                    tran:null
                },

                courseSelector:{
                    show:false,
                    course:null,


                    center_id:0,
                    centerOptions:[],
                    init_courses:[]
                },

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

                    if(student.tran){
                        this.tranCreate.tran={
                            ...student.tran
                        }
                    }

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
                let params={
                    student:this.student.id
                };
                let create=Tran.create(params);
                create.then(data => {
                   this.courseSelector.centerOptions=data.centerOptions.slice(0);
                   this.courseSelector.init_courses=data.courses.slice(0);
                   this.courseSelector.center_id=data.centerId;

                   this.courseSelector.show=true;
                    
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
               
               
            },
            onSubmitCoursesToTran(course){

                this.tranCreate.model={
                    tran:{ ...this.tranCreate.tran },
                    student:{ ...this.student },
                    course:{ ...course}
                };
              
                this.courseSelector.show=false;

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
