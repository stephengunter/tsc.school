<template>
   
    <div v-if="model" >
        <div class="row">
            <div class="col-sm-2" style="margin-top: 3px;"> 
                <h3 v-html="title">
                </h3>
            </div>
            <div class="col-sm-7 form-inline" style="margin-top: 20px;">
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
                <div v-if="false" class="form-group" style="padding-left:1em;">
                    <drop-down :items="payways" :selected="params.payway"
                        @selected="setPayway">
                    </drop-down>
                </div>
                
            </div>
            <div class="col-sm-2"  style="margin-top: 20px;"> 
                <searcher text="姓名" @search="onSearch">
                </searcher>
            </div>
            <div class="col-sm-1 pull-right" style="margin-top: 20px;">
                <a @click.prevent="onCreate" href="#" class="btn btn-primary">
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

        
        <signup-table :model="model" :payed="payed" :can_quit="canQuit"
            @selected="onSelected" @quit="onQuit"  @check-changed="onCheckIdsChanged">
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
            init_params: {
                type: Object,
                default: null
            },
            can_quit:{
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
                title: Menus.getIcon('signups')  + '  報名管理',
               
                loaded:false,

                model:null,

                summary:null,

                canQuit:false,

                courseOptions:[],
                
                params:{
                    term:'0',
                    center:'0',
                    course:'0',
                    status:'0',
                    keyword:'',
                    
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

            if(this.init_params){
                if(this.init_params.term) this.params.term=this.init_params.term.id;
                if(this.init_params.center) this.params.center=this.init_params.center.id;
                if(this.init_params.course) this.params.course=this.init_params.course.id;

                this.params.status=this.init_params.status;
                this.params.keyword=this.init_params.keyword;
                this.params.page=this.init_params.page;
                this.params.pageSize=this.init_params.pageSize;
            
            } 
           

            this.courseOptions=this.courses.slice(0);
            	
            this.canQuit=this.can_quit;	 	
        },
        computed:{
            hasCourse(){
                return Helper.tryParseInt(this.params.course) > 0;
            },
            showReviewBtn(){
               return this.checkedIds.length > 0;
            },
            payed(){
               return  Helper.tryParseInt(this.params.status) != 0;     
            }
            
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onSearch(keyword){
               
				this.params.keyword=keyword;
				this.fetchData();
			},
            onCreate(){
                let params={
                    course:this.params.course,
                    center:this.params.center
                };
                this.$emit('create',params);
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
            setPayway(item){
                this.params.payway = item.value;
                this.fetchData();
            },
            onCourseSelected(item){
                this.params.course = item.value;
                this.fetchData();
            },
            onQuit(id){
               this.$emit('quit',id);
            },
            setReviewed(val) {
                this.params.reviewed = val;
                this.fetchData();
            },
            fetchData() {
                if(!this.payed) this.params.payway='0';
                let getData = Signup.index(this.params);

                getData.then(model => {

                    this.model={ ...model.model };
                    this.summary={ ...model.summaryModel };
                    this.courseOptions=model.courseOptions.slice(0);

                    this.canQuit=model.canQuit;

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onCheckIdsChanged(ids){
                this.checkedIds=ids.slice(0);
            },
            
        }
    }
</script>





