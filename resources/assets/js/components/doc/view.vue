<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div>
                <button v-show="can_back"  @click="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
            </div>
        </div> 
        <div class="panel-body">
            <edit :id="id" @loaded="onLoaded"
            @saved="onSaved"   @cancel="onEditCanceled" >                 
            </edit>
        </div> 
    </div>

   
</div>
</template>
<script>
   
    import Edit from './edit.vue'
    export default {
        name:'DocView',
        components: {
            Edit,
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                icon:'' ,
                readOnly:true,

                doc:null,

               
            }
        },
        computed:{
            creating(){
               
                if(this.id)  return false;
                return true;
            },
            title(){
                let text='文件下載';
               
               
                if(this.creating) return this.icon + ' 新增' + text;

                return this.icon + ' 編輯'+ text;
                
            },
           
        },
        beforeMount(){
            this.init()
        },
        watch: {
          
        },
        methods: {
            init() {
                
            },
            onBack(){
                this.$emit('back');
            },
            onLoaded(doc){
                this.doc = {
					...doc
				};
            },
            onEditCanceled(){
                this.$emit('back');
            },
           
            onSaved(){
                this.$emit('saved');    
            }  
        }
    }
</script>
