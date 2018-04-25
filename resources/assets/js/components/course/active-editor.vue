<template>
    <modal :showbtn="false"   :show.sync="showing"  @closed="onClose" 
        effect="fade" width="600">
        <div slot="modal-header" class="modal-header modal-header-danger">
            <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onClose">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
            </button>
            <h3> 修改課程狀態 </h3>
        </div>
        <div slot="modal-body" class="modal-body">
            <form  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-2 control-label">課程狀態</label>
                    <div class="col-md-4">
                        <toggle  :items="options"   :default_val="active" @selected=onSelected></toggle>
                       
                    </div>
                
                </div>
                
                <div v-if="!active" class="form-group">
                    <label class="col-md-2 control-label">退費比例</label>
                    <div class="col-md-4">
                    
                        <select v-model="form.percents" class="form-control">
                            <option v-for="(item,index) in percents_options" :key="index"  :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                
                </div>
            
            
                <div  class="form-group">
                    <label class="col-md-2 control-label"></label>
                    
                    <div v-if="submitting"  class="col-md-10">
                        <button class="btn btn-default">
                            <i class="fa fa-spinner fa-spin"></i> 
                            處理中
                        </button>
                    </div>
                    <div v-else class="col-md-10">
                        <button class="btn btn-success" type="submit">
                            <i class="fa fa-save"></i>
                            確認存檔
                        </button>
                        &nbsp;&nbsp;&nbsp;
                        <button @click.prevent="onClose" class="btn btn-default">
                            取消
                        </button>
                    </div>
                </div>
            
            </form>
            
        </div>
    </modal>
        
</template>
<script>
    export default {
        name:'CourseActiveEditor',
        props: {
            showing:{
                type: Boolean,
                default: true
            },  
            default_val:{
                type: Boolean,
                default: true
            },
            percents_options:{
               type: Array,
               default: null
            },
            
        },
        data() {
            return {
                options : Course.activeOptions(),
                active:false,

                courseId:0,
                amount:0,
                form:new Form({
                    percents:100
                }),
            
                submitting:false,
            }
        },
        watch: {
            'default_val':'init',
	    },
        beforeMount() {
           this.init(this.default_val);
           
        },
        methods: {
            init(active){
                this.active=active;
            },
            onClose(){
                this.$emit('close');
            },
            onConfirmed(){
               
                this.$emit('save',this.active);
            },
            onSelected(val){
                this.active=val;
            },
            onSubmit(){

                if(this.active){
                    this.$emit('active');
                   
                }else{
                    
                    this.$emit('shut-down',this.form);      
                } 
            },
            
        }
    }
</script>
    