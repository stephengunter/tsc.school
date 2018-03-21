<template>

    <admin :id="id" :can_edit="can_edit" :can_back="can_back"  
        @back="onBack" @saved="onSaved"> 
    </admin>  
    
  
</template>
<script>
    
    import AdminComponent from '../../components/admin/view.vue';
    
    export default {
        name: 'CreateAdminView',
        components: {
            'admin':AdminComponent,
           
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
                
                this.$emit('cancel');
            },
            onSaved(){
                
                this.$emit('saved');    
            }
            
        }
        
    }
</script>
