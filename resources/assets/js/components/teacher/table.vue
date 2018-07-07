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
                        <th style="width:10%">姓名</th>
                        <th style="width:20%">Email</th>
                        <th style="width:10%">手機</th>
                        
                        <th v-if="false" style="width:10%">專長</th>
                        <th style="width:10%" v-if="!center">所屬中心</th>
                        <th v-if="false" style="width:10%">薪酬標準</th>
                        <th v-if="false" style="width:10%">特殊講師鐘點費</th>
                        <th style="width:10%">審核</th>
                        
                    </tr>
                </thead>
                <tbody v-if="hasData">
                    <tr v-for="(teacher,index) in getViewList()" :key="index">
                        <td v-if="canCheck">
							<check-box :value="teacher.userId" :default="beenChecked(teacher.userId)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                        <td v-if="can_select">
                            <a  href="#" @click.prevent="onSelected(teacher.userId)" v-text="teacher.user.profile.fullname"> </a> 
                           
                        </td>
                        <td v-else v-text="teacher.user.profile.fullname">  </td>

                       
                        <td>{{  teacher.user.email }}</td>
                        <td>{{  teacher.user.phone }}</td>
                        


                        <td v-if="false">{{  teacher.specialty }}</td>

                        <td v-if="!center" v-text="centerNames(teacher)"></td>

                        <td v-if="false">{{  teacher.wage.name }}</td>

                        <td v-if="false">{{  teacher.pay | formatMoney }}</td>
                        
                        <td v-html="$options.filters.reviewedLabel(teacher.reviewed)" ></td>
                        
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
    name:'TeacherTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        teachers:{
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
	},
	data() {
		return {
			checked_ids:[],
			checkAll: false,
		};
	},
	computed:{
        hasData(){
            let list=this.getViewList();
            if(!list) return false;
            return list.length > 0;
        },
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
			return this.teachers;
        },
        centerNames(teacher){
            if(teacher.centerNames) return teacher.centerNames;
            return teacher.centers;
        },
        hasContactInfo(teacher){
            if(teacher.user.contactInfo) return true;
            return false;
        },
        hasAddress(teacher){
             
            if(!this.hasContactInfo(teacher)) return false;
            if(teacher.user.contactInfo.address) return true;
            return false;
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
			
			let teacherList = this.getViewList();
			if(!teacherList)  return false;

			teacherList.forEach( teacher => {
				this.onChecked(teacher.userId)
			});
		},
		unCheckAll(){
            
			this.checkAll=false;
			this.checked_ids=[];
		},
        
   }
}
</script>

