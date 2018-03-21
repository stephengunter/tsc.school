<template>

    <term :id="id" :can_edit="can_edit" :can_back="can_back"  
        @back="onBack" @saved="onSaved"> 
    </term>  
    
  
</template>
<script>
    
    import TermComponent from '../../components/term/view.vue';
    
    export default {
        name: 'TermDetails',
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
              default: false
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
                term:null,

                termSettings:{
                    can_edit:true,
                    can_back:true,
                },
                
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
