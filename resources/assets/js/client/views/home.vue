<template>
    <div>

        <div v-if="hasNotices">
            <h1 class="title">公告訊息</h1>
            <notice-table :model="notices_model" @selected="onNoticeSelected" >
            </notice-table> 

            <div style="clear: both;text-align:right;">
                <a href="/notices" class="button is-primary is-outlined">
                    <span class="icon is-small">
                    <i class="fa fa-angle-double-right"></i>
                    </span>
                    <span>更多訊息</span>
                </a>
            </div>
        </div>
        <div v-if="hasLatestCourses" style="margin-top:1em;" >
            <h1 class="title">最新課程</h1>
            <div class="columns is-multiline">

                <div v-for="(course,index) in latest_courses" :key="index" class="column is-one-quater-mobile is-half-tablet is-half-desktop">
                    <course-full-card :course="course"></course-full-card>
                </div>
            
            </div>

        </div>

        <div v-if="hasRecommendCourses" style="margin-top:1em;" >
            <h1 class="title">推薦課程</h1>
            <div class="columns is-multiline">

                <div v-for="(course,index) in recommend_courses" :key="index" class="column is-one-quater-mobile is-half-tablet is-half-desktop">
                    <course-full-card :course="course"></course-full-card>
                </div>
            
            </div>

        </div>

    </div>
</template>

<script>
import NoticeTable from '../components/notice/table.vue';
import CourseFullCard from '../components/course/full-card.vue'
export default {
    name:'HomeView',
    components: {
        'notice-table':NoticeTable,
        'course-full-card':CourseFullCard
    },
    props: {
        notices_model:{
            type: Object,
            default: null
        },
        latest_courses:{
            type: Array,
            default: null
        },
        recommend_courses:{
            type: Array,
            default: null
        },
    },
    computed:{
        hasNotices(){
           if(!this.notices_model) return false;
           if(!this.notices_model.viewList) return false;
           return this.notices_model.viewList.length > 0;
           
        },
        hasLatestCourses(){
           if(!this.latest_courses) return false;
           return this.latest_courses.length > 0;
           
        },
        hasRecommendCourses(){
           if(!this.recommend_courses) return false;
           return this.recommend_courses.length > 0;
           
        },
        showImportBtn(){
            if(!this.can_import) return false;
            return !this.canReview;
        }
        
    }, 
    mounted(){
        this.$emit('loaded');
    },
    methods:{
        init(){
          
        },
        onNoticeSelected(id){
           window.location='/notices/' + id;
        }
    },
}
</script>

