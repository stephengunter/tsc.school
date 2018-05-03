<template>
   
    <div v-if="model" >
        <div class="row">
            <div class="col-sm-2" style="margin-top: 3px;"> 
                <h3 v-html="title">
                </h3>
            </div>
            <div class="col-sm-7 form-inline" style="margin-top: 20px;">
               
                <div  class="form-group" style="padding-left:0.5em;">
                    <drop-down :items="centers" :selected="params.center"
                        @selected="onCenterSelected">
                    </drop-down>
                </div>
                <div  class="form-group" style="padding-left:0.5em;">
                    <select v-model="params.year"  class="form-control" @change="fetchData">
                        <option v-for="(item,index) in years" :key="index" :value="item.value" v-text="item.text" >
                        </option>
                    </select>
                </div>
                <div  class="form-group" style="padding-left:0.5em;">
                    <select v-model="params.month"  class="form-control" @change="fetchData">
                        <option v-for="(item,index) in months" :key="index" :value="item.value" v-text="item.text" >
                        </option>
                    </select>
                </div>
                <div  class="form-group" style="padding-left:0.5em;">
                    <toggle :items="reviewedOptions"   :default_val="params.reviewed" @selected="setReviewed"></toggle>
                    
                </div>
                
            </div>
            <div class="col-sm-3 pull-right" align="right" style="margin-top: 20px;">
                <div v-if="params.reviewed">
                    <a v-if="canFinish"  :disabled="!canSubmitFinish" @click.prevent="onFinishOk" href="#" class="btn btn-primary">
                        <i class="fa fa-check-circle"></i>
                        結案
                    </a>

                </div>
                <div v-else>
                    <a v-if="showReviewBtn" @click.prevent="onReviewOk" :disabled="!canSubmitReview"  href="#" class="btn btn-success">
                        <i class="fa fa-check-circle"></i>
                        審核通過
                    </a>
                    <a @click.prevent="onInitPayrolls" href="#" class="btn btn-warning">
                        <i class="fa fa-play-circle"></i>
                        自動產生
                    </a>
                    <small class="text-danger" v-if="form.errors.has('payroll.duplicate')" v-text="form.errors.get('payroll.duplicate')"></small>
                </div>
            </div>
            
        </div>

        <hr/>    
        
        <payroll-table :model="model" :can_review="showReviewBtn" :can_checked="canChecked" ref="payrollsTable"
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
            can_finish:{
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

                form:new Form({

                }),
              
                canReview:false,
                reviewedOptions:Helper.reviewedOptions(),

                canFinish:false,

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
            this.canFinish=this.can_finish;
        },
        computed:{
            
            showReviewBtn(){
                
                if(this.params.reviewed) return false;
                if(!this.params.center) return false;
                return true;
            },
            canChecked(){
                if(this.canReview) return true;
                if(this.canFinish) return true;
            },
            canSubmitReview(){
                return this.checkedIds.length > 0;
            },
            canSubmitFinish(){
                return this.checkedIds.length > 0;
            }
            
            
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
            onInitPayrolls(){
               
                this.form=new Form({
                    center:this.params.center,
                    year:this.params.year,
                    month:this.params.month
                });
                let init=Payroll.init(this.form);
                init.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();

                    this.checkedIds=[];
                    this.$refs.payrollsTable.unCheckAll();
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
                    this.canReview=model.canReview;

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
            onFinishOk(){
                
                if(!this.checkedIds.length) return;

                let payrolls= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       finish:true
                   };
                })

                let form=new Form({
                    payrolls:payrolls
                });

                let save=Payroll.finish(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.$refs.payrollsTable.unCheckAll();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        },
        clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
    }
</script>





