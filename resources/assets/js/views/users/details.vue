<template>
<div>
    <user :id="id" ref="userView"
     :can_edit="userSettings.can_edit" :can_back="userSettings.can_back"  
      @loaded="onUserLoaded"   @back="onBack" 
      @deleted="onUserDeleted" >
     
    </user>

    <div v-if="user">
        
            <ul class="nav nav-tabs">
                
                <li :class="{ active: activeIndex==0 }" class="label-title">
                    <a @click.prevent="activeIndex=0" href="#" >聯絡資訊</a>
                </li>
                <li :class="{ active: activeIndex==1 }" class="label-title">
                    <a @click.prevent="activeIndex=1" href="#" >報名紀錄</a>
                </li>
                <li :class="{ active: activeIndex==2 }" class="label-title">
                    <a @click.prevent="activeIndex=2" href="#" >學員紀錄</a>
                </li>
            </ul>
       
           
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">

                    <contact-info v-if="activeIndex==0"  :model="user"  
                        :type="contactInfoSettings.type"
                        :canEdit="user.canEdit" :can_options="contactInfoSettings.can_options"
                        @created="reloadUser" @deleted="reloadUser" >             
                    </contact-info> 

                    <signup-records v-if="activeIndex==1" :user="user">
                    </signup-records>

                    <student-records v-if="activeIndex==2" :user="user">
                    </student-records>

                </div>
                          
            </div>
       
    </div> 


    

</div>    
</template>
<script>
    
    import UserComponent from '../../components/user/view.vue';
    import ContactInfoComponent from '../../components/contactInfo/view.vue';
    import SignupRecords from '../../components/user/signups.vue';
    import StudentRecords from '../../components/user/students.vue';
    
    export default {
        name: 'UserDetails',
        components: {
            'user':UserComponent,
            'contact-info':ContactInfoComponent,
            'signup-records':SignupRecords,
            'student-records':StudentRecords,
           
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
                user:null,

                userSettings:{
                    can_edit:true,
                    can_back:true,
                   
                },
               
                
                activeIndex:0,

              
              
                contactInfoSettings:{
                    type:'user',
                    can_edit:true,
                    can_options:true
                },
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
            onUserLoaded(user){
                this.user={
                    ...user
                };

                if(this.user.contactInfo){
                    this.user.contactInfo.canEdit=user.canEdit;
                }
              
              

              
             
               
            },
            reloadUser(){
                
                this.user=null;
                this.$refs.userView.init();
            },
            
            onUserDeleted(){
                this.$emit('user-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
