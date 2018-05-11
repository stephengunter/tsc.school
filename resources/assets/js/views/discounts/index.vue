<template>
   <div>
        <div v-if="model" >
            <div class="row">
                <div class="col-sm-3" style="margin-top: 3px;"> 
                    <h3 v-html="title">
                   </h3>
                </div>
                <div class="col-sm-6 form-inline" style="margin-top: 20px;">
                    <div class="form-group">
						<drop-down :items="keys" :selected="params.key"
							@selected="onKeySelected">
						</drop-down>
					</div>
				</div>
                
                <div  align="right" class="col-sm-3" style="margin-top: 20px;">
                   
                </div>
            </div>

            <hr/>

            <discount-table :model="model" :can_edit="can_edit"
               @saved="fetchData" @remove="beginDelete">
            </discount-table>
            

        </div>


        <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
            @close="deleteConfirm.show=false" @confirmed="deleteDiscount">        
        </delete-confirm>
    </div> 
</template>


<script>
    import DiscountTable from '../../components/discount/table';
    export default {
        name:'DiscountIndexView',
        components: {
            'discount-table':DiscountTable,
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
                title: '折扣管理',

                loaded:false,

                model:null,
                
                params:{
                  
                    key:'',
                    page:1,
                    pageSize:10
                },

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
                    this.params.page=this.init_model.pageNumber;
                    this.params.pageSize=this.init_model.pageSize;
            }
                
            this.setKey(this.key_val);
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
            
            onKeySelected(item){
                this.setKey(item.value);
                this.fetchData();
            },
            setKey(val){
                this.params.key=val;
            },
            fetchData() {
                    
                let getData = Discount.index(this.params);

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            beginDelete(discount){
                let name=discount.name;
                let id= discount.id;

                this.deleteConfirm.msg='確定要刪除 ' + name + ' 嗎?'
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;                
            },
            deleteDiscount(){
             
                let id = this.deleteConfirm.id; 
                let remove=Discount.remove(id);
                
                this.deleteConfirm.show=false;

                remove.then(() => {
                    Helper.BusEmitOK('刪除成功');
                    this.fetchData();
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                   
                })

                   
                
            },
        }
    }
</script>





