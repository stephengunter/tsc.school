<template>
    <div class="card"> 
        <div class="card-content">
            <div class="media">
                <div v-if="course.photo" class="media-left is-hidden-mobile">
                    <figure class="image is-128x128">
                        <img :src="course.photo.path">
                    </figure>
                </div>
                <div class="media-content">
                    <div>
                        <ul class="info">                    
                            <li class="title-item">
                                <span  class="tag is-success">招生中</span> 
                                <a :href="detailsUrl" > 
                                    {{ course.fullName}}
                                </a>
                            </li> 
                            <li class="item">上課時間：<span  v-html="$options.filters.classTimesHtml(course)"></span>    </li>
                            <li class="item">開課日期：{{ course.beginDate }}</li>
                            <li v-if="course.hours" class="item">
                                課程時數：{{  course.hours }} 小時
                                <span v-if="course.weeks">( {{  course.weeks }} 週 )</span>
                            </li>
                            <li class="item">課程費用：{{ course.tuition | formatMoney }}  </li>
                        </ul> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name:'CourseCard',
    props: {
        course:{
            type: Object,
            default: null
        },
    },
    data () {
        return {
           detailsUrl:'',
        }
    },
    beforeMount(){
        this.detailsUrl='/courses/' + this.course.id;
    },
    methods:{
        onSelected(){
            this.$emit('selected',this.course.id)
        }
    }
}
</script>


<style scoped>
.card{
    width: 360px;
}
.status{
    text-align:center;
    padding-bottom:10px;
}
.course-title{
    font-size: 1.125em;
    padding-left: 10px;
}
ul.info {
    list-style-type:none;
}
li.title-item {
    font-size: 1.45em;
    font-weight: normal;
    margin-bottom: 0.5714em;
}
li.item {
    font-size: 1.125em;
    margin-bottom: 0.3714em;

}
  
</style>