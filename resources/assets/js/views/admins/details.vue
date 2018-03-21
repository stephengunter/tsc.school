<template>
<div>
    <admin :id="id" ref="adminView"
     :can_edit="adminSettings.can_edit" :can_back="adminSettings.can_back"  
      @loaded="onAdminLoaded"   @back="onBack" @saved="onAdminSaved"  
      @deleted="onAdminDeleted" >
     
    </admin>

    <div v-if="admin">
        
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
                    <user ref="userView" v-show="activeIndex==0" :model="admin.user"
                     :can_back="userSettings.can_back" :can_edit="userSettings.can_edit"
                   
                     @saved="reloadAdmin">
                    </user>

                    <contact-info v-if="activeIndex==1"  :model="admin.user"  
                        :type="contactInfoSettings.type"
                        :canEdit="admin.canEdit" :can_options="contactInfoSettings.can_options"
                        @created="reloadAdmin" @deleted="reloadAdmin" >             
                    </contact-info> 
                </div>
                          
            </div>
       
    </div> 


    

</div>    
</template>
<script>
    
    import AdminComponent from '../../components/admin/view.vue';
    import UserComponent from '../../components/user/view.vue';
    import ContactInfoComponent from '../../components/contactInfo/view.vue';
    
    export default {
        name: 'AdminDetails',
        components: {
            'admin':AdminComponent,
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
                admin:null,

                adminSettings:{
                    can_edit:true,
                    can_back:true,
                    role:'Admin'
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
            onAdminLoaded(admin){
                

                this.admin={
                    ...admin
                };

                this.admin.user.canEdit=admin.canEdit;
                this.admin.user.contactInfo.canEdit=admin.canEdit;

                
               
            },
            reloadAdmin(){
                
                this.admin=null;
                this.$refs.adminView.init();
            },
            onAdminSaved(admin){  
                 
                this.$emit('admin-saved',admin)        
            },  
            onAdminDeleted(){
                this.$emit('admin-deleted') 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
