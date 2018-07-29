<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr v-if="activeIndex==0">
                        
                        <th style="width:3%" v-if="canCheck">
                            <check-box v-show="dataCounts" :value="0" :default="checkAll"
							 @selected="onCheckAll" @unselected="unCheckAll">
							</check-box>
                        </th>
                        <th v-if="!center" style="width:8%">開課中心</th>
                        <th style="width:12%">編號</th>
                        <th style="width:15%">名稱</th>


                        <th v-if="show_categories" style="width:10%">課程分類</th> 
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
                        <th style="width:3%" v-if="canCheck">
                            <check-box v-show="dataCounts" :value="0" :default="checkAll"
							 @selected="onCheckAll" @unselected="unCheckAll">
							</check-box>
                        </th>
                        <th v-if="!center"  style="width:8%">開課中心</th>
                        <th style="width:12%">編號</th>
                        <th style="width:15%">名稱</th>


                        <th style="width:10%" v-if="show_teachers">教師</th> 
                        <th style="width:5%">週數</th>
                        <th style="width:5%">時數</th>
                        <th style="width:8%">學費</th>
                        <th style="width:8%">優惠價</th>
                        <th>教材費</th>
                        <th style="width:8%">人數上限</th>
                        <th style="width:8%">最低人數</th>

                        
                    </tr>
                </thead>
                <tbody v-if="activeIndex==0">
                    <tr v-for="(course,index) in getViewList()" :key="index">
                        <td v-if="canCheck">
							<check-box :value="course.id" :default="beenChecked(course.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                       
                        <td v-if="!center" > {{ course.center.name }}  </td>
                        <td> {{ course.number }} </td>
                        <td> 
                            <a v-if="can_select" href="#" @click.prevent="onSelected(course.id)" v-text="course.fullName"> </a>
                            <span v-else v-text="course.fullName"></span>
                        </td>
                        <td v-if="show_categories" > {{ course.categoryName }} </td>
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
                        <td v-if="canCheck">
							<check-box :value="course.id" :default="beenChecked(course.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                        <td v-if="!center" > {{ course.center.name }} </td>
                        <td> {{ course.number }}  </td>
                        <td> 
                            <a v-if="can_select" href="#" @click.prevent="onSelected(course.id)" v-text="course.fullName"> </a>
                            <span v-else v-text="course.fullName"></span>
                        </td>
                        <td v-if="show_teachers"> {{ course.teacherNames }}   </td>
                        <td> {{ course.weeks }} </td>
                        <td> {{ course.hours }}   </td>


                        <td> {{ course.tuition | formatMoney }}  </td>
                        <td> <i v-if="isTrue(course.discount)" class="fa fa-check-circle" style="color:green"></i>  </td>
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
        </div>
        <slot name="table-footer"> 
        
        </slot> 
            
    </div>
</template>

<script>
export default {
    name:'CourseTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        courses:{
            type: Array,
            default: null
        },
        can_review:{
            type: Boolean,
            default: false
        },
        can_select:{
            type: Boolean,
            default: true
        },
        can_checked:{
            type: Boolean,
            default: false
        },
        center: {
            type: Boolean,
            default: false
        },
        show_teachers: {
            type: Boolean,
            default: true
        },
        show_categories: {
            type: Boolean,
            default: true
        },
	},
	data() {
		return {
            activeIndex:0,
			checked_ids:[],
            checkAll: false,
            
		};
	},
	computed:{
		canCheck(){
           
            if(this.can_review) return true;
            return this.can_checked;
        },
        dataCounts(){
            
            let viewList=this.getViewList();
            if(!viewList) return 0;
            return viewList.length;
        }
		
    }, 
	watch: {
		checked_ids() {
			this.$emit('check-changed',this.checked_ids);
		}
	},
    methods:{
        getViewList(){
			if(this.model) return this.model.viewList;
			return this.courses;
        },
        isTrue(val){
            return Helper.isTrue(val);
        },
        onSelected(id){
           this.$emit('selected',id);
        },
        beenChecked(id){
            return this.checked_ids.includes(id);
		},
        onChecked(id){
				
			if(!this.beenChecked(id))  this.checked_ids.push(id);
		},
		unChecked(id){
				
			let index= this.checked_ids.indexOf(id);
			if(index >= 0)  this.checked_ids.splice(index, 1); 
				
		},
		onCheckAll(){
			this.checkAll=true;
			
			let courseList = this.getViewList();
			if(!courseList)  return false;

			courseList.forEach( course => {
				this.onChecked(course.id)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
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
        }
        
   }
}
</script>

