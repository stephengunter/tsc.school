<template>
   
    <div>
        <div class="row">
            <div class="col-sm-3" style="margin-top: 3px;"> 
                <h3>請選擇要轉入的課程
                    
                </h3>
            </div>
            <div class="col-sm-4 form-inline" style="margin-top: 20px;">
                
                <div  class="form-group" style="padding-left:1em;">
                    <drop-down :items="centers" :selected="params.center"
                        @selected="onCenterSelected">
                    </drop-down>
                </div>
                
                
            </div>
            <div class="col-sm-3" style="margin-top: 20px;">
                <searcher @search="onSearch">
                </searcher>
            </div>
            <div class="col-sm-2 pull-right" style="margin-top: 20px;">
               
            </div>
            
        </div>

        <hr/>
            
        <table class="table table-striped">
            <thead>
                <tr>
                    
                  
                    <th style="width:12%">編號</th>
                    <th style="width:15%">名稱</th>
                    <th style="width:15%">上課時間</th>
                    <th style="width:20%">課程日期</th>
                    <th style="width:10%">學費</th>
                    <th style="width:10%">材料費</th>
                </tr>
            </thead>
                <tbody>
                    <tr v-for="(course,index) in courses" :key="index">
                       
                       
                       
                        <td> {{ course.number }} </td>
                        <td > 
                            <a v-text="course.fullName" href="#" @click.prevent="onSubmit(course)"></a>
                        </td>
                      
                        <td v-html="$options.filters.classTimesHtml(course)">
                            
                        </td>
                        <td>{{ course.beginDate }}  ~  {{ course.endDate }}</td>
                        <td> {{ course.tuition | formatMoney }}  </td>
                        <td>
                           <span v-if="course.cost" >                            
                               {{ course.cost | formatMoney }} 
                           </span> 
                           <span v-if="course.materials" > 
                           (  {{  course.materials }} )
                           </span>
                        </td>
                    </tr>
                    
                </tbody>
                

            </table>

        
        
        

    </div>

        
        
   
</template>


<script>
    
    
    export default {
        name:'TranCourseSelector',
        props: {
            centers:{
                type:Array,
                default:null
            },
            center_id:{
                type:Number,
                default:0
            },
            student_id:{
                type:Number,
                default:0
            },
            init_courses:{
                type:Array,
                default:null
            },
        },
        data(){
            return {
               


                model:null,
                
                params:{
                    student:0,
                    center:'0',
                    keyword:''
                },

                courses:[],
               
            }
        },
        beforeMount() {
            this.params.center=this.center_id;
            this.params.student=this.student_id;
            if(this.init_courses && this.init_courses.length){
                this.courses=this.init_courses.slice(0);
            }else{
                this.fetchData();
            }
            
          
        },
        computed:{
           
           
            dataCounts(){
            
                return this.courses.length;
            }
           
        }, 
        
        methods:{
            
            onCenterSelected(item){
                this.params.center = item.value;
                this.fetchData();
            },
            onSearch(keyword){
               
				this.params.keyword=keyword;
				this.fetchData();
			},
            fetchData() {
               
                let getData = Tran.courses(this.params);

                getData.then(courses => {

                    this.courses=courses.slice(0);

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onSubmit(course){

                this.$emit('submit', course);
            },
            
            
        }
    }
</script>





