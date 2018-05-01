<template>
<div>
    <lesson :id="id" ref="lessonView" 
     :can_edit="lessonSettings.can_edit" :can_back="lessonSettings.can_back"  
      @loaded="onLessonLoaded"   @back="onBack" @saved="reloadLesson"  
      @deleted="onLessonDeleted" >
     
    </lesson>

    <div v-if="lesson">
        
        <div>
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 , 'label-title':true}" >
                    <a @click.prevent="activeIndex=0" href="#" >課堂學員</a>
                </li>
            </ul>
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    
                    <lesson-students v-if="activeIndex==0" :lesson="lesson"
                        @saved="reloadLesson">

                    </lesson-students>
                    
                </div>
                          
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
   
    import LessonComponent from '../../components/lesson/view.vue';
    import LessonStudents from '../../components/lesson/students.vue';
    export default {
        name: 'LessonDetails',
        components: {
            'lesson':LessonComponent,
            'lesson-students':LessonStudents,
        },
        props: {
            id: {
              type: Number,
              default: 0
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
                lesson:null,

                lessonSettings:{
                    can_edit:true,
                    can_back:true,
                 
                },

                needPrint:false,
               
                
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
            onLessonLoaded(lesson){
                
                this.lesson={
                    ...lesson
                };
            },
            reloadLesson(){
                
                this.lesson=null;
                this.$refs.lessonView.init();
            },
            onLessonDeleted(){
                this.$emit('lesson-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
