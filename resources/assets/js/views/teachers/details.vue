<template>
<div>
    <teacher :id="id" ref="teacherView" :group="group"
     :can_edit="teacherSettings.can_edit" :can_back="teacherSettings.can_back"  
      @loaded="onTeacherLoaded"   @back="onBack" @saved="onTeacherSaved"  
      @deleted="onTeacherDeleted" >
     
    </teacher>

    <div v-if="teacher">
        <div v-if="group">
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 }" class="label-title">
                    <a @click.prevent="activeIndex=0" href="#" >群組中的教師</a>
                </li>
                <li :class="{ active: activeIndex==1 }" class="label-title">
                    <a @click.prevent="activeIndex=1" href="#" >開課紀錄</a>
                </li>
            </ul>
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    
                    <group-view v-if="activeIndex==0" :model="teacher"></group-view>

                    <courses v-if="activeIndex==1" :model="teacher"></courses>
                </div>
                          
            </div>
        </div>
        <div v-else>
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 }" class="label-title">
                    <a @click.prevent="activeIndex=0" href="#" >個人資料</a>
                </li>
                <li :class="{ active: activeIndex==1 }" class="label-title">
                    <a @click.prevent="activeIndex=1" href="#" >聯絡資訊</a>
                </li>
                <li :class="{ active: activeIndex==2 }" class="label-title">
                    <a @click.prevent="activeIndex=2" href="#" >開課紀錄</a>
                </li>
            </ul>
       
           
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    <user v-if="activeIndex==0" :model="teacher.user"
                     :can_back="false" :can_edit="teacher.canEdit"
                     @saved="reloadTeacher">
                    </user>

                    <contact-info v-if="activeIndex==1"  :model="teacher.user"  
                        :type="contactInfoSettings.type"
                        :canEdit="teacher.canEdit" :can_options="contactInfoSettings.can_options"
                        @created="reloadTeacher" @deleted="reloadTeacher" >             
                    </contact-info> 

                    <courses v-if="activeIndex==2" :model="teacher"></courses>
                </div>
                          
            </div>
        </div>
    </div> 


    

</div>    
</template>
<script>
    
    import TeacherComponent from '../../components/teacher/view.vue';
    import TeacherCourses from '../../components/teacher/courses.vue';
    import UserComponent from '../../components/user/view.vue';
    import ContactInfoComponent from '../../components/contactInfo/view.vue';
    import GroupTeacherComponent from '../../components/teacher/group-view.vue';
    export default {
        name: 'TeacherDetails',
        components: {
            'teacher':TeacherComponent,
            'courses':TeacherCourses,
            'user':UserComponent,
            'contact-info':ContactInfoComponent,
            'group-view':GroupTeacherComponent,
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            group: {
              type: Boolean,
              default: false
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
                teacher:null,

                teacherSettings:{
                    can_edit:true,
                    can_back:true,
                    role:'Teacher'
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
            onTeacherLoaded(teacher){
                this.teacher={
                    ...teacher
                };

                this.teacher.user.canEdit=teacher.canEdit;
                this.teacher.user.contactInfo.canEdit=teacher.canEdit;

                this.userSettings.id=teacher.user.id;
                this.userSettings.user={
                    canEdit:teacher.canEdit,
                    ...teacher.user
                };
                
            },
            reloadTeacher(){
                
                this.teacher=null;
                this.$refs.teacherView.init();
            },
            onTeacherSaved(teacher){  
                this.teacher=teacher   
                this.$emit('teacher-saved',teacher)        
            },  
            onTeacherDeleted(){
                this.$emit('teacher-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
