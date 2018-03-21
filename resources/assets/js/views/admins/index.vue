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
						<drop-down :items="centers" :selected="params.center"
							@selected="onCenterSelected">
						</drop-down>
					</div>
					
					
				</div>
                <div class="col-sm-3" style="margin-top: 20px;">
                    <searcher @search="onSearch">
                    </searcher>
                </div>
                
                <div class="col-sm-2 pull-right" align="right" style="margin-top: 20px;">
                    <a  @click.prevent="onCreate" href="#" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> 新增
                    </a>
                    &nbsp;
                    <a  v-show="!showReviewBtn" @click.prevent="beginImport" href="#" class="btn btn-warning pull-right">
                        <i class="fa fa-upload"></i>
                        匯入
                    </a>
                </div>
            </div>

            <hr/>
            <admin-table :model="model" :can_review="canReview" :center="center"
                @selected="onSelected" @check-changed="onCheckIdsChanged">

                <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
					<page-controll   :model="model" @page-changed="onPageChanged"
						@pagesize-changed="onPageSizeChanged">
					</page-controll>
            
                </div>
            </admin-table>
            

        </div>


        
    </div> 
</template>


<script>
    import AdminTable from '../../components/admin/table';
    export default {
        name:'AdminIndexView',
        components: {
            'admin-table':AdminTable,
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
                title: Menus.getIcon('admins')  + '  權限管理',

                loaded:false,

                model:null,
                
                params:{
                    center:'0',
                    page:1,
                    pageSize:999
                },

                activeOptions:Helper.activeOptions(),
                
                center:null,

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
                
            // if(this.centers){
            //     this.setCenter(this.centers[0]);
                
            // }	
        },
        computed:{
            canReview(){
                if(!this.can_review) return false;
                return !this.params.active;
            },
            showReviewBtn(){
               return this.checkedIds.length > 0;
            }
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            beginImport(){
                this.$emit('import');
            },
            onCreate(){
                this.$emit('create');
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
            fetchData() {
                    
                let getData = Admin.index(this.params);

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
            onReviewOk(){
                
                if(!this.checkedIds.length) return;

                let admins= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       active:true
                   };
                })

                let form=new Form({
                    admins:admins
                });

                let save=Admin.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
					this.fetchData();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        }
    }
</script>





