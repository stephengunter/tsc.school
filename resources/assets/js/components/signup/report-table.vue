<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        
                        <th style="width:5%" v-if="canCheck">
                            <check-box v-show="dataCounts" :value="0" :default="checkAll"
							 @selected="onCheckAll" @unselected="unCheckAll">
							</check-box>
                        </th>
                        
                        <th>課程</th>
                        <th style="width:15%">學員人數</th>
                        <th style="width:15%">最低要求</th>
                        <th style="width:15%">上限人數</th>
                        
                        
                    </tr>
                    
                </thead>
                <tbody>
                    <tr v-for="(item,index) in getViewList()" :key="index">
                        <td v-if="canCheck">
							<check-box :value="item.course.id" :default="beenChecked(item.course.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                      
                        <td> {{ item.course.number }} &nbsp;
                             {{ item.course.fullName }} 

                        </td>
                        <td>   
                            <span style="color:red" v-if="item.studentCount<item.course.min">

                                {{ item.studentCount }}   
                            </span>
                            <span v-else>
                                 {{ item.studentCount }}   
                            </span>
                            
                            
                            
                        </td> 
                        <td>   {{ item.course.min }}   </td>
                        <td>   {{ item.course.limit }}   </td>
                       
                       
                    </tr>
                    
                </tbody>
               

            </table>
        </div>
       
            
    </div>
</template>

<script>
export default {
    name:'SignupReportTable',
    props: {
        model: {
            type: Object,
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
            if(!this.can_review) return false;
            return this.can_checked;
        },
        dataCounts(){
            return 5;
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
			return [];
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
			
			let itemList = this.getViewList();
			if(!itemList)  return false;

			itemList.forEach( item => {
				this.onChecked(item.course.id)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
        },
        courseNames(item){
            
            return  Signup.courseNames(item);
        }
        
   }
}
</script>

