<template> 

  <img :src="path"  :class="class_name" :alt="title" >
                 
</template>
<script>

    export default {
        name: 'PhotoImage',
        props: {
            model: {
               type: Object,
               default: null
            },
            type:{
               type: String,
               default: 'profile'
            },
            title:{
               type: String,
               default: '個人相片'
            }
        },
        computed:{
            path(){
                if(this.model){
                    if( Helper.isTrue(model.public)){

                    }else{
                        return '/images/default.png';
                    }
                   
                }
                return '/images/default.png';
            }
            
        },
        data() {
             return {
               
                class_name:"img-thumbnail",

            }
        },
        beforeMount() {
            this.init()
        },  
        methods: {    
            init(){
                
            }
           
            

            
        }
    }
</script>