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
                        
                        <th style="width:10%">姓名</th>
                        <th style="width:10%">報名日期</th>
                        <th style="width:8%">網路報名</th>
                        <th style="width:10%">狀態</th>
                        <th>報名課程</th>
                        <th style="width:25%">折扣</th>
                        <th style="width:10%">應繳金額</th>
                        <th style="width:10%">已繳金額</th>
                        <th v-if="can_quit" style="width:7%"></th>
                    </tr>
                    
                </thead>
                <tbody>
                    <tr v-for="(signup,index) in getViewList()" :key="index">
                        <td v-if="canCheck">
							<check-box :value="signup.id" :default="beenChecked(signup.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                      
                        <td> 
                            <a v-if="can_select"  href="#" @click.prevent="onSelected(signup.id)" v-text="signup.user.profile.fullname"> </a> 
                            <span v-else v-text="signup.user.profile.fullname"></span>
                        </td>
                        <td> 
                            {{ signup.date }} 
                        </td>
                        <td>
                            <i v-if="isTrue(signup.net)" class="fa fa-check-circle" style="color:green"></i>
                        </td>
                        <td v-html="$options.filters.signupStatusLabel(signup.status)" ></td>
                        <td v-html="courseNames(signup)">

                        </td>
                        <td v-if="hasDiscount(signup)">
                             {{ signup.discount }} 
                            <span>
                               &nbsp; {{ signup.pointsText }}
                            </span>  

                        </td>
                        <td v-else>

                        </td>
                        <td>
                             {{ signup.amount | formatMoney }} 
                        </td>
                        <td>
                           
                            {{ signup.amountPayed | formatMoney }} 
                           
                        </td>
                        <td v-if="canQuit(signup)">
                            <button @click.prevent="quit(signup)" class="btn btn-warning btn-xs" >
                               我要退費
                            </button>
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
    name:'SignupTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        signups:{
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
        can_quit:{
            type: Boolean,
            default: false
        },
        payed:{
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
        },
        
		
    }, 
	watch: {
		checked_ids() {
			this.$emit('check-changed',this.checked_ids);
		}
	},
    methods:{
        getViewList(){
			if(this.model) return this.model.viewList;
			return this.signups;
        },
        canQuit(signup){
            if(!this.can_quit) return false;
            return signup.canQuit;
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
			
			let signupList = this.getViewList();
			if(!signupList)  return false;

			signupList.forEach( signup => {
				this.onChecked(signup.id)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
        },
        courseNames(signup){
            return  Signup.courseNames(signup);
        },
        isTrue(val){
            return Helper.isTrue(val);
        },
        hasDiscount(signup){
            return Signup.hasDiscount(signup);
        },
        quit(signup){
            this.$emit('quit',signup.id);
        }
        
   }
}
</script>

