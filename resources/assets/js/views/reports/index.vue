<template>
   
    <div class="row">
        <div class="col-sm-2" style="margin-top: 3px;"> 
            <h3> 報表管理
            </h3>
        </div>
        <div class="col-sm-6 form-inline" style="margin-top: 20px;">
            <div  class="form-group">
            <select class="form-control" name="" id="AA">
                <option value="1">選單A</option>
                <option value="1">選單B</option>
            </select>
                
            </div>

            <div class="form-group" style="padding-left:1em;">
            <select class="form-control" name="" id="BB">
                <option value="1">選單A</option>
                <option value="1">選單B</option>
            </select>
            </div>
            
        </div>
        <div class="col-sm-3" style="margin-top: 20px;">
            <searcher  @search="onSearch">
            </searcher>
        
        </div>
        <div class="col-sm-1 pull-right" align="right" style="margin-top: 20px;">
            
        <button type="button" class="btn btn-primary">送出</button>
            
        </div>
        
    </div>

        
        
   
</template>


<script>
    
    
    export default {
        name:'ReportIndexView',
        components: {
           
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
            	
        },
        computed:{
            
           
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





