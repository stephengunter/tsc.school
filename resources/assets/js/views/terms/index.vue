<template>
   <div>
        <div v-if="model" >
            <div class="row">
                <div class="col-sm-3" style="margin-top: 3px;"> 
                    <h3 v-html="title"></h3>
                </div>
                <div class="col-sm-7 form-inline" style="margin-top: 20px;">
					<div class="form-group" style="padding-left:1cm;">
						<toggle :items="activeOptions"   :default_val="params.active" @selected="setActive"></toggle>
					</div>
				</div>
                
                <div align="right" class="col-sm-2" style="margin-top: 20px;">
                    
                    <a  @click.prevent="onCreate" href="#" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> 新增
                    </a>
                </div>
            </div>

            <hr/>
            <term-table :model="model" :can_edit="can_edit" :can_remove="canRemove"
                @selected="onSelected" @remove="onRemove">
               
            </term-table>
            

        </div>

        <delete-confirm :showing="deleteConfirm.showing" :message="deleteConfirm.message"
            @close="deleteConfirm.showing=false" @confirmed="deleteTerm">
        </delete-confirm>

    </div> 
</template>


<script>
    import TermTable from '../../components/term/table';
    export default {
        name:'TermIndexView',
        components: {
            'term-table':TermTable,
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
                title: Menus.getTitleHtml('terms'),

                model:null,
                
                params:{
                    active:true,
                    page:1,
                    pageSize:10
                },

                activeOptions:Helper.activeOptions(),

                deleteConfirm:{
                    id:0,
                    showing:false,
                    message:''
                }
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
           canRemove(){
               if(!this.can_edit) return false;
               return !this.params.active;
           }
           
        }, 
        methods:{
            init(){
                
            },
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
            setActive(val) {
                this.params.active = val;
                this.fetchData();
            },
            fetchData() {
                    
                let getData = Term.index(this.params);

                getData.then(model => {

                    this.model={ ...model };
                   
                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onRemove(term){
               
                this.deleteConfirm.message='確定要刪除學期設定 ' + term.number + ' 嗎?';
                this.deleteConfirm.id=term.id;
                this.deleteConfirm.showing=true;        
            },
            closeConfirm(){
                this.deleteConfirm.message='';
                this.deleteConfirm.id=0;
                this.deleteConfirm.showing=false;
            },
            deleteTerm(){
                
                
                let id = this.deleteConfirm.id 
                let remove= Term.remove(id)
                remove.then(result => {
                    this.closeConfirm();
                    this.fetchData();
                    Helper.BusEmitOK('刪除成功')
                    
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                    this.closeConfirm();   
                })
            },
            
        }
    }
</script>





