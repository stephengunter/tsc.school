<template>
   
    <div v-if="teacher" class="box">
        <div class="columns">
            <div v-if="teacher.photo" class="column is-2">
                <figure class="image is-128x128">
                    <img :src="teacher.photo.path">                   
                </figure>
            </div>
            <div class="column is-3">
                <h1 class="title">{{ teacher.user.profile.fullname }}</h1>
                <p>
                    <ul style="list-style-type: disc;margin-left: 0; padding-left: 20px;">
                        <li v-if="item" v-for="(item,index) in experiences" :key="index"  style="font-size: 16px;" v-text="item"></li>
                    </ul>
                </p>
            </div>
            <div class="column">
                <p style="font-size: 18px;">
                   {{ teacher.description }}
                </p>
           
            </div>
        </div>
    </div> <!--  end box -->
       
 
</template>

<script>
    export default {
        name:'CourseTeacherCard',
        props: {
            teacher:{
                type: Object,
                default: null
            },
        },
        computed: {
            experiences(){
                if(!this.teacher) return [];
                if(!this.teacher.experiences) return [];
                return this.teacher.experiences.split('<br>');
            }
        },

    }
</script>


<style>
  .ul-experiences{
    list-style-type: disc;
    margin-left: 0; 
    padding-left: 20px;
    font-size: 16px;
  }
</style>

