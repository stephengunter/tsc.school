<template>
   <div>
        <div v-if="model" >
            <div class="row">
                <div class="col-sm-3" style="margin-top: 3px;"> 
                    <h3 v-html="title">
                   </h3>
                </div>
                <div class="col-sm-6 form-inline" style="margin-top: 20px;">
                    <div v-show="params.active" class="form-group">
						<drop-down :items="overseaOptions" :selected="params.oversea"
							@selected="onOverseaSelected">
						</drop-down>
					</div>
					<div v-show="params.active" class="form-group">
						<drop-down v-show="!params.oversea" :items="areas" :selected="area.value"
							@selected="onAreaSelected">
						</drop-down>
					</div>
					<div class="form-group" style="padding-left:1cm;">
						<toggle :items="activeOptions"   :default_val="params.active" @selected="setActive"></toggle>
					</div>
					
					
				</div>
                
                <div  align="right" class="col-sm-3" style="margin-top: 20px;">
                    <a v-if="can_edit" @click.prevent="onCreate" href="#" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> 新增
                    </a>    
                    <a v-if="can_import" @click.prevent="beginImport" href="#" class="btn btn-warning">
                        <i class="fa fa-upload"></i>
                        匯入
                    </a>
                </div>
            </div>

            <hr/>
            <center-table :model="model" :can_order="can_edit"
                @selected="onSelected"
                @up="up" @down="down" @save-orders="saveImportances">
            </center-table>
            

        </div>


        
    </div> 
</template>


<script>
    import CenterTable from '../../components/center/table';
    export default {
        name:'CenterIndexView',
        components: {
            'center-table':CenterTable,
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
            areas:{
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
                title: Menus.getIcon('centers')  + '  開課中心管理',

                loaded:false,

                model:null,
                
                params:{
                    oversea:false,
                    area:0,
                    active:true,
                    page:1,
                    pageSize:10
                },

                overseaOptions:Center.overseaOptions(),

                activeOptions:Helper.activeOptions(),
                
                area:null,
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
                
            if(this.areas){
                this.setArea(this.areas[0]);
                
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
            up(center,index){
                let centers= this.getList();
                if(!centers.length) return;

                let upper=centers[index-1];
                if(!upper) return;

                let upperOrder=upper.importance;
                let downOrder=center.importance;

                upper.importance=downOrder;
                center.importance=upperOrder;

                this.sortCenters();

            },
            down(center,index){
                let centers= this.getList();
                if(!centers.length) return;

                let downer=centers[index+1];
                if(!downer) return;

                let downerOrder=downer.importance;
                let upperOrder=center.importance;

                downer.importance=upperOrder;
                center.importance=downerOrder;

                this.sortCenters();
            },
            sortCenters(){
                let centers= this.getList();
                if(!centers.length) return;
				centers.sort((a,b)=>{
					return b.importance- a.importance;
				})
            },
            onOverseaSelected(item){
                this.params.oversea=item.value;
                this.fetchData();
            },
            onAreaSelected(area){
                this.setArea(area);
                this.fetchData();
            },
            setArea(area){
                this.area=area;
                this.params.area=area.value;
            },
            setActive(val) {
                this.params.active = val;
                this.fetchData();
            },
            fetchData() {
                    
                let getData = Center.index(this.params);

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
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

                let save=Center.importances(form);
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





