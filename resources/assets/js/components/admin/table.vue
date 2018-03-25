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
                        
                        <th  v-if="!center">所屬中心</th>
                        
                        <th style="width:10%">角色</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(admin,index) in model.viewList" :key="index">
                        <td v-if="canCheck">
							<check-box :value="admin.id" :default="beenChecked(admin.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                        <td>
                            <a href="#" @click.prevent="onSelected(admin)" v-text="admin.user.profile.fullname"> </a> 
                           
                        </td>
                        <td>{{  admin.user.email }}</td>
                        <td>{{  admin.user.phone }}</td>
                        

                        <td v-if="!center" v-text="centerNames(admin)">

                        </td>
                        
                        
                        <td v-html="roleLabels(admin.user)"> </td>
                           
                       
                       
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
    name:'AdminTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        can_review:{
            type: Boolean,
            default: false
        },
        center: {
            type: Object,
            default: null
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
            return this.can_review;
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
			return null;
        },
        centerNames(admin){
            if(admin.centerNames) return admin.centerNames;
            return admin.centers;
        },
        hasContactInfo(admin){
            if(admin.user.contactInfo) return true;
            return false;
        },
        hasAddress(admin){
             
            if(!this.hasContactInfo(admin)) return false;
            if(admin.user.contactInfo.address) return true;
            return false;
        },
        roleLabels(user){
            if(user.roleNames) return User.roleLabels(user.roleNames);
            return User.roleLabels(user.roles);
        },
        onSelected(admin){
           if(admin.id)  this.$emit('selected',admin.id);
           else this.$emit('selected',admin.userId);
           
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
			
			let adminList = this.getViewList();
			if(!adminList)  return false;

			adminList.forEach( admin => {
				this.onChecked(admin.id)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
		},
        
   }
}
</script>

