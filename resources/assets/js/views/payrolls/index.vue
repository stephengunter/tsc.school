<template>
   
    <div v-if="model" >
        <div class="row">
            <div class="col-sm-2" style="margin-top: 20px;"> 
                <h3 v-html="title">
                </h3>
            </div>
            <div class="col-sm-8 form-inline" style="margin-top: 20px;">
               
                <div  class="form-group" style="padding-left:0.5em;">
                    <drop-down :items="centers" :selected="params.center"
                        @selected="onCenterSelected">
                    </drop-down>
                </div>
                <div  class="form-group" style="padding-left:0.5em;">
                    <select v-model="params.year"  class="form-control">
                        <option v-for="(item,index) in years" :key="index" :value="item.value" v-text="item.text" >
                        </option>
                    </select>
                </div>
                <div  class="form-group" style="padding-left:0.5em;">
                    <select v-model="params.month"  class="form-control">
                        <option v-for="(item,index) in months" :key="index" :value="item.value" v-text="item.text" >
                        </option>
                    </select>
                </div>
                <div  class="form-group" style="padding-left:0.5em;">
                    <toggle :items="reviewedOptions"   :default_val="params.reviewed" @selected="setReviewed"></toggle>
                    
                </div>
                
            </div>
            <div class="col-sm-2 pull-right" align="right" style="margin-top: 20px;">

                <a v-if="showReviewBtn" @click.prevent="onReviewOk" :disabled="!canSubmitReview"  href="#" class="btn btn-success">
                    <i class="fa fa-check-circle"></i>
                    審核通過
                </a>
                <a @click.prevent="onInitByDate" href="#" class="btn btn-warning">
                    <i class="fa fa-play-circle"></i>
                    自動產生
                </a>
                
            </div>
            
        </div>

        <hr/>    
        
        <payroll-table v-if="false" :model="model" :can_review="showReviewBtn" ref="payrollsTable"
            @selected="onSelected" @check-changed="onCheckIdsChanged">
            <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                <page-controll   :model="model" >
                    
                </page-controll>
        
            </div>
        </payroll-table>
        
        

    </div>

        
        
   
</template>


<script>
    import PayrollTable from '../../components/payroll/table';
    
    export default {
        name:'PayrollIndexView',
        components: {
            'payroll-table':PayrollTable
        },
        props: {
            init_model: {
                type: Object,
                default: null
            },
            init_params: {
                type: Object,
                default: null
            },
            can_review:{
                type:Boolean,
                default:false
            },
            centers:{
                type:Array,
                default:null
            },
            years:{
                type:Array,
                default:null
            },
            months:{
                type:Array,
                default:null
            },
            version:{
                type:Number,
                default:0
            },
        },
        data(){
            return {
                title: '教師鐘點費',

                model:null,

                courseOptions:[],
                
                params:{
                    center:'0',
                    year:'0',
                    month:'0',
                    reviewed:false
                },
              
                canReview:false,
                reviewedOptions:Helper.reviewedOptions(),

                checkedIds:[]
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            if(this.init_model){
                this.model={...this.init_model };
            }
           
            this.params.center=this.centers[0].value;

            this.params.year=this.init_params.year;
            this.params.month=this.init_params.month;
            	
            this.canReview=this.can_review;	 	
        },
        computed:{
            
            showReviewBtn(){
                
                if(this.params.reviewed) return false;
                if(!this.params.center) return false;
                return true;
            },
            canSubmitReview(){
                return this.checkedIds.length > 0;
            },
            
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onSelected(id){
               this.$emit('selected',id);
            },
            onCenterSelected(item){
                this.params.center = item.value;
                this.params.course='0';
                this.fetchData();
            },
            setReviewed(val) {
                this.params.reviewed = val;
                this.fetchData();
            },
            onInitByDate(){
                let center=this.params.center;
                let date=this.params.beginDate;
                let form=new Form({
                    center:center,
                    date:date
                });
                let init=Payroll.init(form);
                init.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.checkedIds=[];
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})

                
            },
            fetchData() {
              
                let params={
                    ...this.params
                };
                if(!params.beginDate) params.beginDate='';
                
                let getData = Payroll.index(params);

                getData.then(model => {

                    this.model={ ...model.model };
                    this.courseOptions=model.courseOptions.slice(0);

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onCheckIdsChanged(ids){
                this.checkedIds=ids.slice(0);
            },
            onReviewOk(){
                
                if(!this.checkedIds.length) return;

                let payrolls= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       reviewed:true
                   };
                })

                let form=new Form({
                    payrolls:payrolls
                });

                let save=Payroll.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.checkedIds=[];
                    this.$refs.payrollsTable.unCheckAll();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        }
    }
</script>





