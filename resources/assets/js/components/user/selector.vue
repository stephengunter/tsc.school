<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span>
        </div>  
        <div class="panel-body">
            <user-table :model="model" 
                @selected="onSelected" @check-changed="onCheckIdsChanged">

                
            </user-table>
        </div>
        
    </div>
</template>


<script>
    import UserTable from './table';
    export default {
        name:'UserSelector',
        components: {
            'user-table':UserTable,
        },
        props: {
            model: {
                type: Object,
                default: null
            },
            text:{
                type:String,
                default:'選擇使用者'
            },
            multi:{
                type:Boolean,
                default:false
            },
        },
        data(){
            return {
                title: Menus.getIcon('users')  + '  ' + this.text,

                checkedIds:[]
            }
        },
        watch: {
           
	    },
        beforeMount() {
           
        },
        computed:{
           
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onSelected(id){
               this.$emit('selected',id);
            },
            onCheckIdsChanged(ids){
                this.checkedIds=ids.slice(0);
            },
            
        }
    }
</script>





