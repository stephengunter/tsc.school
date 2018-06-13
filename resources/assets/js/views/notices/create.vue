<template>

    <notice :id="id" :can_edit="can_edit" :can_back="can_back"  
        @back="onBack" @saved="onSaved"> 
    </notice>  
    
  
</template>
<script>
    
    import NoticeComponent from '../../components/notice/view.vue';
    
    export default {
        name: 'CreateNoticeView',
        components: {
            'notice':NoticeComponent,
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
