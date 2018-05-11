<template>
<div v-if="volunteer">
    <div class="show-data">
        <div class="row" >
            <div  class="col-sm-4">
                <label class="label-title">姓名</label>
                <p v-text="volunteer.user.profile.fullname"></p>                      
            </div>
            <div class="col-sm-8">
                <label class="label-title">所屬中心</label>
                <p v-text="volunteer.centersText">
                 
                </p>                   
            </div>
            <div v-if="false" class="col-sm-4">
                <label class="label-title">加入日期</label>
                <p v-if="volunteer.joinDate">
                    {{  volunteer.joinDate  }}
                </p>                   
            </div>
            
        </div>  <!-- End row--> 
        <div class="row" >
            <div class="col-sm-4">
                <label class="label-title">可服務時段</label>
                <p v-text="volunteer.time"></p>                    
            </div>
            <div class="col-sm-8">
                <label class="label-title">可服務時間</label>
                <p v-text=" volunteer.weekdaysText">
                 
                </p> 
                                    
            </div>
            
        </div>  <!-- End row--> 
        <div class="row" >
            <div  class="col-sm-12">
                <label class="label-title">備註</label>
                <p v-text="volunteer.ps"></p>                      
            </div>
            
            
        </div>  <!-- End row--> 
        
        
    </div>


   
</div>    
</template>

<script>
    export default {
        name: 'ShowVolunteer', 
        props: {
            volunteer: {
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
            
            
        }
    }
</script>
