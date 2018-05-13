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
                    <a v-if="can_edit" @click.prevent="create=true" href="#" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i> 新增
                    </a>   
                </div>
            </div>

            <hr/>

            <identity-table :model="model" :can_edit="can_edit" :create="create"
               @cancel-create="create=false" @saved="fetchData" >
            </identity-table>
            

        </div>
    </div> 
</template>


<script>
    import IdentityTable from '../../components/identity/table';
    export default {
        name:'IdentityIndexView',
        components: {
            'identity-table':IdentityTable,
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
            ps:{
                type:Object,
                default:null
            },
            version:{
                type:Number,
                default:0
            },
        },
        data(){
            return {
                title: '身分管理',

                loaded:false,

                model:null,

                create:false,
              
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            this.model={...this.init_model };
        },
        computed:{
           
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            fetchData() {
                this.create=false;
                    
                let getData = Identity.index();

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onCreate(){
                this.create=true;
            }
            
        }
    }
</script>





