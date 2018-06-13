<template>
    <div>
        <doc :id="id" ref="docView" 
        :can_edit="docSettings.can_edit" :can_back="docSettings.can_back"  
        @loaded="onDocLoaded"   @back="onBack" @saved="onDocSaved"  
        @deleted="onDocDeleted" >
        
        </doc>

    </div>    
</template>
<script>
   
    import DocComponent from '../../components/doc/view.vue';
    
    export default {
        name: 'DocDetails',
        components: {
            'doc':DocComponent,
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            mode:{
              type: String,
              default: ''
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
                doc:null,

                docSettings:{
                    can_edit:true,
                    can_back:true,
                 
                },
               
            }
        },
        computed:{
           
            
        },
        beforeMount(){
           
        },
        watch: {
            'id': 'init'
        },
        methods:{
            init(){
                
            },
            onDocLoaded(doc){
                
                this.doc={
                    ...doc
                };
                
            },
            reloadDoc(){
              
                this.doc=null;
                this.$refs.docView.init();
            },
            onDocSaved(){  
                this.$emit('saved');   
            },  
            onDocDeleted(){
                this.$emit('deleted'); 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
