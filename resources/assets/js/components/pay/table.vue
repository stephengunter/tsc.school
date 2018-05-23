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
                        <th v-if="payed" style="width:10%">繳費方式</th>
                        <th v-if="can_quit" style="width:7%"></th>
                    </tr>
                    
                </thead>
                <tbody>
                    <tr v-for="(pay,index) in getViewList()" :key="index">
                        <td v-if="canCheck">
							<check-box :value="pay.id" :default="beenChecked(pay.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                      
                        <td> 
                            <a  href="#" @click.prevent="onSelected(pay.id)" v-text="pay.user.profile.fullname"> </a> 
                         
                        </td>
                        <td> 
                            {{ pay.date }} 
                        </td>
                        <td>
                            <i v-if="isTrue(pay.net)" class="fa fa-check-circle" style="color:green"></i>
                        </td>
                        <td v-html="$options.filters.payStatusLabel(pay.status)" ></td>
                        <td v-html="courseNames(pay)">

                        </td>
                        <td v-if="hasDiscount(pay)">
                             {{ pay.discount }} 
                            <span>
                               &nbsp; {{ pay.pointsText }}
                            </span>  

                        </td>
                        <td v-else>

                        </td>
                        <td>
                             {{ pay.amount | formatMoney }} 
                        </td>
                        <td v-if="payed" >
                            <span v-if="pay.bill.payway" >
                            {{ pay.bill.payway.name }} 
                           </span>
                        </td>
                        <td v-if="can_quit" >
                            <button @click.prevent="quit(pay)" class="btn btn-warning btn-xs" >
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
    name:'PayTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        pays:{
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
        canQuit(){

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
			return this.pays;
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
			
			let payList = this.getViewList();
			if(!payList)  return false;

			payList.forEach( pay => {
				this.onChecked(pay.id)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
        },
        courseNames(pay){
            return  Pay.courseNames(pay);
        },
        isTrue(val){
            return Helper.isTrue(val);
        },
        hasDiscount(pay){
            return Pay.hasDiscount(pay);
        },
        quit(pay){
            this.$emit('quit',pay.id);
        }
        
   }
}
</script>

