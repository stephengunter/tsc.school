<template>
<div>
    
    <div class="columns" >
            
        <div class="column">
            <a href="#" @click.prevent="print" class="button is-info is-outlined is-medium" >
                <span class="icon" style="vertical-align:middle;">
                    <i class="fa fa-download"></i> 
                </span>
                下載帳單 便利商店列印繳費
            </a>
        </div>
        
    </div>   
    
    <bill-print :signup="signup" ref="billPrint"></bill-print>

   
</div>    
</template>


<script>
  
    import BillPrint from '../../components/bill/print.vue';
    export default{
        name:'BillPrintView',
        components: {
           'bill-print':BillPrint
        },
        props: {
            signup: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                showPrint:false,
                can_edit:false,
                options:{
                    height: 50,
                }
            }
        },
        computed:{
            hasDiscount(){
                return Signup.hasDiscount(this.signup);
            },
           
        }, 
        beforeMount(){
            
        },
        mounted(){
           
        },
        methods: {
            init(){
               
               
            },
            print() {
                this.$refs.billPrint.print();
               
            }
            
            
        },



    }
</script>
