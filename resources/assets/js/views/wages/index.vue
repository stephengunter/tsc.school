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
                   
                </div>
            </div>

            <hr/>

            <wage-table :model="model" :can_edit="can_edit"
               @saved="fetchData" >
            </wage-table>
            

        </div>

        <div class="row">
            <ul style="font-size:1.2em">
                <li> 
                   夜間課程定義：{{ ps.night }}
                </li> 
                <li> 
                   大型課程定義：西部 - {{ ps.bigWest }}
                </li> 
                <li> 
                   大型課程定義：東部 - {{ ps.bigEast }}
                </li>         
            </ul>  
        </div>
       
    </div> 
</template>


<script>
    import WageTable from '../../components/wage/table';
    export default {
        name:'WageIndexView',
        components: {
            'wage-table':WageTable,
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
                title: '教師薪酬標準',

                loaded:false,

                model:null,
              
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
                    
                let getData = Wage.index();

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            
        }
    }
</script>





