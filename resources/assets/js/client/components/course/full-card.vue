<template>
    <div class="box">
        
        <div class="media">
            <div class="media-left is-hidden-mobile">
                <div v-if="course.canOnlineSignup" class="status">
                    <span class="tag is-success">招生中</span> 
                </div>
                <figure v-if="course.photo"  class="image is-128x128">
                    <a>
                        <img :src="course.photo.path">
                    </a>
                </figure>

            </div>
            <div class="media-content">
               
                <ul class="info">                    
                    <li class="title">
                        <i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; {{ course.center.name }}
                        <a @click.prevent="selected"> 
                            {{ course.fullName}}
                        </a>
                    </li> 
                    <li class="item">上課時間：<span  v-html="$options.filters.classTimesHtml(course)"></span>  
                    </li>
                    <li class="item">開課日期：{{ course.beginDate }}

                    </li>
                    <li v-if="course.hours" class="item">課程時數：{{  course.hours }} 小時
                                    <span v-if="course.weeks">( {{  course.weeks }} 週 )</span>
                    </li>
                    <li class="item">課程費用：{{ course.tuition | formatMoney }}  

                    </li>
                </ul> 
                
            </div>
             
        </div>
    </div>  <!-- end box -->
</template>

<script>
export default {
    name:'CourseFullCard',
    props: {
        course:{
            type: Object,
            default: {}
        },
    },
    data () {
        return {
            
        }
    },
    beforeMount(){
        
    },
}


</script>
   

<style scoped>
.course-center{
    display: flex;
    align-items: center;
    font-size:1.2em;
}
.status{
    text-align:center;
    padding-bottom:10px;
}
.course-title{
    font-size: 1.325em;
    padding-left: 10px;
}
ul.info {
    list-style-type:none;
}
li.title {
    font-size: 1.45em;
    font-weight: normal;
    margin-bottom: 0.5714em;
}
li.item {
    font-size: 1.125em;
    margin-bottom: 0.3714em;
}
  
</style>