<template>
<div>
    <h1 class="title">{{ title }}</h1>

    <div v-if="model" >
      
        
        <doc-table :model="model" >
           
        </doc-table> 
    </div>

</div>

</template>

<script>

    import DocTable from '../../components/doc/table.vue';
    export default {
        name:'DocIndexView',
        components:{
            'doc-table':DocTable,
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
            }  

            

        },
        mounted(){
            this.$emit('loaded');
        },
        data(){
            return{
                title:'下載專區',
                model:null,
            }
        },
        computed: {
           
        },
        methods:{
            
            
        },
        
    }
</script>