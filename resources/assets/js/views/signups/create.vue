<template>

    <signup :id="id" :params="params" :can_edit="can_edit" :can_back="can_back"  
        @back="onBack" @saved="onSaved"> 
    </signup>  
    
  
</template>
<script>
    
    import SignupComponent from '../../components/signup/view.vue';
    
    export default {
        name: 'CreateSignupView',
        components: {
            'signup':SignupComponent,
           
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            params:{
               type:Object,
               default: null
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
                
                this.$emit('cancel');
            },
            onSaved(signup){
               
                this.$emit('saved',signup);    
            }
            
        }
        
    }
</script>
