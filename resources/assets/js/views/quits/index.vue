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

                <div class="form-group" style="padding-left:1em;">
                    <drop-down :items="statuses" :selected="params.status"
                        @selected="setStatus">
                    </drop-down>
                </div>
                
            </div>
            <div class="col-sm-3" style="margin-top: 20px;">
                <searcher text="姓名" @search="onSearch">
                </searcher>
            </div>
            <div class="col-sm-1 pull-right" align="right" style="margin-top: 20px;">
               
                <drop-down :items="actions" :selected="action" btn_style="primary"
                    @selected="onActionSelected">
                </drop-down>
                <small class="text-danger" v-if="error" v-text="error"></small>
            </div>
            
        </div>
            
        
        <div v-if="summary" class="row">
           
            <div class="col-sm-6" style="margin-top: 3px;font-size:1.2em"> 
                <div class="form-group" >
                     資料筆數：{{ summary.count }} 筆   總金額：{{ summary.amount }} 元
                </div>
               
            </div>
        </div> 
        
        <quit-table :model="model" :can_review="canReview" :can_checked="canChecked"  ref="quitTable" :center="center!=null"
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
            init_params: {
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

                center:null,
                
                params:{
                    
                    center:'0',
                    status:-1,
                 
                    keyword:'',
                    
                    page:1,
                    pageSize:10
                },

                canReview:false,
                
               
                checkedIds:[],

                action:'none',

                error:''
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            if(this.init_params){
                this.params={ ...this.init_params };
            }  

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
            hasData(){
                if(!this.model) return false;
                return this.model.viewList.length > 0;
            },
            showUnhadledBtn(){
               
                if(!this.canReview) return false;
                return parseInt(this.params.status) == 0;
              
            },
            showExportBtn(){
               
                if(!this.canReview) return false;
                return parseInt(this.params.status) == -1;
              
            },
            showReviewBtn(){

                if(!this.canReview) return false;
                return parseInt(this.params.status) == 0;
              
            },
            showFinishBtn(){

                if(!this.canReview) return false;
                return parseInt(this.params.status) == 1;
              
            },
            canChecked(){
                if(this.showReviewBtn) return true;
                if(this.showFinishBtn) return true;
                return false;
            },
            actions(){
                let actions=[{
                    value:'none' , text:'執行'
                }];

                if(!this.hasData)  return actions;

                if(this.showUnhadledBtn){
                    actions.push({
                      value:'unhandled' , text:'待處理'
                   });
                }

                if(this.showExportBtn){
                    actions.push({
                      value:'report' , text:'匯出審核報表'
                   });
                }

                if(this.showReviewBtn){
                    actions.push({
                      value:'review' , text:'審核通過'
                   });
                }
                

                if(this.showFinishBtn){
                    actions.push({
                      value:'finish' , text:'結案'
                    });
                }

               

                return actions;
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
            onCenterSelected(center){
                this.setCenter(center);
                this.fetchData();
            },
            setCenter(center){
                if(parseInt(center.value)>0){
                    this.center={ ...center };
                    this.params.center = center.value;
                }else{
                    this.center=null;
                    this.params.center = '0';
                }
            },
            setStatus(item){
                this.params.status = item.value;
                this.fetchData();
            },
            onSearch(keyword){
               
				this.params.keyword=keyword;
				this.fetchData();
			},
            fetchData() {
                
                let getData = Quit.index(this.params);

                getData.then(model => {

                    this.model={ ...model.model };
                    this.summary={ ...model.summary };
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
                this.error='';
            },
            onActionSelected(item){
                this.error='';

                let action=item.value;
                if(action=='report') this.exportReports();
                else if(action=='unhandled') this.updateStatuses(-1);
                else if(action=='review') this.updateStatuses(1);
                else if(action=='finish') this.updateStatuses(2);
            },
            checkError(){
                if(!this.checkedIds.length){
                    return '您沒有勾選任何一筆退費申請';
                    
                }
                return '';
            },
            updateStatuses(status){
                let form=new Form({
                    status:status
                });
                let save=Quit.updateStatuses(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
                    this.resolveError(form);
				})
            },
            resolveError(form){
               if(form.errors.has('status')){
                   this.error=form.errors.get('status');
               }
            },
            exportReports(){
                let url = '/manage/reports/quits';
                window.open(url);
            }
            
        }
    }
</script>





