<template>

    <teacher :id="id" :group="group" :can_edit="can_edit" :can_back="can_back"  
        @back="onBack" @saved="onSaved"> 
    </teacher>  
    
  
</template>
<script>
    
    import TeacherComponent from '../../components/teacher/view.vue';
    
    export default {
        name: 'CreateTeacherView',
        components: {
            'teacher':TeacherComponent,
           
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            group:{
               type: Boolean,
               default: false
            }, 
            can_edit: {
              type: Boolean,
              default: true
            },
            can_back: {
              type: Boolean,
              default: true
            },
        },
        data(){
            return{
               
                
            }
        },
        beforeMount(){
           this.init()
        },
        watch: {
           
        },
        methods:{
            init(){
               
               
            },
            onBack(){
                this.$emit('back');
            },
            onSaved(){
                this.$emit('back');    
            }
            
        }
        
    }
</script>
