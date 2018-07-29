<template>
<div v-if="course" class="show-data">
    <div class="row">
                
        <div class="col-sm-3">
                                    
            <label class="label-title">人數上限</label>
            <p v-text="course.limit"></p>  
            
        </div>
        <div class="col-sm-3">
            <label class="label-title">最低人數</label>
            <p v-text="course.min"></p>  
        </div>
        <div class="col-sm-3">
             
        </div>
        <div class="col-sm-3">
                                    
            
            
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
                                    
            <label class="label-title">學費</label>
            <p v-html="$options.filters.formatMoney(course.tuition)" ></p> 
             
        </div>
        <div class="col-sm-3">
                                    
            <label class="label-title">優惠價</label>
            <p>
                <i v-if="isTrue(course.discount)" class="fa fa-check-circle" style="color:green"></i>
            </p>
        </div>
        <div class="col-sm-3">
                                
            <label class="label-title">教材費</label>
            <p v-html="$options.filters.formatMoney(course.cost)" ></p>  
            
        </div>
        <div class="col-sm-">
                                
            <label class="label-title">教材</label>
            <p v-html="course.materials"></p>  
            
        </div>
        
    </div>
    
    <div class="row">
        <div class="col-sm-12">
                                    
            <label class="label-title">注意事項</label>
            <p v-html="cautions(course)"></p>  
            
        </div>
    </div>   


   
</div>    
</template>

<script>
    export default {
        name: 'ShowCourseInfo', 
        props: {
            course: {
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
            editReview(){
                this.$emit('edit-review')
            },
            isTrue(val){
                return Helper.isTrue(val);
            },
            cautions(course){
                if(!course.caution) return '';
                return Helper.replaceAll(course.caution,'\n' , '<br>')
					
            }
            
        }
    }
</script>
