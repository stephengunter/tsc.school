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
                        <th>地址</th>
                        
                    </tr>
                </thead>
                <tbody v-if="hasData">
                    <tr v-for="(volunteer,index) in getViewList()" :key="index">
                        <td v-if="canCheck">
							<check-box :value="volunteer.userId" :default="beenChecked(volunteer.userId)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                        <td v-if="can_select">
                            <a  href="#" @click.prevent="onSelected(volunteer.userId)" v-text="volunteer.user.profile.fullname"> </a> 
                           
                        </td>
                        <td v-else v-text="volunteer.user.profile.fullname">  </td>

                       
                        <td>{{  volunteer.user.email }}</td>
                        <td>{{  volunteer.user.phone }}</td>
                        <td>
                            <span v-if="hasAddress(volunteer)">
                                {{  volunteer.user.contactInfo.address.fullText }}
                            </span>
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
    name:'VolunteerTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        volunteers:{
            type: Array,
            default: null
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
			return this.volunteers;
        },
        hasContactInfo(volunteer){
            if(volunteer.user.contactInfo) return true;
            return false;
        },
        hasAddress(volunteer){
             
            if(!this.hasContactInfo(volunteer)) return false;
            if(volunteer.user.contactInfo.address) return true;
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
			
			let volunteerList = this.getViewList();
			if(!volunteerList)  return false;

			volunteerList.forEach( volunteer => {
				this.onChecked(volunteer.userId)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
		},
        
   }
}
</script>

