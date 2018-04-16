<template>
<div>
    <teacher-view :model="teacher"  @fetch="fetchData">
     
    </teacher-view> 
    <div v-if="teacher">
        <div style="margin-top:1.2em">
            <div class="tabs">
                <ul style="font-size:16px">
                    <li :class="{ 'is-active ': activeIndex==0 }">
                        <a @click.prevent="activeIndex=0" href="#">開課紀錄</a>
                    </li>
                    <li :class="{ 'is-active ': activeIndex==1 }">
                        <a @click.prevent="activeIndex=1" href="#">學員成績</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="box" style="margin-top:1.2em">
            <courses v-if="activeIndex==0" :model="teacher"></courses>

            <students v-if="activeIndex==1" :teacher="teacher" :course_options="courseOptions"
             :courseId="courseId">

            </students>
        </div>
    </div> 
</div>
</template>


<script>
    import TeacherView from '../../components/teacher/view.vue';
    import TeacherCourses from '../../components/teacher/courses.vue';
    import CourseStudents from '../../components/teacher/students.vue';
    export default{
        name:'TeacherDetailsView',
        components: {
            'teacher-view':TeacherView,
            'courses':TeacherCourses,
            'students':CourseStudents                         
        },
        props: {
            init_model: {
               type: Object,
               default: null
            }
        },
        data(){
            return {
                teacher:null,
                activeIndex:0,

                courseId:0,
                courseOptions:[]
               
            }
        },
        beforeMount(){
            this.teacher={
                 ...this.init_model
            }

            this.setOptions();
        },
        methods: {
            fetchData() {
                
                let getData=Teacher.show();
               
                getData.then(teacher => {
                   
                    this.teacher = {
                        ...teacher
                    }; 
                    this.setOptions();
                })
                .catch(error=> {
                    
                    Helper.BusEmitError(error)
                })
            },  
            setOptions(){
                this.courseOptions=this.teacher.courses.map((course)=>{
                    return {
                        value:course.id,
                        text:course.center.name + ' ' +  course.fullName
                    };
                })
            }
            
        },



    }
</script>
