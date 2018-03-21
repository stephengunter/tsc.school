<template>
<div>
    <student :id="id" ref="studentView" 
     :can_edit="studentSettings.can_edit" :can_back="studentSettings.can_back"  
      @loaded="onStudentLoaded"   @back="onBack" @saved="onStudentSaved"  
      @deleted="onStudentDeleted" >
     
    </student>

    


    

</div>    
</template>
<script>
    
    import StudentComponent from '../../components/student/view.vue';
    
    export default {
        name: 'StudentDetails',
        components: {
            'student':StudentComponent,
            
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
                student:null,

                studentSettings:{
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
            onStudentLoaded(student){
                this.student={
                    ...student
                };
                
            },
            reloadStudent(){
                
                this.student=null;
                this.$refs.studentView.init();
            },
            onStudentSaved(student){  
                this.student=student   
                this.$emit('student-saved',student)        
            },  
            onStudentDeleted(){
                this.$emit('student-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
