<template>

    <term :id="id" :can_edit="can_edit" :can_back="can_back"  
        @back="onBack" @saved="onSaved"> 
    </term>  
    
  
</template>
<script>
    
    import TermComponent from '../../components/term/view.vue';
    
    export default {
        name: 'CreateTermView',
        components: {
            'term':TermComponent,
           
        },
        props: {
            id: {
              type: Number,
              default: 0
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
