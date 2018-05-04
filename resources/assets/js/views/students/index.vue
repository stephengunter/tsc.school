<template>
   
    <div v-if="model" >
        <div class="row">
            <div class="col-sm-2" style="margin-top: 3px;"> 
                <h3 v-html="title">
                </h3>
            </div>
            <div class="col-sm-6 form-inline" style="margin-top: 20px;">
                <div class="form-group" style="padding-left:1em;">
                    <drop-down :items="terms" :selected="params.term"
                        @selected="onTermSelected">
                    </drop-down>
                </div>
                <div  class="form-group" style="padding-left:1em;">
                    <drop-down :items="centers" :selected="params.center"
                        @selected="onCenterSelected">
                    </drop-down>
                </div>
                <div v-if="courseOptions.length" class="form-group" style="padding-left:1em;">
                    <drop-down :items="courseOptions" :selected="params.course"
                        @selected="onCourseSelected">
                    </drop-down>
                </div>
            
                
                
            </div>
            <div class="col-sm-3" style="margin-top: 20px;">
               
            </div>
            <div class="col-sm-1 pull-right" align="right" style="margin-top: 20px;">
                <a :disabled="checkedIds.length < 1" @click.prevent="onSubmitPrint" href="#" class="btn btn-success">
                    <i class="fa fa-check-circle"></i>
                    列印證書
                </a>
            </div>
            
        </div>
            
        <div v-if="hasCourse" class="row">
           
            <div class="col-sm-6" style="margin-top: 3px;font-size:1.2em"> 
                <div class="form-group" >
                     學員數：{{ total }} 人   已退出：{{ canceled }} 人
                </div>
               
            </div>
        </div> 

        
        <student-table :model="model" :can_edit_score="canEditScore" @refresh="fetchData"
            @selected="onSelected" @check-changed="onCheckIdsChanged">
            
        </student-table>
        
        

    </div>

        
        
   
</template>


<script>
    import StudentTable from '../../components/student/table';
    
    export default {
        name:'StudentIndexView',
        components: {
            'student-table':StudentTable
        },
        props: {
            init_model: {
                type: Object,
                default: null
            },
            terms:{
                type:Array,
                default:null
            },
            centers:{
                type:Array,
                default:null
            },
            courses:{
                type:Array,
                default:null
            },
            version:{
                type:Number,
                default:0
            },
            can_edit_scores:{
                type:Boolean,
                default:false
            }
        },
        data(){
            return {
                title: Menus.getIcon('students')  + '  學員管理',
               

                model:null,

                courseOptions:[],
                
                params:{
                    term:'0',
                    center:'0',
                    course:'0',
                 
                },
              
                canEditScore:false,

                checkedIds:[]
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            if(this.init_model){
                this.model={...this.init_model };
               
                this.params.page=this.init_model.pageNumber;
                this.params.pageSize=this.init_model.pageSize;
            }  

            this.courseOptions=this.courses.slice(0);
           
            this.params.term=this.terms[0].value;
            this.params.center=this.centers[0].value;

            if(this.courses.length) this.params.course=this.courses[0].value;

            this.canEditScore=this.can_edit_scores;
            
            this.onDataLoaded();	
           
        },
        computed:{
            hasCourse(){
                return Helper.tryParseInt(this.params.course) > 0;
            },
            total(){
                return this.getList().length;
            },
            canceled(){
                if(this.total>0){
                    let canceledStudent=this.getList().filter((student)=>{
                        return parseInt(student.status) < 0;
                    })
                    return canceledStudent.length;
                }

                return 0;
            }
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onCreate(){
                this.$emit('create',this.params.course);
            },
            onSelected(id){
                this.$emit('selected',id);
            },
            onTermSelected(item){
                this.params.term=item.value;
                this.params.course='0';
                this.fetchData()
            },
            onCenterSelected(item){
                this.params.center = item.value;
                this.params.course='0';
                this.fetchData();
            },
            onCourseSelected(item){
                this.params.course = item.value;
                this.fetchData();
            },
            fetchData() {
                
                let getData = Student.index(this.params);

                getData.then(data => {

                    this.model={ ...data.model };
                    this.courseOptions=data.courseOptions.slice(0);

                    if(!this.hasCourse){
                        if(this.courseOptions.length) this.params.course=this.courseOptions[0].value;
                    }

                    this.canEditScore=data.canEditScores;
                    

                    this.onDataLoaded();

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onDataLoaded(){
                for(let i=0; i< this.model.viewList.length ; i++){
                    let student=this.model.viewList[i];
                  
                    student.score= Helper.formatMoney(student.score);
                }
            },
            onCheckIdsChanged(ids){
                this.checkedIds=ids.slice(0);
            },
            onSubmitPrint(){
                
            }
            
        }
    }
</script>





