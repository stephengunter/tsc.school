<template>
   <div>
        <div>
            <div class="row">
                <div class="col-sm-2" style="margin-top: 3px;"> 
                    <h3 v-html="title">
                   </h3>
                </div>
                <div class="col-sm-5 form-inline" style="margin-top: 20px;">
                    <div  class="form-group" style="padding-left:1em;">
                        <drop-down :items="centers" :selected="params.center"
                            @selected="onCenterSelected">
                        </drop-down>
                    </div>
                    <div  class="form-group" style="padding-left:1em;">
                        <drop-down v-if="false" :items="weekdays" :selected="params.weekday"
                            @selected="onWeekdaySelected">
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
                    <a  @click.prevent="beginImport" href="#" class="btn btn-warning pull-right">
                        <i class="fa fa-upload"></i>
                        匯入
                    </a>
                </div>
            </div>

            <hr/>
            <div v-if="model">
                

                <volunteer-table  ref="volunteersTable"  :model="model" 
                    @selected="onSelected" @check-changed="onCheckIdsChanged">

                    <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                        <page-controll   :model="model" @page-changed="onPageChanged"
                            @pagesize-changed="onPageSizeChanged">
                        </page-controll>
                
                    </div>
                </volunteer-table>
            </div> 
            

        </div>

        
        
    </div> 
</template>


<script>
    import VolunteerTable from '../../components/volunteer/table';
    
    export default {
        name:'VolunteerIndexView',
        components: {
            'volunteer-table':VolunteerTable
        },
        props: {
            init_model: {
                type: Object,
                default: null
            },
            can_import:{
                type:Boolean,
                default:false
            },
            centers:{
                type:Array,
                default:null
            },
            weekdays:{
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
                title: Menus.getIcon('volunteers')  + '  志工管理',

                model:null,
                
                params:{
                    center:'0',
                    weekday:'0',
                    keyword:'',
                    page:1,
                    pageSize:10
                },

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

           
            	
        },
        computed:{
           
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
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
            onWeekdaySelected(item){
                this.params.weekday = item.value;
                this.fetchData();
            },
            beginImport(){
                this.$emit('import');
            },
            onCreate(){
                this.$emit('create');
            },
            onSelected(id){
               this.$emit('selected',id , this.isGroup);
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
                this.model=null;

                let getData = Volunteer.index(this.params);

                getData.then(data => {

                    this.model={ ...data.model };

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





