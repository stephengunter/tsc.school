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
                    <drop-down :items="keys" :selected="params.key"
						@selected="onKeySelected">
					</drop-down>
                </div>
              
                
            </div>
            <div class="col-sm-3"  style="margin-top: 20px;"> 
                <searcher text="姓名" @search="onSearch">
                </searcher>
            </div>
            
        </div>
            
        <hr>
        
        <tran-table :model="model" @edit-quit="editQuit">
          
            <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                <page-controll   :model="model" @page-changed="onPageChanged"
                    @pagesize-changed="onPageSizeChanged">
                </page-controll>
        
            </div>
        </tran-table>
        
        <modal :showbtn="false"  :show.sync="quitEdit.show"  @closed="quitEdit.show=false" 
            effect="fade" :width="1200">
            <div slot="modal-header" class="modal-header modal-header-danger">
                
                <button id="close-button" type="button" class="close"  @click="quitEdit.show=false">
                        x
                </button>
                 <h3> 轉班退費 </h3>
            </div>
        
            <div slot="modal-body" class="modal-body">
                <quit-edit  v-if="quitEdit.show" :tran="quitEdit.tran"
                 @saved="onQuitSaved" @cancel="quitEdit.show=false">

                </quit-edit>
            </div>
        </modal>

    </div>

        
        
   
</template>


<script>
    import TranTable from '../../components/tran/table';
    import QuitEdit from '../../components/tran/quit-edit';
    export default {
        name:'TranIndexView',
        components: {
            'tran-table': TranTable,
            'quit-edit' : QuitEdit
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
            terms:{
                type:Array,
                default:null
            },
            keys:{
                type:Array,
                default:null
            },
            key_val:{
                type:String,
                default:''
            },
            version:{
                type:Number,
                default:0
            },
        },
        data(){
            return {
                title: '轉班紀錄',
               
                loaded:false,

                model:null,
                
                params:{
                    term:'0',
                    key:'',
                
                    keyword:'',
                  
                    
                    page:1,
                    pageSize:999
                },
              
               
                checkedIds:[],

                quitEdit:{
                    show:false,
                    tran:null
                }
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            this.params={...this.init_params };
            this.params.page=this.init_model.pageNumber;
            this.params.pageSize=this.init_model.pageSize;


            this.model={...this.init_model };
            
            this.setKey(this.params.key);
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
            onKeySelected(item){
                this.setKey(item.value);
                this.fetchData();
            },
            setKey(val){
                this.params.key=val;
            },
            fetchData() {
              
                let getData = Tran.index(this.params);

                getData.then(model => {

                    this.model={ ...model.model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            editQuit(tran){
               this.quitEdit.tran=tran;
               this.quitEdit.show=true;
            },
            onQuitSaved(){
                this.quitEdit.show=false;
                this.quitEdit.tran=null;

                this.fetchData();
            }
            
        }
    }
</script>





