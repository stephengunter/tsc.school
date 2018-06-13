<template>
   
    <div v-if="model" >
        <div class="row">
            <div class="col-sm-2" style="margin-top: 3px;"> 
                <h3 v-html="title">
                </h3>
            </div>
            <div class="col-sm-5 form-inline" style="margin-top: 20px;">
                
                <div  class="form-group" style="padding-left:1em;">
                    <drop-down :items="keys" :selected="params.key"
						@selected="onKeySelected">
					</drop-down>
                </div>
                <div class="form-group" style="padding-left:1cm;">
                    <toggle :items="activeOptions"   :default_val="params.active" @selected="setActive"></toggle>
                </div>
                <div class="form-group" style="padding-left:1em;">
                    <toggle :items="reviewedOptions"   :default_val="params.reviewed" @selected="setReviewed"></toggle>
                    
                </div>
            </div>
            <div class="col-sm-3"  style="margin-top: 20px;"> 
                <searcher @search="onSearch">
                </searcher>
            </div>
            <div  align="right" class="col-sm-2" style="margin-top: 20px;">
                <a  @click.prevent="onCreate" href="#" class="btn btn-primary">
                    <i class="fa fa-plus-circle"></i> 新增
                </a>    
                
            </div>
        </div>
            
        <hr>
        
        <notice-table :model="model" @selected="onSelected">
          
            <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                <page-controll   :model="model" @page-changed="onPageChanged"
                    @pagesize-changed="onPageSizeChanged">
                </page-controll>
        
            </div>
        </notice-table>
        
        

    </div>

        
        
   
</template>


<script>
    import NoticeTable from '../../components/notice/table';
    export default {
        name:'NoticeIndexView',
        components: {
            'notice-table': NoticeTable,
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
            keys:{
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
                title: '公告管理',
               
                loaded:false,

                model:null,
                
                params:{
                   
                    key:'west',
                    active:true,
                    reviewed:true,
                    keyword:'',
                    
                    page:1,
                    pageSize:10
                },

                activeOptions:Notice.activeOptions(),
                reviewedOptions:Helper.reviewedOptions(),
              
               
                checkedIds:[],
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            if(this.init_model){
                this.model={...this.init_model };
            }  

            if(this.init_params){
                this.params={...this.init_params };
            }
            
            this.setKey(this.params.key);
        },
        computed:{
            
            
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onCreate(){
                this.$emit('create');
            },
            onSearch(keyword){
               
				this.params.keyword=keyword;
				this.fetchData();
            },
            setActive(val) {
                this.params.active = val;
                this.fetchData();
            },
            setReviewed(val) {
                this.params.reviewed = val;
                this.fetchData();
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
            onKeySelected(item){
                this.setKey(item.value);
                this.fetchData();
            },
            setKey(val){
                this.params.key=val;
            },
            fetchData() {
              
                let getData = Notice.index(this.params);

                getData.then(model => {

                    this.model={ ...model.model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            
        }
    }
</script>





