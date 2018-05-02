<template>
<div>
    <!-- <payroll :id="id" ref="payrollView" 
     :can_edit="payrollSettings.can_edit" :can_back="payrollSettings.can_back"  
      @loaded="onPayrollLoaded"   @back="onBack" @saved="reloadPayroll"  
      @deleted="onPayrollDeleted" >
     
    </payroll> -->

    <div v-if="payroll">
        
        <div>
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 , 'label-title':true}" >
                    <a @click.prevent="activeIndex=0" href="#" >課堂學員</a>
                </li>
            </ul>
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    
                   
                    
                </div>
                          
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
   
    //import PayrollComponent from '../../components/payroll/view.vue';
    export default {
        name: 'PayrollDetails',
        components: {
            // 'payroll':PayrollComponent,
            // 'payroll-students':PayrollStudents,
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
                payroll:null,

                payrollSettings:{
                    can_edit:true,
                    can_back:true,
                 
                },

                needPrint:false,
               
                
                activeIndex:0,
            }
        },
        computed:{
            
        },
        beforeMount(){
           this.init()
        },
        watch: {
            'id': 'init'
        },
        methods:{
            init(){
               
            },
            onPayrollLoaded(payroll){
                
                this.payroll={
                    ...payroll
                };
            },
            reloadPayroll(){
                
                this.payroll=null;
                this.$refs.payrollView.init();
            },
            onPayrollDeleted(){
                this.$emit('payroll-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
