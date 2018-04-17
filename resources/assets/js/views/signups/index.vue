<template>
   
    <div v-if="model" >
        <div class="row">
            <div class="col-sm-2" style="margin-top: 3px;"> 
                <h3 v-html="title">
                </h3>
            </div>
            <div class="col-sm-6 form-inline" style="margin-top: 20px;">
                <div class="form-group" style="padding-left:1em;">
                    <drop-down :items="terms" :selected="params.term"
                        @selected="onTermSelected">
                    </drop-down>
                </div>
                <div  class="form-group" style="padding-left:1em;">
                    <drop-down :items="centers" :selected="params.center"
                        @selected="onCenterSelected">
                    </drop-down>
                </div>
                 <div  class="form-group" style="padding-left:1em;">
                    <drop-down :items="courseOptions" :selected="params.course"
                        @selected="onCourseSelected">
                    </drop-down>
                </div>
                <div class="form-group" style="padding-left:1em;">
                    <drop-down :items="statuses" :selected="params.status"
                        @selected="setStatus">
                    </drop-down>
                </div>
                
                
            </div>
            <div class="col-sm-3" style="margin-top: 3px;"> 
               
            </div>
            <div class="col-sm-1 pull-right" align="right" style="margin-top: 20px;">
                <a v-show="hasCourse" @click.prevent="onCreate" href="#" class="btn btn-primary">
                    <i class="fa fa-plus-circle"></i> 新增
                </a>
                
            </div>
        </div>
            
        <div v-if="summary" class="row">
           
            <div class="col-sm-6" style="margin-top: 3px;font-size:1.2em"> 
                <div class="form-group" >
                     總數：{{ summary.total }} 筆   已繳費：{{ summary.ok }} 筆   待繳費：{{ summary.no }} 筆   已取消：{{ summary.canceled }} 筆
                </div>
               
            </div>
        </div>  

        
        <signup-table :model="model" :can_review="canReview" 
            @selected="onSelected" @check-changed="onCheckIdsChanged">
            <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                <page-controll   :model="model" @page-changed="onPageChanged"
                    @pagesize-changed="onPageSizeChanged">
                </page-controll>
        
            </div>
        </signup-table>
        
        

    </div>

        
        
   
</template>


<script>
    import SignupTable from '../../components/signup/table';
    
    export default {
        name:'SignupIndexView',
        components: {
            'signup-table':SignupTable
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
            can_import:{
                type:Boolean,
                default:false
            },
            terms:{
                type:Array,
                default:null
            },
            centers:{
                type:Array,
                default:null
            },
            courses:{
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
                title: Menus.getIcon('signups')  + '  報名管理',
               
                loaded:false,

                model:null,

                summary:null,

                courseOptions:[],
                
                params:{
                    term:'0',
                    center:'0',
                    course:'0',
                    status:'0',
                    
                    page:1,
                    pageSize:999
                },
              
               
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

           

            this.courseOptions=this.courses.slice(0);
            this.params.term=this.terms[0].value;
            this.params.center=this.centers[0].value;
            	
            this.canReview=this.can_review;	 	
        },
        computed:{
            hasCourse(){
                return Helper.tryParseInt(this.params.course) > 0;
            },
            showReviewBtn(){
               return this.checkedIds.length > 0;
            },
            
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onCreate(){
                this.$emit('create',this.params.course);
            },
            onSelected(id){
               this.$emit('selected',id);
            },
            onPageChanged(page){
				this.params.page=page;
				this.fetchData();
            },
            onPageSizeChanged(){
                
                this.params.pageSize=this.model.pageSize;
				this.fetchData();
            },
            onTermSelected(item){
                this.params.term=item.value;
                this.params.course='0';
                this.fetchData()
            },
            onCenterSelected(item){
                this.params.center = item.value;
                this.params.course='0';
                this.fetchData();
            },
            setStatus(item){
                this.params.status = item.value;
                this.fetchData();
            },
            onCourseSelected(item){
                this.params.course = item.value;
                this.fetchData();
            },
            setReviewed(val) {
                this.params.reviewed = val;
                this.fetchData();
            },
            fetchData() {
                
                let getData = Signup.index(this.params);

                getData.then(model => {

                    this.model={ ...model.model };
                    this.summary={ ...model.summaryModel };
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

                let signups= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       reviewed:true
                   };
                })

                let form=new Form({
                    signups:signups
                });

                let save=Signup.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.checkedIds=[];
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        }
    }
</script>





