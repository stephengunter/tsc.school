<template>
<div v-if="teacher">
    <div v-if="teacher.group" class="show-data">
        <div class="row" >
            <div  class="col-sm-4">
                <label class="label-title">名稱</label>
                <p v-text="teacher.name"></p>                      
            </div>
            <div class="col-sm-4">
                <label class="label-title">所屬中心</label>
                <p v-text="teacher.center.name">
                
                </p>                   
            </div>
            <div v-if="false" class="col-sm-4">
                <label class="label-title">狀態</label>
                <p>
                    <span v-html="$options.filters.activeLabel(teacher.active)"></span>
                    
                </p>    
            </div>
        </div>  <!-- End row--> 
        <div class="row" >
            <div  class="col-sm-12">
                <label class="label-title">簡介</label>
                <p v-html="teacher.description"></p>                     
            </div>
        </div>  <!-- End row--> 
        <div class="row" >
            <div  class="col-sm-12">
                <label class="label-title">備註</label>
                <p v-text="teacher.ps"></p>                     
            </div>
        </div>  <!-- End row--> 
    </div>
    <div v-else class="show-data">
        <div class="row" >
            <div  class="col-sm-4">
                <label class="label-title">姓名</label>
                <p v-text="teacher.user.profile.fullname"></p>                      
            </div>
            <div class="col-sm-4">
                <label class="label-title">所屬中心</label>
                <p v-text="centerNames(teacher)">
                 
                </p>                   
            </div>
            <div class="col-sm-4">
                <label class="label-title">資料審核</label>
                <p>
                    <span v-html="$options.filters.reviewedLabel(teacher.reviewed)"></span>
                    &nbsp;
                    <button class="btn btn-primary btn-xs" @click.prevent="editReview">
                        <i class="fa fa-edit"></i>
                    </button>
                </p>    
            </div>
        </div>  <!-- End row--> 
        
        
        <div  class="row">
            <div class="col-sm-4">
                <label class="label-title">專長</label>
                <p v-text="teacher.specialty"></p>                      
            </div>
            <div class="col-sm-4">
                <label class="label-title">最高學歷</label>
                <p v-text="teacher.education"></p>                      
            </div>
            
        </div>   <!-- End row-->
        <div  class="row">
            <div class="col-sm-4">
                <label class="label-title">經歷</label>
                <p v-html="teacher.experiences"></p>                     
            </div>
            <div class="col-sm-8">
                <label class="label-title">個人簡介</label>
                <p v-html="teacher.description"></p> 
            </div>
            
                
        </div>   <!-- End row-->
        <div class="row">
            <div class="col-sm-4">
                <label class="label-title">鐘點費</label>
                <p>{{ teacher.wage | formatMoney }}</p>           
            </div>
            <div class="col-sm-4">
                <label class="label-title">銀行帳號</label> 
                 <p>{{ teacher.account  }}</p>        
            </div>
        </div>  
        <div class="row">
            <div class="col-sm-8">
                <label class="label-title">備註</label>
                <p>{{ teacher.ps }}</p>           
            </div>
            
        </div>   
    </div>


   
</div>    
</template>

<script>
    export default {
        name: 'ShowTeacher', 
        props: {
            teacher: {
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
                if(!this.teacher) return false;
                if(!this.teacher.reviewedBy) return false;
                return true;
            }
        }, 
        methods: { 
            centerNames(teacher){
              
                if(teacher.centerNames) return teacher.centerNames;
                return teacher.centers;
            },
            editReview(){
                this.$emit('edit-review')
            }
            
        }
    }
</script>
