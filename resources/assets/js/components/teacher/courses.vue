<template>
   
    <div v-if="model" >
        

        <course-table :courses="model.courses" :can_select="false" :can_review="false" 
            :center="false" :show_teachers="false" :show_categories="false">
        
        </course-table>
        
        

    </div>

        
        
   
</template>


<script>
    import CourseTable from '../../components/course/table';
    
    export default {
        name:'TeacherCourses',
        components: {
            'course-table':CourseTable
        },
        props: {
            model: {
                type: Object,
                default: null
            }
        },
        data(){
            return {

                
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            
            	
        },
        computed:{
            
           
           
        }, 
        methods:{
            
            
            
        }
    }
</script>





