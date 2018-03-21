<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span>
            <div>
               
                <a v-if="canSubmit"  @click.prevent="onSubmit" href="#" class="btn btn-success">
                    <i class="fa fa-check-circle"></i>
                    確定送出
                </a>

            </div>
        </div>  
        <div class="panel-body">
            <teacher-table :model="model" :teachers="teachers" :center="center" 
                :can_checked="multi" :can_select="!multi"
                @check-changed="onCheckIdsChanged">
            </teacher-table>
        </div>
        
    </div>
</template>


<script>
    import TeacherTable from './table';
    export default {
        name:'TeacherSelector',
        components: {
            'teacher-table':TeacherTable,
        },
        props: {
            model: {
                type: Object,
                default: null
            },
            teachers:{
                type: Array,
                default: null
            },
            center:{
                type: Boolean,
                default: false    
            },
            text:{
                type:String,
                default:'選擇教師'
            },
            multi:{
                type:Boolean,
                default:true
            },
        },
        data(){
            return {
                title: Menus.getIcon('teachers')  + '  ' + this.text,

                checkedIds:[]
            }
        },
        watch: {
           
	    },
        beforeMount() {
           
        },
        computed:{
            
            canSubmit(){
               return this.checkedIds.length > 0;
            }
        }, 
        methods:{
            
            onCheckIdsChanged(ids){
                this.checkedIds=ids.slice(0);
            },
            onSubmit(){
                this.$emit('selected',this.checkedIds);
            },
            
        }
    }
</script>





