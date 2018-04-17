<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="readOnly">
                <button v-if="canCreate" @click="beginCreate" class="btn btn-primary btn-sm" >
                    <i class="fa fa-plus"></i> 新增
                </button>
               
                <button v-if="canEdit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
                </button>
            </div>
        </div>  
        <div class="panel-body">
            <show :quit="signup.quit" :percents_options="percentsOptions" v-if="readOnly"  :signup="signup" >  
            </show>
            <div  v-else>
                <create :percents_options="percentsOptions" v-if="creating" :signup="signup"
                    @saved="onSaved"   @cancel="onEditCanceled" >                 
                </create>
                <edit :percents_options="percentsOptions" v-else :id="quitId" :signup_id="signup.id"
                    @saved="onSaved"   @cancel="onEditCanceled" >                 
                </edit>
            </div>
            
        </div>
        
    </div>
   
   
</div>
</template>
<script>
    import Create from './create.vue';
    import Show from './show.vue';
    import Edit from './edit.vue';
    export default {
        name:'Quit',
        components: {
            Create,
            Show,
            Edit,
           
        },
        props: {
            signup:{
                type: Object,
                default: null
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
                icon:Menus.getIcon('quits') ,
                readOnly:true,
               
                percentsOptions:Quit.percentsOptions(),

            }
        },
        computed:{
            canCreate(){
               
                if(!this.can_edit) return false;
                if(!this.signup)   return false;
               
                return !Signup.hasQuit(this.signup);
            },
            canEdit(){
                if(!this.can_edit) return false;
                if(!this.signup.quit)  return false;
                return true;
            },
            quitId(){
                if(!this.signup) return 0;
                if(!this.signup.quit) return 0;
                return this.signup.quit.signupId;
            },
            creating(){
                if(this.readOnly) return false;
                if(this.quitId)  return false;
                return true;
            },
            title(){
                let text='退費申請';
               
                if(this.readOnly) return text;
                if(this.creating) return '新增' + text;

                return '編輯退費申請';
                
            }
           
        },
        beforeMount(){
            this.init()
        },
        watch: {
            'version':'init'
        },
        methods: {
            init() {
                
            },
            beginCreate(){
                this.readOnly=false;
            },  
            beginEdit() {
                this.readOnly=false;
            },
            onEditCanceled(){
                this.readOnly=true;
            },
            onSaved(){
                this.$emit('saved');
            },  

            
        }
    }
</script>
