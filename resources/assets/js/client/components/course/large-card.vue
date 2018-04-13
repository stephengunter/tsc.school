<template>
    <div v-if="course" class="tile is-ancestor">
        <div class="tile is-vertical">
            <div class="tile">
                <div v-if="course.photo" class="column is-one-quarter">
                    <figure class="image is-2by2">
                        <img :src="course.photo.path">
                    </figure>
                </div>
                
                <div class="tile is-parent is-vertical">
                    <div class="title is-2">
                        {{ course.fullName }}
                        <a href="#" v-if="course.canSignup"  @click.prevent="onSignup" class="button is-info is-outlined  is-focused" style="margin-top: 5px;" >
                            線上報名
                        </a>
                    
                    </div>
                    <div class="title item-title" >
                        上課時間：<span v-html="$options.filters.classTimesHtml(course)"></span> 
                    </div>
                    <div class="title item-title">
                    
                        開課日期：{{ course.beginDate }}
                    </div>
                    <div v-if="course.hours" class="title item-title">
                        
                        課程時數：{{  course.hours }} 小時&nbsp; <span v-if="course.weeks">( {{  course.weeks }} 週 )</span>
                    </div>
                    <div v-if="false" class="title item-title">
                    
                        報名期間：{{  course.openDate }}&nbsp;起至&nbsp;{{ course.closeDate }} &nbsp;止
                    </div>
                    <div class="title item-title">
                        
                        課程費用：{{ course.tuition | formatMoney }} 
                    </div>
                    <div v-if="course.cost" class="title item-title">
                    
                        教材費用：{{ course.cost | formatMoney }} 
                    </div>
                    <div >
                        
                        <p v-if="course.description" class="content" style="font-size:17px;">{{ course.description.trim() }}
                        </p>
                
                    </div>
                </div>
            
            </div>    
        </div>
        
    </div>
</template>

<script>
export default {
    name:'CourseLargeCard',
    props: {
        course:{
            type: Object,
            default: null
        },
    },
    methods:{
        onSignup(){
            this.$emit('signup' , this.course.id);
        }
        
    },
    
    
}
</script>

<style scoped>
.item-title{
    font-size: 20px;
    line-height: 0.9em
}
</style>
