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
                        <th style="width:5%">審核</th>
                    </tr>
                    
                </thead>
                <tbody>
                    <tr v-for="(payroll,index) in getViewList()" :key="index">
                        <td v-if="canCheck">
							<check-box :value="payroll.id" :default="beenChecked(payroll.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                      
                        <td> 
                            <a  href="#" @click.prevent="onSelected(payroll.id)" v-text="payroll.course.fullName"> </a> 
                         
                        </td>
                        <td> 
                            {{ payroll.date }} 
                        </td>
                        <td>
                             {{ payroll.timeString }} 
                        </td>
                        <td> {{ payroll.hours }} </td>

                        
                        <td v-html="teacherNames(payroll)">
                           
                        </td>
                        <td v-html="volunteerNames(payroll)" > 
                             
                        </td>
                        <td>
                            {{ payroll.studentCount }} 
                        </td>
                        <td v-html="$options.filters.reviewedLabel(payroll.reviewed)" ></td>
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
    name:'PayrollTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        payrolls:{
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
			return this.payrolls;
        },
        teacherNames(payroll){
            return Payroll.teacherNames(payroll);
        },
        volunteerNames(payroll){
            return Payroll.volunteerNames(payroll);
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
			
			let payrollList = this.getViewList();
			if(!payrollList)  return false;

			payrollList.forEach( payroll => {
				this.onChecked(payroll.id)
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

