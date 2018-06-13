<template>

    <doc :id="id" :can_edit="can_edit" :can_back="can_back"  
        @back="onBack" @saved="onSaved"> 
    </doc>  
    
  
</template>
<script>
    
    import DocComponent from '../../components/doc/view.vue';
    
    export default {
        name: 'CreateDocView',
        components: {
            'doc':DocComponent,
        },
        props: {
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
               id:0
                
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
            onSaved(){
              
                this.$emit('saved');   
            }
            
        }
        
    }
</script>
