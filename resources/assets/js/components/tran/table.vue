<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:10%">日期</th>
                        <th style="width:10%">學員姓名</th>
                        <th>原課程</th>
                        <th>轉班後課程</th>
                        <th style="width:10%">應繳金額</th>
                        <th style="width:10%">應退金額</th>
                        <th style="width:7%"></th>
                    </tr>
                    
                </thead>
                <tbody>
                    <tr v-for="(tran,index) in getViewList()" :key="index">
                        <td> 
                            {{ tran.date }} 
                        </td>
                        <td v-text="tran.user.profile.fullname"> 
                         
                        </td>
                       
                        <td>
                             {{ tran.formCourse.fullName }} 
                        </td>
                        <td>
                             {{ tran.course.fullName }} 
                        </td>
                       
                        <td v-if="tran.amountMustPay">
                            
                             {{ tran.amountMustPay | formatMoney }} 

                        </td>
                        <td v-else>

                        </td>
                        <td v-if="tran.amountMustBack">
                           
                          
                                {{ tran.amountMustBack | formatMoney }} 

                                &nbsp;
                                <button v-if="!tran.quit" class="btn btn-primary btn-xs" @click.prevent="editQuit(tran)">
                                    <i class="fa fa-edit"></i>
                                </button>
                          
                            
                        </td>
                        <td v-else>

                        </td>
                       
                        <td>
                            
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
    name:'TranTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        trans:{
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
		
        dataCounts(){
          
            let viewList=this.getViewList();
            if(!viewList) return 0;
            return viewList.length;
        },
        canQuit(){

        }
		
    }, 
	watch: {
		
	},
    methods:{
        getViewList(){
			if(this.model) return this.model.viewList;
			return this.trans;
		},
        onSelected(id){
            
           this.$emit('selected',id);
        },
        courseNames(tran){
            return  Tran.courseNames(tran);
        },
        isTrue(val){
            return Helper.isTrue(val);
        },
        editQuit(tran){
            this.$emit('edit-quit',tran);
        },

        
   }
}
</script>

