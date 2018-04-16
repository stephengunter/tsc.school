<template>
   
   <table  v-if="model"  class="table">
       <thead>
            <tr v-if="activeIndex==0">
                <th style="width:8%">開課中心</th>
                <th style="width:12%">編號</th>
                <th style="width:15%">名稱</th>
                <th style="width:15%">上課時間</th>
                <th style="width:20%">課程日期</th>
                <th style="width:5%">審核</th>
                <th style="width:5%">狀態</th>
                <th style="width:3%">
                    <a @click.prevent="activeIndex+=1" href="#" class="btn btn-xs btn-default">
                        <i class="fa fa-step-forward"></i>
                    </a>
                </th>
            </tr>
            <tr v-if="activeIndex==1">
                <th style="width:3%">
                    <a @click.prevent="activeIndex-=1" href="#" class="btn btn-xs btn-default">
                        <i class="fa fa-step-backward"></i>
                    </a>
                </th>
                <th style="width:8%">開課中心</th>
                <th style="width:12%">編號</th>
                <th style="width:15%">名稱</th>
               
                <th style="width:5%">週數</th>
                <th style="width:5%">時數</th>
                <th style="width:8%">學費</th>
                <th>材料費</th>
                <th style="width:8%">人數上限</th>
                <th style="width:8%">最低人數</th>

                
            </tr>
        </thead>
        <tbody v-if="activeIndex==0">
            <tr v-for="(course,index) in getViewList()" :key="index">
                
                <td> {{ course.center.name }}  </td>
                <td> {{ course.number }} </td>
                <td v-text="course.fullName"> 
                   
                </td>
               
                <td v-html="$options.filters.classTimesHtml(course)">
                    
                </td>
                <td>{{ course.beginDate }}  ~  {{ course.endDate }}</td>
                <td v-html="$options.filters.reviewedLabel(course.reviewed)" ></td>
                <td v-html="$options.filters.courseActiveLabel(course.active)" ></td>
                <td></td>
            </tr>
            
        </tbody>
        <tbody v-if="activeIndex==1">
            
            <tr v-for="(course,index) in getViewList()" :key="index">
                <td></td>
                <td> {{ course.center.name }} </td>
                <td> {{ course.number }}  </td>
                <td> 
                    <a href="#" @click.prevent="onSelected(course.id)" v-text="course.fullName"> </a>
                  
                </td>
             
                <td> {{ course.weeks }} </td>
                <td> {{ course.hours }}   </td>


                <td> {{ course.tuition | formatMoney }}  </td>
                <td>
                    <span v-if="course.cost" >                            
                        {{ course.cost | formatMoney }} 
                    </span> 
                    <span v-if="course.materials" > 
                    (  {{  course.materials }} )
                    </span>
                </td>
                <td>{{  course.limit }}</td>
                
                <td>{{  course.min }}</td>
            </tr>
            
        </tbody>
   </table>

        
        
   
</template>


<script>
    export default {
        name:'TeacherCourses',
        props: {
            model: {
                type: Object,
                default: null
            },
            courses:{
                type: Array,
                default: null
            },
        },
        data(){
            return {
                activeIndex:0,
                
            }
        },
        watch: {
            
	    },
        beforeMount() {
            
            	
        },
        computed:{
            dataCounts(){
            
                let viewList=this.getViewList();
                if(!viewList) return 0;
                return viewList.length;
            }
           
        }, 
        methods:{
            getViewList(){
                if(this.model) return this.model.courses;
                return this.courses;
            },
            classTimes(course){
                if(course.classTimes && course.classTimes.length)
                {
                    let html='';
                    course.classTimes.forEach((item)=>{
                        
                        html += `${item.weekday.title}&nbsp;${item.on} ~ ${item.off}`;
                        
                    });
                    return html;
                    
                }
                return '';
            },
            onSelected(id){
                alert(id);
                this.$emit('selected',id);
            },
            
        }
    }
</script>





