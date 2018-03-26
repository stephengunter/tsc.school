<template>
<div>
    <div v-if="model" class="panel panel-default">
        <div class="panel-heading">
           
            <div v-if="model" v-show="readOnly">
                <button v-if="model.canEdit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-plus-circle"></i>
                    新增
                </button>
            </div>
        </div>  
        <div class="panel-body">
            <table class="table table-striped" style="width:320px">
                <tbody>
                    <tr v-for="(teacher,index) in teachers" :key="index">
                        <td style="width:80%">{{  teacher.user.profile.fullname }}</td>
                        <td>
                            <button @click.prevent="onRemove(teacher)" class="btn btn-sm btn-danger">
                               <i class="fa fa-trash"></i> 
                            </button>
                        </td>
                    </tr>
                </tbody>

            </table> 
        </div>
        
    </div>

    <modal :showbtn="false"  :show.sync="teacherSelector.show"  @closed="teacherSelector.show=false" 
        effect="fade" :width="1200">
		<div slot="modal-header" class="modal-header">
			<button id="close-button" type="button" class="close"  @click="teacherSelector.show=false">
                x
			</button>
		</div>
	
		<div slot="modal-body" class="modal-body">
			<teacher-selector :center="true" :teachers="teacherSelector.teachers"
                @selected="addTeachers">

            </teacher-selector>
		</div>
    </modal>

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="removeTeacher">        
    </delete-confirm>
</div>
</template>
<script>
    import TeacherSelector from './selector';
    export default {
        name:'GroupTeacher',
        components: {
            'teacher-selector':TeacherSelector
        },
        props: {
            model:{
                type: Object,
                default: null
            },
        },
        data() {
            return {
                
                readOnly:true,
                teachers:[],

                teachersCanAdd:[],
                

                teacherSelector:{
                    show:false,
                    teachers:[],
                },

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
            }
        },
        computed:{
            groupId(){
                return this.model.id;
            }
        },
        beforeMount(){
            this.init()
        },
        watch: {
           
        },
        methods: {
            init() {
                this.teacherSelector.show=false;
                this.teacherSelector.teachers=[];

                this.fetchData();
            },
            fetchData() {
                
                let getData=TeacherGroup.teachers(this.groupId);
               
                getData.then(teachers => {
                
                    this.teachers = teachers.slice(0);
                })
                .catch(error=> {
                    
                    Helper.BusEmitError(error);
                })
            }, 
            beginEdit() {
                let getData=TeacherGroup.editTeacher(this.groupId);
                getData.then(teachers => {
                   
                    this.teacherSelector.teachers = teachers.slice(0);
                })
                .catch(error=> {
                    
                    Helper.BusEmitError(error);
                })

                this.teacherSelector.show=true;
            },
            onEditCanceled(){
                
            },
            addTeachers(teacherIds){
                
                let save=TeacherGroup.addTeachers(this.groupId, teacherIds);
                save.then(() => {
                    Helper.BusEmitOK();
                    this.init();
                })
                .catch(error => {
                    Helper.BusEmitError(error,'新增失敗');
                   
                })

                
            },  
            onRemove(teacher){
                
                let name=teacher.user.profile.fullname;
                let id=teacher.userId;

                this.deleteConfirm.msg='確定要將教師 ' + name + ' 從群組中移除嗎？'
                this.deleteConfirm.id=id
                this.deleteConfirm.show=true                
            },
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            removeTeacher(){
                
                let teacherId = this.deleteConfirm.id;
                
                let remove=TeacherGroup.removeTeacher(this.groupId, teacherId);
                remove.then(() => {
                    Helper.BusEmitOK('刪除成功');
                    this.fetchData();
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                   
                })

                this.closeConfirm();
            },

            
        }
    }
</script>
