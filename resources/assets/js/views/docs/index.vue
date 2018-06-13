<template>
   <div>
        <div v-if="model" >
            <div class="row">
                <div class="col-sm-3" style="margin-top: 3px;"> 
                    <h3 v-html="title">
                   </h3>
                </div>
                <div class="col-sm-6 form-inline" style="margin-top: 20px;">
                   
					
					
				</div>
                
                <div  align="right" class="col-sm-3" style="margin-top: 20px;">
                    <a v-if="can_edit" @click.prevent="onCreate" href="#" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> 新增
                    </a>    
                   
                </div>
            </div>

            <hr/>
            <doc-table v-show="model.totalItems > 0"  :model="model" :can_edit="can_edit" 
                @selected="onSelected"  @remove="beginDelete">

                <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                    <page-controll   :model="model" >
                       
                    </page-controll>
            
                </div>


            </doc-table>
            

        </div>

        <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
            @close="deleteConfirm.show=false" @confirmed="deleteDoc">        
        </delete-confirm>
        
    </div> 
</template>


<script>
    import DocTable from '../../components/doc/table';
    export default {
        name:'DocIndexView',
        components: {
            'doc-table':DocTable,
        },
        props: {
            init_model: {
                type: Object,
                default: null
            },
            can_edit:{
                type:Boolean,
                default:false
            },
            version:{
                type:Number,
                default:0
            },
        },
        data(){
            return {
                title: '下載專區',

                loaded:false,

                model:null,

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
                
              
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            if(this.init_model){
                 this.model={...this.init_model };
            }
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
            onSelected(id){
                this.$emit('selected',id);
            },
            fetchData() {
                    
                let getData = Doc.index();

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            beginDelete(doc){
               
                let id= doc.id;
                let title= doc.title;

                this.deleteConfirm.msg='確定要刪除 ' +  title  + ' 嗎?'
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;                
            },
            deleteDoc(){
             
                let id = this.deleteConfirm.id; 
                let remove=Doc.remove(id);
                
                this.deleteConfirm.show=false;

                remove.then(() => {
                    Helper.BusEmitOK('刪除成功');
                    this.fetchData();
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                    this.deleteConfirm.show=false;  
                })

                   
                
            },
            
        }
    }
</script>





