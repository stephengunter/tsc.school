<template>
<div>
    <signup :id="id" ref="signupView" :payways="payways"
     :can_edit="signupSettings.can_edit" :can_back="signupSettings.can_back"  
      @loaded="onSignupLoaded"   @back="onBack" @saved="onSignupSaved"  
      @deleted="onSignupDeleted" >
     
    </signup>

    <div v-if="signup">
        
        <div>
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 , 'label-title':true}" >
                    <a v-if="payed" @click.prevent="activeIndex=0" href="#" >列印收據</a>
                    <a v-else @click.prevent="activeIndex=0" href="#" >列印繳費單</a>
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

                    <quit-view v-if="activeIndex==1"  :signup="signup" :quit="quit"   
                       :read_mode="quitViewSettings.read_mode" :init_mode="quitViewSettings.init_mode"
                       @selected="viewQuitDetails" @back="initQuitView"
                       @saved="onQuitChanged" @deleted="onQuitChanged"> 

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
            mode:{
              type: String,
              default: ''
            },
            payways: {
              type: Array,
              default: null
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

                quitViewSettings:{
                    read_mode:'table',
                    init_mode:'',
                },

                needPrint:false,
               
                
                activeIndex:0,

               
                quit:null
            }
        },
        computed:{
            payed(){
               if(!this.signup) return false;
               return Helper.isTrue(this.signup.payed);
            }
            
        },
        beforeMount(){
            if(this.mode=='quit'){
                this.activeIndex=1;
                this.quitViewSettings.init_mode ='create';
                
            }
        },
        watch: {
            'id': 'init'
        },
        methods:{
            init(){
                this.initQuitView();
            },
            initQuitView(){
                this.quit=null;
                this.quitViewSettings.read_mode ='table';
               
            },
            onSignupLoaded(signup){
                
                this.signup={
                    ...signup
                };

                if(this.needPrint){
                   
                   setTimeout(()=>{ 
                        this.$refs.billPrint.print();
                        this.needPrint=false;
                   }, 1000);

                }
                
            },
            reloadSignup(){
                this.init();
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
            onQuitChanged(){  
                this.reloadSignup();   
                  this.quitViewSettings.init_mode ='';
            }, 
            beginPrint() {
                if(this.payed){
                    this.$refs.billPrint.print();
                    return;
                } 
                else{
                   
                    let initPrint=Signup.initPrint(this.signup.id);
                    initPrint.then(() => {
                        this.needPrint=true;
                        this.reloadSignup();
                    })
                    .catch(error=> {
                        
                        Helper.BusEmitError(error)
                    })
                }
                
            },
            viewQuitDetails(id){
                this.quit=this.signup.quits.find((item) => {
                    return item.id==id;
                });
                this.quitViewSettings.read_mode ='details';
            }
            
        }
        
    }
</script>
