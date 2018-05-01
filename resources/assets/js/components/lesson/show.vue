<template>
<div v-if="lesson">
    <div  class="show-data">
        <div class="row" >
            <div  class="col-sm-3">
                <label class="label-title">課程</label>
                <p v-text="lesson.course.fullName"></p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">課堂日期</label>
                <p>
                    {{ lesson.date }}
                </p>                   
            </div>
            <div class="col-sm-3">
                <label class="label-title">上課時間</label>
                <p>
                     {{ lesson.timeString }} 
                </p>    
            </div>
            <div class="col-sm-1">
                <label class="label-title">時數</label>
                
                <p>
                   {{ lesson.hours }} 
                </p> 
               
            </div>
            <div class="col-sm-2">
                <label class="label-title">審核</label>
                 
                <p>
                    <span v-html="$options.filters.reviewedLabel(lesson.reviewed)"></span>
                    &nbsp;
                    <button class="btn btn-primary btn-xs" @click.prevent="editReview">
                        <i class="fa fa-edit"></i>
                    </button>
                </p> 
            </div>
        </div>  <!-- End row--> 
        <div class="row" >
            <div  class="col-sm-3">
                <label class="label-title">授課教師</label>
                <p v-html="teacherNames(lesson)"></p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">教育志工</label>
                <p v-html="volunteerNames(lesson)" >
                   
                </p>                   
            </div>
            <div class="col-sm-6">
                <label class="label-title">學員出席狀況</label>
                <p v-html="attendSummary(lesson)" >
                   
                </p> 
            </div>
            
        </div>  <!-- End row--> 
        <div class="row" >
            <div class="col-sm-6">
                <label class="label-title">課堂內容</label>
                 
                <p>
                    {{  lesson.content }}
                </p> 
            </div>
            <div class="col-sm-6">
                <label class="label-title">備註</label>
                 
                <p>
                    {{  lesson.ps }}
                </p> 
            </div>
        </div>  <!-- End row--> 
    </div>
    
    
   
</div>    
</template>

<script>
    export default {
        name: 'ShowLesson', 
        props: {
            lesson: {
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
                if(!this.lesson) return false;
                if(!this.lesson.reviewedBy) return false;
                return true;
            }
        }, 
        methods: { 
            teacherNames(lesson){
                return Lesson.teacherNames(lesson);
            },
            volunteerNames(lesson){
                return Lesson.volunteerNames(lesson);
            },
            attendSummary(lesson){
                let all=lesson.studentCount;
                let actual=lesson.studentAttended;
                return `應到： ${all} 實到：${actual}`;

            },
            editReview(){
                this.$emit('edit-review')
            },
            isTrue(val){
                return Helper.isTrue(val);
            },
            editReview(){
                this.$emit('edit-review')
            },
            
        }
    }
</script>
