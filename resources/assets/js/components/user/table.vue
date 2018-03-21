<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:5%" v-if="can_check">
                            <check-box v-show="dataCounts" :value="0" :default="checkAll"
							 @selected="onCheckAll" @unselected="unCheckAll">
							</check-box>
                        </th>
                        <th style="width:10%">姓名</th>
                        <th style="width:10%">身分證號</th>
                        <th >Email</th>
                        <th style="width:10%">手機</th>
                        
                        <th style="width:10%">角色</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user,index) in model.viewList" :key="index">
                        <td v-if="can_check">
							<check-box :value="user.id" :default="beenChecked(user.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                        <td>
                            <a href="#" @click.prevent="onSelected(user.id)" v-text="user.profile.fullname"> </a> 
                        </td>
                        <td>{{  user.profile.sid }}</td>
                        <td>{{  user.email }}</td>
                        <td>{{  user.phone }}</td>
                       
                        <td v-html="roleLabels(user)">
                           
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
    name:'UserTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        can_check: {
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
			return null;
		},
        hasContactInfo(user){
            if(user.contactInfo) return true;
            return false;
        },
        hasAddress(user){
             
            if(!this.hasContactInfo(user)) return false;
            if(user.contactInfo.address) return true;
            return false;
        },
        roleLabels(user){
            return User.roleLabels(user.roles);
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
			
			let userList = this.getViewList();
			if(!userList)  return false;

			userList.forEach( user => {
				this.onChecked(user.id)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
		},
        
   }
}
</script>

