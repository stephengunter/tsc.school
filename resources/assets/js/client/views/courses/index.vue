<template>
    <div v-if="photo" class="columns is-multiline">

        <div v-for="(course,index) in model.viewList" :key="index" class="column is-one-quater-mobile is-half-tablet is-half-desktop">
            <course-card :course="course"
                @selected="onSelected" >
            </course-card>
        </div>
    
    </div>
    <div v-else class="columns is-multiline">

        <div v-for="(course,index) in model.viewList" :key="index" class="column is-one-quater-mobile is-one-quater-tablet is-one-quater-desktop is-one-quater-widescreen is-one-quarter-fullhd">
           <course-card :course="course"
                @selected="onSelected" >
            </course-card>
        </div>
        
    </div>
    
</template>

<script>
import Card from '../../components/course/card.vue'
export default {
    name:'CourseIndexView',
    components:{
       'course-card':Card
    },
    props: {
        model: {
            type: Object,
            default: null
        },
        photo: {
            type: Boolean,
            default: false
        },

    },
    data(){
        return {
           courses:[]
        }
    },
    computed: {
        
    },
    beforeMount(){
        
    },
    mounted(){
        this.$emit('loaded');
    },
    methods:{
        onSelected(id){
            
        }
      
    }
}
</script>




