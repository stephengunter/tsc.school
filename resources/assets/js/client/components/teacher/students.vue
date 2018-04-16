<template>
<div  v-if="teacher">
    <div>
        <div class="select is-medium">
            <select v-model="params.course" @change="fetchData"  >
                <option v-for="(item,index) in course_options" :key="index" :value="item.value">
                    {{ item.text }}
                </option>
            </select>
        </div>
      
    </div>
    <table  class="table">
       <thead>
            <tr>
                <th style="width:15%">姓名</th>
                <th v-if="edittingScore" style="width:15%">分數
                    <button @click.prevent="onSubmitScores" class="button is-small is-success">
                        <i class="fa fa-save"></i> 
                    </button>

                    <button @click.prevent="cancelEditScore" class="button is-small">
                        <i class="fa fa-undo"></i> 
                    </button>
                    
                    
                </th>
                <th v-else style="width:15%">分數
                    
                    <button @click.prevent="onEditScore" class="button is-small is-info">
                        <i class="fa fa-edit"></i> 
                    </button>
                </th>
                <th >Email</th>
                <th style="width:10%">手機</th>
                <th style="width:10%"></th>
                  
                
            </tr>
           
        </thead>
        <tbody>
            <tr v-for="(student,index) in students" :key="index">
               
                <td v-text="student.user.profile.fullname">
                   
                </td>
                <td v-if="edittingScore">
                    <input @keydown="clearErrorMsg(student)" type="text" name="student.score" class="input" v-model="student.score">
                    <p class="help is-danger" v-text="getError(student)"></p>
                    
                </td>
                <td v-else>{{  student.score | formatMoney }}</td>
                
                
                <td>{{  student.user.email }}</td>
                <td>{{  student.user.phone }}</td>
                <td>
                    <span v-if="student.status < 0" class="label label-default"> 已退出 </span>
                </td>
                
                
                
            </tr> 
            
        </tbody>
        
   </table>

        
        
</div>      
</template>


<script>
    export default {
        name:'CourseStudents',
        props: {
            teacher: {
                type: Object,
                default: null
            },
            courseId:{
                type: Number,
                default: 0
            },
            course_options:{
                type: Array,
                default: null
            },
        },
        data(){
            return {
                params:{
                    course:'0',
                },

                students:[],

                selectedCourseId:0,
                edittingScore:false,
                errors:[]
            }
        },
        watch: {
            
	    },
        beforeMount() {
         
            if(this.courseId) this.params.course=this.courseId;
            else{
                if(this.course_options && this.course_options.length){
                    this.params.course=this.course_options[0].value;
                }
            }

            this.fetchData();
            	
        },
        computed:{
            
           
        }, 
        methods:{
           
            fetchData() {

                let getData = Student.index(this.params);

                getData.then(students => {

                    this.students=students.slice(0);

                    for(let i=0; i< this.students.length ; i++){
                        let student=this.students[i];
                       
                        student.score= Helper.formatMoney(student.score);
                    }

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onEditScore(){
                this.edittingScore=true;
            },
            cancelEditScore(){
                this.edittingScore=false;
                this.fetchData();
            },
            getError(student){
                let index= this.errors.findIndex((item)=>{
                    return item.id==student.id;
                });
                if(index >= 0) return this.errors[index].msg;
                return '';
            },
            clearErrorMsg(student){
                let id=student.id;
                let index= this.errors.findIndex((item)=>{
                    return item.id==id;
                });


                if(index >= 0)  this.errors.splice(index, 1); 
            
            },
            onSubmitScores(){
            
                this.errors=[];

                
                this.students.forEach((student)=>{
                    let val=student.score
                    if(isNaN(val)){
                    
                    this.errors.push({
                        id:student.id,
                        msg:'必須為數字'
                    });
                    }
                });
            
                
                if(this.errors.length) return;

                this.updateScores()
            },
            updateScores(){
                let students = this.students.map(item=>{
                    return {
                                id:item.id,
                                score:item.score
                            }
                })

                
                let save=Student.updateScores(students)
                save.then(() => {
                        Helper.BusEmitOK('資料已存檔');
                        this.edittingScore=false;
                        this.fetchData();  
                    })
                    .catch(error => {
                        Helper.BusEmitError(error,'存檔失敗');
                        this.edittingScore=false;
                        this.fetchData();
                    })
            },
            
        }
    }
</script>





