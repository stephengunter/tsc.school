<template>
<div v-if="quit">
    <quit-view :quit="quit" ref="quitView" 
        :can_edit="quitSettings.can_edit" :can_back="quitSettings.can_back"  
        @back="onBack" @saved="onQuitSaved"  
        @deleted="onQuitDeleted" >
     
    </quit-view>

    


    

</div>    
</template>
<script>
   
   
    import QuitView from '../../components/quit/view.vue'
    
    export default {
        name: 'QuitDetails',
        components: {
            'quit-view':QuitView
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
                quit:null,

                quitSettings:{
                    can_edit:true,
                    can_back:true,
                 
                },
               
                
                activeIndex:0,
            }
        },
        computed:{
            
            payed(){
               if(!this.quit) return false;
               return parseInt(this.quit.status)!=0;
            }
        },
        beforeMount(){
           this.init()
        },
        watch: {
            'id': 'init'
        },
        methods:{
            init(){
                this.fetchData();
                
            },
            fetchData() {
              
                let getData=Quit.show(this.id);
               
                getData.then(quit => {
                   
                    this.quit={
                        ...quit
                    };
                   
                })
                .catch(error=> {
                    Helper.BusEmitError(error)
                })
            }, 
            onQuitSaved(){  
                this.fetchData();      
            },  
            onQuitDeleted(){
                this.$emit('back');
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
