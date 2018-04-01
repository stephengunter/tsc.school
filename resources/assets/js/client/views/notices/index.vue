<template>
<div>
    <h1 class="title">{{ title }}</h1>

    <div >

        <notice-table :model="model" @selected="onSelected" >
            <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                <pager></pager>
        
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
                 
                    keyword:'',
                    page:1,
                    pageSize:10
                },
            }
        },
        computed: {
           
        },
        methods:{
            
            onSelected(id){
               alert(id);
            },
        },
        
    }
</script>