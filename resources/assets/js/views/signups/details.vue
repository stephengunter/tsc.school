<template>
<div>
    <signup :id="id" ref="signupView" 
     :can_edit="signupSettings.can_edit" :can_back="signupSettings.can_back"  
      @loaded="onSignupLoaded"   @back="onBack" @saved="onSignupSaved"  
      @deleted="onSignupDeleted" >
     
    </signup>

    <div v-if="signup">
        
        <div>
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 , 'label-title':true}" >
                    <a @click.prevent="activeIndex=0" href="#" >列印繳費單</a>
                </li>
                <li v-show="payed" :class="{ 'active ': activeIndex==1 , 'label-title':true}">
                    <a @click.prevent="activeIndex=1" href="#">退費申請</a>
                </li>
            </ul>
       
           
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    <div v-show="activeIndex==0"  class="panel panel-default">
                        <div class="panel-heading">
                            <div>
                                <button  @click.prevent="beginPrint" class="btn btn-primary btn-sm" >
                                    <i class="fa fa-print"></i>
                                    列印
                                </button>
                            </div>
                        </div>  
                        <div class="panel-body">
                            <bill-print  :signup="signup" ref="billPrint"></bill-print>

                        </div>
                        
                    </div>

                    <quit-view v-if="activeIndex==1"  :signup="signup"  
                        @saved="reloadSignup" @deleted="reloadSignup"> 

                    </quit-view>
                     
                    
                </div>
                          
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
   
    import SignupComponent from '../../components/signup/view.vue';
    import BillPrint from '../../components/signup/bill-print.vue'; 
    import QuitView from '../../components/quit/view.vue'
    
    export default {
        name: 'SignupDetails',
        components: {
            'signup':SignupComponent,
            'bill-print':BillPrint,
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
                signup:null,

                signupSettings:{
                    can_edit:true,
                    can_back:true,
                 
                },
               
                
                activeIndex:0,
            }
        },
        computed:{
            payed(){
               if(!this.signup) return false;
               return parseInt(this.signup.status)!=0;
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
                
                
            },
            onSignupLoaded(signup){
                this.signup={
                    ...signup
                };
                
            },
            reloadSignup(){
                
                this.signup=null;
                this.$refs.signupView.init();
            },
            onSignupSaved(signup){  
                this.signup=signup   
                this.$emit('signup-saved',signup)        
            },  
            onSignupDeleted(){
                this.$emit('signup-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            beginPrint() {
                this.$refs.billPrint.print();
            },
            
        }
        
    }
</script>
