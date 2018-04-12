<template>
   
    <div v-if="user" class="show-data">
        
        <div class="row">
            <div class="col-sm-3">
                <!-- <photo :id="$options.filters.tryParseInt(user.profile.photo_id)"></photo> -->
            </div> 
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="label-title">Email</label>
                        <p> {{ user.email }} </p>
                    </div>  
                    <div class="col-sm-4">
                        <label class="label-title">手機</label>
                        <p> {{ user.phone }} </p>                         
                    </div> 
                    <div class="col-sm-4">
                        <label class="label-title">角色</label>
                        <p v-html="roleLabels()"> </p>  
                    </div>    
                </div> <!-- End Row -->
                <div class="row">
                    <div class="col-sm-4">
                        <label class="label-title">真實姓名</label>
                        <p> {{ user.profile.fullname }} </p>
                    </div>  
                    <div class="col-sm-4">
                        <label class="label-title">性別</label>
                        <p> {{ user.profile.gender | genderText  }} </p>                    
                    </div> 
                    <div class="col-sm-4">
                        
                    </div>    
                </div>  <!-- End Row -->
                <div class="row">
                    <div class="col-sm-4">
                        <label class="label-title">身分證號</label>
                        <p> {{ user.profile.sid }} </p>

                    </div>  
                    <div class="col-sm-4">
                        <label class="label-title">生日</label>
                        <p> {{ user.profile.dob }} </p>                  
                    </div> 
                    <div class="col-sm-4">
                        
                    </div>    
                </div>  <!-- End Row -->
                
            </div>     
        </div>
        
    </div>
    
</template>

<script>
    export default {
        name: 'ShowUser', 
        props: {
            user: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {

            }
        },
        computed:{
            
        }, 
        methods: { 
            
            roleLabels(){
                if(this.user.roleNames) return User.roleLabels(this.user.roleNames);
                return User.roleLabels(this.user.roles);
            }
            
        }
    }
</script>
