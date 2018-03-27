<template>
<div v-if="course" class="show-data">
    <div class="row">
        <div class="col-sm-3">
           <!-- <photo :id="$options.filters.tryParseInt(course.photo_id)"></photo> -->
        </div>  
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-4">
                    <label class="label-title">名稱</label>
                    <p>{{course.fullName}}</p>
                </div>  
                <div class="col-sm-4">
                    <label class="label-title">開課中心</label>  
                    <p> {{ course.center.name }}</p>                          
                </div> 
                <div class="col-sm-4">
                    <label class="label-title">學期</label>
                    <p v-text="course.term.number">                       
                    </p>
                </div>    
            </div> 
            <div class="row">
                <div class="col-sm-4">
                    <label class="label-title">課程編號</label>
                    <p>{{course.number}}</p>
                </div>  
                <div class="col-sm-4">
                    <label class="label-title">課程分類</label>
                    <p  v-text="course.categoryName"></p>
                </div> 
                <div class="col-sm-4">
                    <label class="label-title">教師</label>
                    <p v-text="course.teacherNames"></p>
                </div>    
            </div>   
                
            
            <div class="row">
                <div class="col-sm-12">
                    <label class="label-title">課程簡介</label>
                    <p>{{  course.description }}</p>
                </div>  
                    
            </div> 
            <div class="row">
                <div class="col-sm-4">
                    <label class="label-title">課程日期</label>
                    <p>
                         {{ course.beginDate }}  ~  {{ course.endDate }}
                    </p>
                </div>  
                <div  class="col-sm-4">
                    <label  class="label-title">週數</label>
                    <p>{{  course.weeks }}</p>

                </div> 
                <div class="col-sm-4">
                    <label class="label-title">時數</label>
                    <p>{{  course.hours }}</p>
                </div>    
            </div> 
            
            <div class="row">
                <div class="col-sm-4">
                    <label class="label-title">狀態</label>
                    <p v-html="$options.filters.courseActiveLabel(course.active)">                       
                    </p>
                </div>  
                <div  class="col-sm-4">
                    <label class="label-title">資料審核</label>
                    <p>
                        <span v-html="$options.filters.reviewedLabel(course.reviewed)"></span>
                        &nbsp;
                        <button class="btn btn-primary btn-xs" @click.prevent="editReview">
                            <i class="fa fa-edit"></i>
                        </button>
                    </p> 

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
        name: 'ShowCourse', 
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
            
            hasReviewedBy(){
                if(!this.course) return false;
                if(!this.course.reviewedBy) return false;
                return true;
            }
        }, 
        methods: { 
            editReview(){
                this.$emit('edit-review')
            }
            
        }
    }
</script>
