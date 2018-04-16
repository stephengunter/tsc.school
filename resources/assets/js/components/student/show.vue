<template>
<div v-if="student">
    
    <div class="show-data">
        <div class="row" >
            <div  class="col-sm-4">
                <label class="label-title">姓名</label>
                <p v-text="student.user.profile.fullname"></p>                      
            </div>
            <div class="col-sm-4">
                <label class="label-title">課程</label>
                <p v-text="student.course.fullName">
                 
                </p>                   
            </div>
            <div class="col-sm-4">
                <label class="label-title">狀態</label>
                <p>
                    <span  v-html="statusLable()"></span>
                </p>    
            </div>
        </div>  <!-- End row--> 
        
        
        <div  class="row">
            <div class="col-sm-4">
                <label class="label-title">分數</label>
                <p v-show="hasScore" >
                  {{ student.score | formatMoney  }}  
                </p>                      
            </div>
            <div class="col-sm-8">
                <label class="label-title">備註</label>
                <p v-text="student.ps"></p>                      
            </div>
            
        </div>   <!-- End row-->
        
        
    </div>


   
</div>    
</template>

<script>
    export default {
        name: 'ShowStudent', 
        props: {
            student: {
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
            hasScore(){
                if(!this.student) return false;
                return Helper.tryParseInt(this.student.score) > 0;
            }
            
        }, 
        methods: { 
            statusLable(){
              
                return Student.statusLabel(this.student.status);
            }
            
        }
    }
</script>
