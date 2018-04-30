<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        
                        <th style="width:3%" v-if="canCheck">
                            <check-box v-show="dataCounts" :value="0" :default="checkAll"
							 @selected="onCheckAll" @unselected="unCheckAll">
							</check-box>
                        </th>
                        
                        <th style="width:15%">課程</th>
                        <th style="width:10%">課堂日期</th>
                        <th style="width:10%">上課時間</th>
                        <th style="width:10%">時數</th>
                        <th>授課教師</th>
                        <th style="width:15%">教育志工</th>
                        <th style="width:10%">學員人數</th>
                        
                    </tr>
                    
                </thead>
                <tbody>
                    <tr v-for="(lesson,index) in getViewList()" :key="index">
                        <td v-if="canCheck">
							<check-box :value="lesson.id" :default="beenChecked(lesson.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                      
                        <td> 
                            <a  href="#" @click.prevent="onSelected(lesson.id)" v-text="lesson.course.fullName"> </a> 
                         
                        </td>
                        <td> 
                            {{ lesson.date }} 
                        </td>
                        <td>
                             {{ lesson.on }} ~ {{ lesson.off }}
                        </td>
                        <td> {{ lesson.hours }} </td>

                        
                        <td v-html="teacherNames(lesson)">
                           
                        </td>
                        <td v-html="volunteerNames(lesson)" > 
                             
                        </td>
                        <td>
                            {{ lesson.studentCount }} 
                        </td>
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
    name:'LessonTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        lessons:{
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
	},
	data() {
		return {
           
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
			return this.lessons;
        },
        courseNames(lesson){
            return '';
        },
        teacherNames(lesson){
            return Lesson.teacherNames(lesson);
        },
        volunteerNames(lesson){
            return Lesson.volunteerNames(lesson);
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
			
			let lessonList = this.getViewList();
			if(!lessonList)  return false;

			lessonList.forEach( lesson => {
				this.onChecked(lesson.id)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
        },
        isTrue(val){
            return Helper.isTrue(val);
        }
        
   }
}
</script>

