<template>
<div>
    <center :id="id" ref="centerView"
      :can_edit="centerSettings.can_edit" :can_back="centerSettings.can_back"  
      @loaded="onCenterLoaded"   @back="onBack" @saved="onCenterSaved"  
      @deleted="onCenterDeleted" >
     
    </center>

    <div v-if="center">
        
            <ul class="nav nav-tabs">
                <li :class="{ active: activeIndex==0 }" class="label-title">
                    <a @click.prevent="activeIndex=0" href="#" >聯絡資訊</a>
                </li>
            </ul>
       
           
            <div class="tab-content" style="margin-top:10px">
                <div class="tab-pane fade active in">
                    <contact-info v-if="activeIndex==0"  :model="center"  
                        :type="contactInfoSettings.type"
                        :canEdit="center.canEdit" :can_options="contactInfoSettings.can_options"
                        @created="reloadCenter" @deleted="reloadCenter" >             
                    </contact-info> 
                </div>
                          
            </div>
       
    </div> 


    

</div>    
</template>
<script>
    
    import CenterComponent from '../../components/center/view.vue';
    import ContactInfoComponent from '../../components/contactInfo/view.vue';
    
    export default {
        name: 'CenterDetails',
        components: {
            'center':CenterComponent,
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
                center:null,

                centerSettings:{
                    can_edit:true,
                    can_back:true,
                    role:'Center'
                },
               
                
                activeIndex:0,
              
                contactInfoSettings:{
                    type:'center',
                    can_edit:true,
                    can_options:true
                },
                signupSettings:{
                    disable_edit:true
                }
            }
        },
        computed:{
            activeContactInfo(){
                if(!this.center) return false;
                return this.activeIndex==0;
            }
        },
        beforeMount(){
           this.init()
        },
        watch: {
           
        },
        methods:{
            init(){
                
                this.activeIndex=0;
                
                this.centerSettings={
                    can_edit:true,
                    can_back:this.can_back,
                    role:'Center'
                }

                this.contactInfoSettings={
                    id:0,
                    center_id:this.id,
                    can_edit:true,
                }
               
               
            },            
            onCenterLoaded(center){
                this.center={
                    ...center
                };
                //this.contactInfoSettings.can_options=!Helper.isTrue(center.oversea) 
                //this.setContactInfo(center.contact_info)
                //this.centerLoaded=true
            },
            reloadCenter(){
                this.center=null;
                this.$refs.centerView.init();
            },
            onCenterSaved(center){  
                this.center=center   
                this.$emit('center-saved',center)        
            },  
            onCenterDeleted(){
                this.$emit('center-deleted') 
            },
            onContactInfoCreated(){
                this.setContactInfo(contactInfoId)
            },
            
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
