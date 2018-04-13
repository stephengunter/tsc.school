<template>
<div v-if="signup">
    <div>
        
        <div class="columns">
            <div class="column" >
                
                <table class="table is-striped">
                    <thead>
                        <tr style="font-size:15px">
                            <th>課程編號</th> 
                            <th>課程名稱</th> 
                            <th>課程費用</th>
                            <th>教材費用</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item,index) in signup.details" :key="index">
                            <td>{{ item.course.number }} </td>
                            <td>{{ item.course.fullName }} </td>
                            <td>{{  item.course.tuition | formatMoney }}   </td>
                            <td>{{  item.course.cost | formatMoney}}   </td>    
                           
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="columns">
            <div class="column" >
                <label class="label label-title">折扣</label>
                <p>
                    {{ signup.discount }} 
                    <span v-if="signup.points">
                        &nbsp; {{ signup.pointsText }}
                    </span>  
                </p>                      
            </div>
            <div class="column" >
                <label class="label label-title">應繳金額</label>
                <p style="color:red;font-size:1.2em">
                    {{ signup.amount | formatMoney }} 
                </p>                   
            </div>
            
        </div>  <!-- End row-->         
    </div>
    
    
   
</div>    
</template>

<script>
    export default {
        name: 'ShowBill', 
        props: {
            signup: {
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
                if(!this.signup) return false;
                if(!this.signup.reviewedBy) return false;
                return true;
            }
        }, 
        methods: { 
            editReview(){
                this.$emit('edit-review')
            },
            isTrue(val){
                return Helper.isTrue(val);
            }
            
        }
    }
</script>
