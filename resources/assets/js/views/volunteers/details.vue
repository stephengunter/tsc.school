<template>
<div>
    <volunteer :id="id" ref="volunteerView" 
      :can_edit="volunteerSettings.can_edit" :can_back="volunteerSettings.can_back"  
      @loaded="onVolunteerLoaded"   @back="onBack" @saved="onVolunteerSaved"  
      @deleted="onVolunteerDeleted" >
     
    </volunteer>

    <div v-if="volunteer">
        
        <div>
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 }" class="label-title">
                    <a @click.prevent="activeIndex=0" href="#" >個人資料</a>
                </li>
                <li :class="{ active: activeIndex==1 }" class="label-title">
                    <a @click.prevent="activeIndex=1" href="#" >聯絡資訊</a>
                </li>
               
            </ul>
       
           
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    <user v-if="activeIndex==0" :model="volunteer.user"
                     :can_back="false" :can_edit="volunteer.canEdit"
                     @saved="reloadVolunteer">
                    </user>

                    <contact-info v-if="activeIndex==1"  :model="volunteer.user"  
                        :type="contactInfoSettings.type"
                        :canEdit="volunteer.canEdit" :can_options="contactInfoSettings.can_options"
                        @created="reloadVolunteer" @deleted="reloadVolunteer" >             
                    </contact-info> 

                    
                </div>
                          
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
    
    import VolunteerComponent from '../../components/volunteer/view.vue';
    import UserComponent from '../../components/user/view.vue';
    import ContactInfoComponent from '../../components/contactInfo/view.vue';
    export default {
        name: 'VolunteerDetails',
        components: {
            'volunteer':VolunteerComponent,
            'user':UserComponent,
            'contact-info':ContactInfoComponent,
          
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
                volunteer:null,

                volunteerSettings:{
                    can_edit:true,
                    can_back:true,
                    role:'Volunteer'
                },
               
                
                activeIndex:0,

                userSettings:{
                    id:'',
                    can_edit:true,
                    can_back:false,
                },
              
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
            onVolunteerLoaded(volunteer){
                
                this.volunteer={
                    ...volunteer
                };

                if(this.volunteer.user){
                    this.volunteer.user.canEdit=volunteer.canEdit;
                    if(this.volunteer.user.contactInfo){
                        this.volunteer.user.contactInfo.canEdit=volunteer.canEdit;
                    }

                    this.userSettings.id=volunteer.user.id;
                    this.userSettings.user={
                        canEdit:volunteer.canEdit,
                        ...volunteer.user
                    };

                }

               
              

                
                
            },
            reloadVolunteer(){
                
                this.volunteer=null;
                this.$refs.volunteerView.init();
            },
            onVolunteerSaved(volunteer){  
                this.volunteer=volunteer   
                this.$emit('volunteer-saved',volunteer)        
            },  
            onVolunteerDeleted(){
                this.$emit('volunteer-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
