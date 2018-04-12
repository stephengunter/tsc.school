<template>

    <div>
        <div class="panel">
            <div class="panel-heading panel-title heading" >
                
                {{ title }}
                
                <div style="float: right">
                    <a href="#" v-show="readOnly" class="button is-info is-outlined" @click.prevent="beginEdit">
                        <span class="icon is-small" style="vertical-align:middle;">
                            <i class="fa fa-edit"></i> 
                        </span>
                        <span>
                        修改    
                        </span>
                        
                        
                    </a>
                </div>
               
               
                 
            </div>
            <div class="panel-block">
                <show v-if="readOnly"  :user="user" >  
                </show>
                <edit v-else :id="userId" :role="role"
                    @saved="onSaved"   @cancel="onEditCanceled" >                 
                </edit>
		    </div>
   

        </div>
    </div>   
  
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'
    export default {
        name:'Profile',
        components: {
            Show,
            Edit,
        },
        props: {
            model:{
                type: Object,
                default: null
            },
            id: {
                type: Number,
                default: 0
            },
            role:{
                type: String,
                default: ''	
            },
            can_edit:{
                type: Boolean,
                default: true
            },            
            can_back:{
                type: Boolean,
                default: true
            },
            title_text:{
                type:String,
                default:'個人資訊'
            },
            version: {
                type: Number,
                default: 0
            },
        },
        data() {
            return {
                
                readOnly:true,

                user:null,
            }
        },
        computed:{
            canEdit(){
                if(!this.user) return false;

                if(this.model){
                    return this.can_edit;
                }else{
                    if(!this.can_edit) return false;
                    return this.user.canEdit;
                }
            },
            canDelete(){
                if(!this.canEdit) return false;

                if(!this.user) return false;

                if(this.model){
                    return this.can_delete;
                }else{
                    if(!this.can_delete) return false;
                    return this.user.canDelete;
                }
            },
            userId(){
                if(this.user) return this.user.id;
                return 0;
            },
            title(){
               
                if(this.readOnly) return  this.title_text;
                return '編輯' + this.title_text;
            }
           
        },
        beforeMount(){
            if(this.model){
                this.loadModel();
            }else{
                this.fetchData();
            } 
        },
        watch: {
            model: {
                handler: function () {
                
                   this.init();
                },
                deep: true
            },
        },
        methods: {
            init() {

                this.readOnly=true;
                
            },
            loadModel(){
                
                this.user={
                    ...this.model
                };
            },
            fetchData() {
               
                let getData=User.show();
               
                getData.then(user => {
                   
                    this.user = {
                        ...user
                    }; 

                    this.$emit('loaded',this.user);
                })
                .catch(error=> {
                    
                    Helper.BusEmitError(error)
                })
            }, 
             
            onBack(){
                this.$emit('back');
            },
            beginEdit() {
                this.readOnly=false;
            },
            onEditCanceled(){
                this.readOnly=true;
            },
            onSaved(){
                this.fetchData();
                this.init();
            }, 

            
        }
    }
</script>
