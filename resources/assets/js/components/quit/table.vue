<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        
                        <th style="width:3%" v-if="false">
                            <check-box v-show="dataCounts" :value="0" :default="checkAll"
							 @selected="onCheckAll" @unselected="unCheckAll">
							</check-box>
                        </th>
                       
                        <th style="width:7%">姓名</th>
                        <th style="width:7%">原因</th>
                        <th style="width:10%">申請日期</th>
                        <th>明細</th>
                        <th style="width:7%">退還學費</th>
                        <th style="width:7%">手續費</th>
                        <th v-if="false" style="width:10%">退款方式</th>
                        <th style="width:7%">應退金額</th>
                        <th>銀行帳號</th>
                        <th style="width:7%">狀態</th>
                    </tr>
                    
                </thead>
                <tbody>
                    <tr v-for="(quit,index) in getViewList()" :key="index">
                        <td v-if="false">
							<check-box :value="quit.id" :default="beenChecked(quit.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                        
                        <td> 
                            <a  href="#" @click.prevent="onSelected(quit.id)" v-text="quit.signup.user.profile.fullname"> </a> 
                         
                        </td>
                        <td>
                            {{ quit.reason }} 
                        </td>
                        <td> 
                            {{ quit.date }} 
                        </td>
                        <td v-html="getDetails(quit)">
                          
                        </td>
                        <td>
                             {{ quit.tuitions | formatMoney }}    
                        </td>
                        <td>
                             {{ quit.fee | formatMoney }}    
                        </td>
                        <td v-if="false">
                             {{ quit.payway.name }} 
                        </td>
                        <td>
                             {{ quit.amount | formatMoney }} 
                        </td>
                        <td v-html="quit.accountInfo" ></td>
                      
                        <td v-html="getStatusLabel(quit)" ></td>
                      
                       
                       
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
    name:'QuitTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        quits:{
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
        }
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
            if(!this.can_checked) return false;
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
			return this.quits;
        },
        getDetails(quit){
          
            let html='';
            quit.details.forEach(item=>{
                html += Quit.detailSummary(item) + '<br>'
            })
            return html;
        },
        getStatusLabel(quit){
          
            let statusLabel=Quit.statusLabel(quit.status);
            return statusLabel;
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
			
			let quitList = this.getViewList();
			if(!quitList)  return false;

			quitList.forEach( quit => {
				this.onChecked(quit.signupId)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
        },
        courseNames(quit){
            return  Quit.courseNames(quit);
        },
        isTrue(val){
            return Helper.isTrue(val);
        }
        
   }
}
</script>

