<template>
<div>
    <student :id="id" ref="studentView" 
      :can_edit="studentSettings.can_edit" :can_back="studentSettings.can_back"  
      @loaded="onStudentLoaded"   @back="onBack" @saved="onStudentSaved" >
    
     
    </student>

    <div v-if="student">
        
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
                    <user v-if="activeIndex==0" :model="student.user"
                     :can_back="false" :can_edit="student.canEdit"
                     @saved="reloadStudent">
                    </user>

                    <contact-info v-if="activeIndex==1"  :model="student.user"  
                        :type="contactInfoSettings.type"
                        :canEdit="student.canEdit" :can_options="contactInfoSettings.can_options"
                        @created="reloadStudent" @deleted="reloadStudent" >             
                    </contact-info> 

                    
                </div>
                          
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
    
    import StudentComponent from '../../components/student/view.vue';
    import UserComponent from '../../components/user/view.vue';
    import ContactInfoComponent from '../../components/contactInfo/view.vue';
   
   
    export default {
        name: 'StudentDetails',
        components: {
            'student':StudentComponent,
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
                student:null,

                studentSettings:{
                    can_edit:true,
                    can_back:true,
                 
                },

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
            onStudentLoaded(student){
                this.student={
                    ...student
                };

                if(this.student.user){
                    this.student.user.canEdit=student.canEdit;
                    if(this.student.user.contactInfo){
                        this.student.user.contactInfo.canEdit=student.canEdit;
                    }

                    this.userSettings.id=student.user.id;
                    this.userSettings.user={
                        canEdit:student.canEdit,
                        ...student.user
                    };

                }
                
            },
            reloadStudent(){
                
                this.student=null;
                this.$refs.studentView.init();
            },
            onStudentSaved(student){  
                this.student=student   
                this.$emit('student-saved',student)        
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
