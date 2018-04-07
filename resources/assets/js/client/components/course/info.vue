<template>
    <div class="course-info">
        
        <div class="columns">
            <div class="column">
                課程名稱：{{ course.fullName }}
            </div>
            <div class="column">
                課程編號：{{ course.number }}
            </div>
            
        </div>
        <div class="columns">
            <div class="column">
                上課地點：{{ course.center.name }}
                ( {{ course.center.contactInfo.address.fullText  }} )        
            </div>
            <div class="column">
               
                洽詢電話： <span v-if="course.center.courseTel">{{ course.center.courseTel }}</span>
                         <span v-else>{{ course.center.contactInfo.tel }}</span>
                
            </div>
          
        </div>
        <div class="columns">
            <div class="column">
                 上課時間：<span  v-html="$options.filters.classTimesHtml(course)"></span>           
            </div>
            <div class="column">
                 開課日期：{{ course.beginDate }}
            </div>
          
        </div>
        <div class="columns">
            <div class="column">                          
                 課程費用：{{ course.tuition | formatMoney }}      
            </div>
            <div v-if="course.hours" class="column">
                 課程時數：{{  course.hours }} 小時&nbsp;
                 <span v-if="course.weeks">( {{  course.weeks }} 週 )</span>             
            </div>
            
            
        </div>
        <div v-if="false"  class="columns">
            
            <div class="column">                          
                 學分數：{{ course.credit_count }}       
            </div>
            
        </div>
        <div class="columns">
            <div v-if=" course.cost" class="column">                          
                 材料費：{{ course.cost | formatMoney }}   
            </div>
            <div class="column">
                 招生對象：<span v-if="course.target">{{  course.target }}</span>  
                <span v-else> 有興趣者皆可報名 </span>
            </div>
          
        </div>
        <div v-if="details" class="columns">
            <div v-if="false" class="column">
                 報名期間：{{ course.open_date }} 起至 {{ course.close_date }} 止&nbsp;&nbsp;
                 <span v-if="course.canJoin" class="tag is-success">招生中</span>          
            </div>
            
            <div v-show="course.limit" class="column">
                 招生人數：{{ course.limit }}人為原則
                 <span v-show="course.min" > (未達 {{course.min}}人得不開班)</span>
            </div>
          
        </div>
    </div>  <!--  End Course-Info -->
</template>

<script>
    export default {
        name:'CourseInfo',
        props: {
            course:{
                type: Object,
                default: {}
            },
            details:{
                type: Boolean,
                default: true
            },
        },
        methods: {
            formatLocation(course){
                return '--';
            },
           
            
            

        },
      
      
  }


</script>

<style>
.course-info {
    font-size:17px;
}
    
</style>