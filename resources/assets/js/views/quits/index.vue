<template>
   
    <div v-if="model" >
        <div class="row">
            <div class="col-sm-2" style="margin-top: 3px;"> 
                <h3 v-html="title">
                </h3>
            </div>
            <div class="col-sm-6 form-inline" style="margin-top: 20px;">
                <div  class="form-group">
                    <drop-down :items="centers" :selected="params.center"
                        @selected="onCenterSelected">
                    </drop-down>
                </div>

                <div  class="form-group" style="padding-left:1em;">
                    
                    <drop-down :items="payways" :selected="params.payway"
                        @selected="onPaywaySelected">
                    </drop-down>
                </div>
                
                <div class="form-group" style="padding-left:1em;">
                    <toggle :items="reviewedOptions"   :default_val="params.reviewed" @selected="setReviewed"></toggle>
                    
                </div>

                <div class="form-group" style="padding-left:1em;">
                    <drop-down :items="statuses" :selected="params.status"
                        @selected="setStatus">
                    </drop-down>
                </div>
                
            </div>
           
            <div class="col-sm-4 pull-right" align="right" style="margin-top: 20px;">
                <a v-if="canReview" v-show="showReviewBtn" :disabled="!canSubmitReview" @click.prevent="onReviewOk" href="#" class="btn btn-success">
                    <i class="fa fa-check-circle"></i>
                    審核通過
                </a>
                
            </div>
        </div>
            
        
        <hr>
        
        <quit-table :model="model" :can_review="canReview"  ref="quitTable" 
            @selected="onSelected" @check-changed="onCheckIdsChanged">
            <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                <page-controll   :model="model" @page-changed="onPageChanged"
                    @pagesize-changed="onPageSizeChanged">
                </page-controll>
        
            </div>
        </quit-table>
        
        

    </div>

        
        
   
</template>


<script>
    import QuitTable from '../../components/quit/table';
    
    export default {
        name:'QuitIndexView',
        components: {
            'quit-table':QuitTable
        },
        props: {
            init_model: {
                type: Object,
                default: null
            },
            summary_model: {
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
            statuses:{
                type:Array,
                default:null
            },
            payways:{
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
                title: '退費管理',

                model:null,

                summary:null,
                
                params:{
                    
                    center:'0',
                    status:'0',
                    payway:'0',
                    reviewed:false,
                    
                    page:1,
                    pageSize:10
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
                this.summary={...this.summary_model };
               
                this.params.page=this.init_model.pageNumber;
                this.params.pageSize=this.init_model.pageSize;
            }  

           
            this.params.center=this.centers[0].value;
            	
            this.canReview=this.can_review;	 	
        },
        computed:{
           
            showReviewBtn(){
                return !this.params.reviewed;
              
            },
            canSubmitReview(){
                return this.checkedIds.length > 0;
            }
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onReviewedSelected(item){
                this.params.reviewed=item.value;
                this.fetchData();
            },
            onPageChanged(page){
				this.params.page=page;
				this.fetchData();
            },
            onPageSizeChanged(){
                
                this.params.pageSize=this.model.pageSize;
				this.fetchData();
            },
            onCenterSelected(item){
                this.params.center = item.value;
                this.fetchData();
            },
            onPaywaySelected(item){
                this.params.payway = item.value;
                this.fetchData();
            },
            setStatus(item){
                this.params.status = item.value;
                this.fetchData();
            },
            setReviewed(val) {
                this.params.reviewed = val;
                this.fetchData();
            },
            fetchData() {
                
                let getData = Quit.index(this.params);

                getData.then(model => {

                    this.model={ ...model.model };
                    this.canReview= model.canReview;

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onSelected(id){
               this.$emit('selected',id);
            },
            onCheckIdsChanged(ids){
                this.checkedIds=ids.slice(0);
            },
            onReviewOk(){
                
                if(!this.checkedIds.length) return;

                let quits= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       reviewed:true
                   };
                })

                let form=new Form({
                    quits:quits
                });

                let save=Quit.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.$refs.quitTable.unCheckAll();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        }
    }
</script>





