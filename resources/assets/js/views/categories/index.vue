<template>
   <div>
        <div v-if="model" >
            <div class="row">
                <div class="col-sm-3" style="margin-top: 3px;"> 
                    <h3 v-html="title">
                   </h3>
                </div>
                <div class="col-sm-7 form-inline" style="margin-top: 20px;">
					<div class="form-group" style="padding-left:1cm;">
						<!-- <toggle :items="activeOptions"   :default_val="params.active" @selected="setActive"></toggle> -->
					</div>
				</div>
                
                <div align="right" class="col-sm-2" style="margin-top: 20px;">
                    <a v-if="can_edit" @click.prevent="beginImport" href="#" class="btn btn-warning">
                        <i class="fa fa-upload"></i>
                        匯入
                    </a>
                </div>
            </div>

            <hr/>
            <category-table :model="model" :can_edit="can_edit" :can_order="can_edit"
                @selected="onSelected" @saved="onSaved"
                @up="up" @down="down" @save-orders="saveImportances">
            </category-table>
            

        </div>


        
    </div> 
</template>


<script>
    import CategoryTable from '../../components/category/table';
    export default {
        name:'CategoryIndexView',
        components: {
            'category-table':CategoryTable,
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
            can_import:{
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
                title: Menus.getIcon('categories')  + '  課程分類管理',

                model:null,
                
                params:{
                    active:true,
                    page:1,
                    pageSize:10
                },

                activeOptions:Helper.activeOptions(),
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
            beginImport(){
                this.$emit('import');
            },
            onCreate(){
                this.$emit('create');
            },
            onSelected(id){
               this.$emit('selected',id);
            },
            up(category,index){
                let list= this.getList();
                if(!list.length) return;

                let upper=list[index-1];
                if(!upper) return;

                let upperOrder=upper.importance;
                let downOrder=category.importance;

                upper.importance=downOrder;
                category.importance=upperOrder;

                this.sortList();

            },
            down(category,index){
                let list= this.getList();
                if(!list.length) return;

                let downer=list[index+1];
                if(!downer) return;

                let downerOrder=downer.importance;
                let upperOrder=category.importance;

                downer.importance=upperOrder;
                category.importance=downerOrder;

                this.sortList();
            },
            sortList(){
                let list= this.getList();
                if(!list.length) return;
				list.sort((a,b)=>{
					return b.importance- a.importance;
				})
            },
            setActive(val) {
                this.params.active = val;
                this.fetchData();
            },
            fetchData() {
                    
                let getData = Category.index(this.params);

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onSaved(){
                this.fetchData();
            },
            saveImportances(){
                
                let list= this.getList();
                if(!list.length) return;

                let importances= list.map((item)=>{
                   return {
                       id:item.id,
                       importance:item.importance
                   };
                })

                let form=new Form({
                    importances:importances
                });

                let save=Category.importances(form);
				save.then(() => {
					this.fetchData();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        }
    }
</script>





