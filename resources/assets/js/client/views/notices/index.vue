<template>
<div>
    <h1 class="title">{{ title }}</h1>

    <div >

        <notice-table :model="model" >
            <div v-if="model" v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
               
                <pagination :total="model.totalItems" :page-size="model.pageSize" :current="params.page" layout="pager"
                 :change="onPageChanged">
                </pagination>
                
            </div>
        </notice-table> 
       
    </div>

</div>

</template>

<script>

    import NoticeTable from '../../components/notice/table.vue';
    export default {
        name:'NoticeIndexView',
        components:{
            'notice-table':NoticeTable,
        },
        props: {
            init_model: {
                type: Object,
                default: null
            }
        },
        beforeMount(){
            if(this.init_model){
                this.model={...this.init_model };
                this.params.page=this.init_model.pageNumber;
                this.params.pageSize=this.init_model.pageSize;
            }  

            

        },
        mounted(){
            this.$emit('loaded');
        },
        data(){
            return{
                title:'公告訊息',
                model:null,

                params:{
                    page:1,
                   
                },
            }
        },
        computed: {
           
        },
        methods:{
            fetchData() {
              
                let getData = Notice.index(this.params);

                getData.then(model => {

                    this.model={ ...model };

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onPageChanged(page){
               
				this.params.page=page;
				this.fetchData();
            },
        },
        
    }
</script>