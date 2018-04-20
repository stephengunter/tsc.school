<template>
<div>
   <edit :signup="signup"></edit>
    
</div>    
</template>


<script>
    import Edit from '../../components/bill/edit.vue';
    export default{
        name:'BillEditView',
        components: {
           Edit
        },
        props: {
            signup: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                
               
            }
        },
        computed:{
            hasDiscount(){
                return Signup.hasDiscount(this.signup);
            },
           
        }, 
        beforeMount(){
            
        },
       
        methods: {
            init(){
               
               
            }
            
            
        },



    }
</script>
