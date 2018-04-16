<template>

    <volunteer :id="id" :can_edit="can_edit" :can_back="can_back"  
        @back="onBack" @saved="onSaved"> 
    </volunteer>  
    
  
</template>
<script>
    
    import VolunteerComponent from '../../components/volunteer/view.vue';
    
    export default {
        name: 'CreateVolunteerView',
        components: {
            'volunteer':VolunteerComponent,
           
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
