<template>
<div>
    <signup :id="id" ref="signupView" 
     :can_edit="signupSettings.can_edit" :can_back="signupSettings.can_back"  
      @loaded="onSignupLoaded"   @back="onBack" @saved="onSignupSaved"  
      @deleted="onSignupDeleted" >
     
    </signup>

    <!-- <div v-if="signup">
        
        <div>
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 }" class="label-title">
                    <a @click.prevent="activeIndex=0" href="#" >報名資訊</a>
                </li>
                
            </ul>
       
           
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    <signup-info v-if="activeIndex==0" :model="signup"
                     :can_edit="signup.canEdit"
                     @saved="reloadSignup">
                    </signup-info>
                    
                </div>
                          
            </div>
        </div>
    </div>  -->


    

</div>    
</template>
<script>
    
    import SignupComponent from '../../components/signup/view.vue';
    
    export default {
        name: 'SignupDetails',
        components: {
            'signup':SignupComponent,
            
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
            
        }
        
    }
</script>
