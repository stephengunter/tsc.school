<template>
    <div>
        <large-card :course="model" @signup="onSignup"></large-card>
        <div class="back-btn" style="clear: both; text-align: right;" >    
            <a @click.prevent="onBack"  class="button is-primary is-outlined">
                <span class="icon is-small">
                    <i class="fa fa-angle-double-left"></i>
                </span>
                <span>返回</span>
            </a>
        </div>

        <h1 class="title" >課程資訊</h1>
        <course-info :course="model"></course-info>

        <div v-if="cautions.length" style="padding-top:1.2em">
            <h1 class="title" >注意事項</h1>
            <ul class="course-info"  style="list-style-type: disc;margin-left: 0; padding-left: 20px;">
                <li v-for="(item,index) in cautions" :key="index" v-text="item">
                  
                </li>
                
            </ul>
        </div>
        <div v-if="false" style="padding-top:1.2em">
            <h1 class="title" >優惠辦法</h1>
            <discount-view :center="model.center" :bird_date_text="model.term.birdDate" ></discount-view>
        </div>
        <div style="padding-top:1.2em">
            <h1 class="title" >師資介紹</h1>
            <teacher-card v-for="(teacher,index) in model.teachers" :key="index" :teacher="teacher"></teacher-card>
        </div>

        <div v-if="model.processes.length" style="padding-top:1.2em">
            <h1 class="title">課程大綱</h1>
            <processes :processes="model.processes"></processes>
        </div>
    </div>

</template>

<script>
    import LargeCard from '../../components/course/large-card.vue';
    import CourseInfo from '../../components/course/info.vue';
    import TeacherCard from '../../components/course/teacher-card.vue';
    import CourseProcesses from '../../components/course/processes.vue';
    import DiscountView from '../../components/discount/view.vue';
    export default {
        name:'CourseDetailsView',
        components:{
            'large-card':LargeCard,
            'course-info':CourseInfo,
            'teacher-card':TeacherCard,
            'processes':CourseProcesses,
            'discount-view':DiscountView
        },
        props: {
            init_model: {
                type: Object,
                default: null
            }
        },
        beforeMount(){
            if(this.init_model){
                this.model={...this.init_model };
                
            }  

        },
        mounted(){
            this.$emit('loaded');
        },
        data(){
            return{
                model:null,
            }
        },
        computed: {
            cautions(){
                if(!this.model) return [];
                if(!this.model.caution) return [];
                return this.model.caution.split('\n');
            }
        },
        methods:{
            onBack(){
                window.history.back();
            },
            onSignup(id){
                let url = `/signups/create?course=${id}`;
                window.location = url;
            }
            
        },
        
    }
</script>