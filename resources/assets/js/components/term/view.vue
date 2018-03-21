<template>

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
        </div>  <!-- End panel-heading-->
        <div class="panel-body">
            <edit :id="id" 
            @saved="onSaved"   @cancel="onCancel" >                 
            </edit> 
        </div>
        
    </div>
    
</template>
<script>
    
    import Edit from './edit.vue'
    export default {
        name:'Term',
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
            
        },
        data() {
            return {
                icon:Menus.getIcon('terms'),
                
            }
        },
        computed:{
            title(){
                if(this.id) return this.icon + ' 編輯學期設定';
                return this.icon + ' 新增學期設定';
            }
           
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
               

                
            },
            onBack(){
                this.$emit('back');
            },
            onCancel(){
                this.$emit('back');
            },
            onSaved(){
                this.$emit('saved');
            },  
            

            
        }
    }
</script>
