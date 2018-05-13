<template>
   
    <div>
        <div class="row">
            <div class="col-sm-2" style="margin-top: 3px;"> 
                <h3>選擇課程
                </h3>
            </div>
            <div class="col-sm-5 form-inline" style="margin-top: 20px;">
                
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
                <a @click.prevent="onSubmit" href="#" :disabled="!canSubmit" class="btn btn-success">
                    確定
                </a>
            </div>
            
        </div>

        <hr/>
            
        <table class="table table-striped">
            <thead>
                <tr>
                    
                    <th style="width:3%">
                        
                    </th>
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
                        <td>
							<check-box :value="course.id" :default="beenChecked(course.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                       
                       
                        <td> {{ course.number }} </td>
                        <td v-text="course.fullName"> 
                            
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
        name:'SignupCourseSelector',
        props: {
            centers:{
                type:Array,
                default:null
            },
            center_id:{
                type:Number,
                default:0
            }
        },
        data(){
            return {
               


                model:null,
                
                params:{
                   
                    center:'0',
                    keyword:''
                },

                courses:[],

                checked_ids:[],
                checkAll: false,
               
            }
        },
        beforeMount() {
           this.params.center=this.center_id;
           this.fetchData();
          
        },
        computed:{
           
            canSubmit(){
                return this.checked_ids.length > 0;
            },
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
                
                let getData = Signup.courses(this.params);

                getData.then(courses => {

                    this.courses=courses.slice(0);

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onChecked(id){
				
			    if(!this.beenChecked(id))  this.checked_ids.push(id);
            },
            beenChecked(id){
                return this.checked_ids.includes(id);
            },
            unChecked(id){
                    
                let index= this.checked_ids.indexOf(id);
                if(index >= 0)  this.checked_ids.splice(index, 1); 
                    
            },
            onCheckAll(){
                this.checkAll=true;
                
                let courseList = this.courses;
                if(!courseList)  return false;

                courseList.forEach( course => {
                    this.onChecked(course.id)
                });
            },
            unCheckAll(){
                this.checkAll=false;
                this.checked_ids=[];
            },
            onCheckIdsChanged(ids){
                this.checked_ids=ids.slice(0);
            },
            onSubmit(){
                
                if(!this.checked_ids.length) return;

                let courses=this.courses.filter(course=>{
                    return this.checked_ids.includes(course.id);
                })

                this.$emit('submit', courses);
            },
            
            
        }
    }
</script>





