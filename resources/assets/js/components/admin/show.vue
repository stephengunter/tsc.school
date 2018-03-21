<template>
   
    <div v-if="admin" class="show-data">
        <div class="row" >
            <div  class="col-sm-3">
                <label class="label-title">姓名</label>
                <p v-text="admin.user.profile.fullname"></p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">所屬中心</label>
                <p>
                    {{ admin.centers }}
                </p>                   
            </div>
            <div class="col-sm-3">
                <label class="label-title">角色</label>
                <p v-html="roleLabels(admin.user)">
                   
                </p>                   
            </div>
            <div class="col-sm-3">
                   
            </div>
        </div>  <!-- End row--> 
        
        
        
    </div>
    
</template>

<script>
    export default {
        name: 'ShowAdmin', 
        props: {
            admin: {
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
            hasReviewedBy(){
                if(!this.admin) return false;
                if(!this.admin.reviewedBy) return false;
                return true;
            }
        }, 
        methods: { 
            roleLabels(user){
                return User.roleLabels(user.roles);
            },
            editReview(){
                this.$emit('edit-review')
            }
            
        }
    }
</script>
