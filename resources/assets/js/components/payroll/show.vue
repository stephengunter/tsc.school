<template>
<div v-if="payroll">
    <div  class="show-data">
        <div class="row" >
            <div  class="col-sm-3">
                <label class="label-title">開課中心</label>
                <p>
                    {{ payroll.center.name }}     
                </p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">教師姓名</label>
                <p>
                  {{ payroll.user.profile.fullname }}  
                </p>                   
            </div>
            
            <div class="col-sm-3">
                <label class="label-title">審核</label>
                 
                <p>
                    <span v-html="$options.filters.reviewedLabel(payroll.reviewed)"></span>
                    &nbsp;
                    <button class="btn btn-primary btn-xs" @click.prevent="editReview">
                        <i class="fa fa-edit"></i>
                    </button>
                </p> 
            </div>
            <div  class="col-sm-3">
                <label class="label-title">狀態</label>
                   
                <p>
                    <span v-html="$options.filters.payrollStatusLabel(payroll.status)"></span>
                    &nbsp;
                    <button class="btn btn-primary btn-xs" @click.prevent="editStatus">
                        <i class="fa fa-edit"></i>
                    </button>
                </p>                  
            </div>
        </div>  <!-- End row--> 
        <div class="row" >
            <div  class="col-sm-3">
                <label class="label-title">月份</label>
                <p>
                    {{ payroll.monthString }} 
                </p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">薪酬標準</label>
                <p>
                     {{ payroll.wageName }} 
                </p>    
            </div>
            <div class="col-sm-3">
                <label class="label-title">鐘點費金額</label>
                <p>
                   {{ payroll.amount | formatMoney }} 
                </p>                   
            </div>
            
            
        </div>  <!-- End row--> 
        <div class="row" >
            
            <div class="col-sm-12">
                <label class="label-title">備註</label>
                 
                <p>
                    {{  payroll.ps }}
                     &nbsp;
                    <button class="btn btn-primary btn-xs" @click.prevent="editPS">
                        <i class="fa fa-edit"></i>
                    </button>
                </p> 
            </div>
        </div>  <!-- End row--> 
    </div>
    
    
   
</div>    
</template>

<script>
    export default {
        name: 'ShowPayroll', 
        props: {
            payroll: {
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
                if(!this.payroll) return false;
                if(!this.payroll.reviewedBy) return false;
                return true;
            }
        }, 
        methods: { 
            editReview(){
                this.$emit('edit-review')
            },
            isTrue(val){
                return Helper.isTrue(val);
            },
            editReview(){
                this.$emit('edit-review')
            },
            editStatus(){
                this.$emit('edit-status')
            },
            editPS(){
                this.$emit('edit-ps')
            },
            
        }
    }
</script>
