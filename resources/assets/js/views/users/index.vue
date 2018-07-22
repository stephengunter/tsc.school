<template>
   <div>
        <div v-if="model" >
            <div class="row">
                <div class="col-sm-3" style="margin-top: 3px;"> 
                    <h3 v-html="title">
                   </h3>
                </div>
                <div class="col-sm-4 form-inline" style="margin-top: 20px;">
                    <div class="form-group">
						
					</div>
					
					
					
					
				</div>
                <div class="col-sm-3" style="margin-top: 20px;">
                    <searcher @search="onSearch">
                    </searcher>
                </div>
                
                <div class="col-sm-2 pull-right" align="right" style="margin-top: 20px;">
                    <a  @click.prevent="beginImport" href="#" class="btn btn-warning pull-right">
                        <i class="fa fa-upload"></i>
                        匯入
                    </a>
                    
                </div>
            </div>

            <hr/>
            <user-table :model="model" 
                @selected="onSelected" @check-changed="onCheckIdsChanged">

                <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
					<page-controll   :model="model" @page-changed="onPageChanged"
						@pagesize-changed="onPageSizeChanged">
					</page-controll>
            
                </div>
            </user-table>
            

        </div>


        
    </div> 
</template>


<script>
    import UserTable from '../../components/user/table';
    export default {
        name:'UserIndexView',
        components: {
            'user-table':UserTable,
        },
        props: {
            init_model: {
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
            centers:{
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
                title: Menus.getIcon('users')  + '  使用者管理',

                loaded:false,

                model:null,
                
                params:{
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
                this.params.page=this.init_model.pageNumber;
                this.params.pageSize=this.init_model.pageSize;
            }	
        },
        computed:{
           
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onSelected(id){
               this.$emit('selected',id);
            },
            onSearch(keyword){
               
				this.params.keyword=keyword;
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
            fetchData() {
                    
                let getData = User.index(this.params);

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onCheckIdsChanged(ids){
                this.checkedIds=ids.slice(0);
            },
            beginImport(){
                this.$emit('import');
            },
            
        }
    }
</script>





