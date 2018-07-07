<template>
<div>
    <course :id="id" ref="courseView" :percents_options="percents_options"
     :can_edit="courseSettings.can_edit" :can_back="courseSettings.can_back"  
      @loaded="onCourseLoaded"   @back="onBack" @saved="onCourseSaved"  
      @deleted="onCourseDeleted" >
     
    </course>

    <div v-if="course">
        
        <div>
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 }" class="label-title">
                    <a @click.prevent="activeIndex=0" href="#" >報名資訊</a>
                </li>
                <li :class="{ active: activeIndex==1 }" class="label-title">
                    <a @click.prevent="activeIndex=1" href="#" >上課時間</a>
                </li>
                <li :class="{ active: activeIndex==2 }" class="label-title">
                    <a @click.prevent="activeIndex=2" href="#" >教學大綱</a>
                </li>
            </ul>
       
           
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    <course-info v-if="activeIndex==0" :model="course"
                     :can_edit="course.canEdit"
                     @saved="reloadCourse">
                    </course-info>
                    <class-time v-if="activeIndex==1" :model="course"
                     :weekdays="weekdays" :can_edit="course.canEdit"
                     @saved="reloadCourse">
                    </class-time>
                    <process v-if="activeIndex==2" :course="course"
                     :can_edit="course.canEdit"
                     @saved="reloadCourse">
                    </process>
                </div>
                          
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
    
    import CourseComponent from '../../components/course/view.vue';
    import CourseInfoComponent from '../../components/course/info-view.vue';
    import ClassTimeComponent from '../../components/course/time-view.vue';
    import ProcessComponent from '../../components/course/process-view.vue';
    
    export default {
        name: 'CourseDetails',
        components: {
            'course':CourseComponent,
            'course-info':CourseInfoComponent,
            'class-time':ClassTimeComponent,
            'process':ProcessComponent,
            
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            weekdays:{
              type: Array,
              default: null
            },
            percents_options:{
              type: Array,
              default: null
            },
            can_back: {
              type: Boolean,
              default: true
            },
            version:{
               type: Number,
               default: 0
            }
        },
        data(){
            return{
                course:null,

                courseSettings:{
                    can_edit:true,
                    can_back:true,
                 
                },
               
                
                activeIndex:0,
            }
        },
        computed:{
            
        },
        beforeMount(){
           this.init()
        },
        watch: {
            'id': 'init'
        },
        methods:{
            init(){
                
                
            },
            onCourseLoaded(course){
                this.course={
                    ...course
                };
                
            },
            reloadCourse(){
                
                this.course=null;
                this.$refs.courseView.init();
            },
            onCourseSaved(course){  
                this.course={
                    ...course
                };
                this.$emit('course-saved',course)        
            },  
            onCourseDeleted(){
                this.$emit('course-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
