<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading ">  
            <h4 v-html="title"></h4>
            <div>
                <button  @click="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
            </div>
        </div> <!--  panel  heading -->
        <div  class="panel-body">
         
            <div class="row">
                <div class="col-sm-4" >
                    <button v-if="loading" class="btn btn-default">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                    </button>
                    <div v-else>
                        <label v-if="can_import" :disabled="loading" class="btn  btn-success btn-file" @click="init('import')">
                            <i class="fa fa-upload"></i>
                                匯入
                            <input :disabled="loading" type="file"  ref="fileinput"  name="file" style="display: none;"  
                            @change="onFileChange" >
                        </label>

                        <label :disabled="loading" class="btn  btn-success btn-file" @click="init('upload')">
                            <i class="fa fa-upload"></i>
                               上傳資料
                            <input :disabled="loading" type="file"  ref="fileinput"  name="file" style="display: none;"  
                            @change="onFileChange" >
                        </label>
                        <a class="btn btn-primary" @click.prevent="onDownload">
                            <i class="fa fa-download"></i>
                            下載範例檔
                        </a>

                    </div>
                    <small class="text-danger" v-if="hasError" v-text="err_msg"></small>
                </div>
            </div>
            

        </div> <!--  panel  body -->
       
    </div>  <!--  panel  -->

    <div v-show="areas">
        <h3>區域Id對照表</h3>
        <ul style="font-size:1.5em">
            <li v-for="(area,index) in areaOptions" :key="index">
                {{ area.text }}： {{ area.value }}
            </li>
        </ul>
       
    </div>

</div>   
</template>

<script>
    
    export default {
        name: 'CenterImport',
        props: {
            can_import:{
                type:Boolean,
                default:false
            },
            areas:{
               type: Array,
               default: null
            },
        },
        data() {
            return {
                title: Menus.getIcon('centers')  + '  匯入開課中心',
                action:'upload',
                loading:false,

                files: [],

                err_msg:''
               
            }
        },
        computed:{
            areaOptions(){
                return this.areas.filter(item=>{
                    if(item.value) return true;
                    return false;
                })
            },
            hasError(){
                if(this.err_msg) return true;
                return false;
            }
        },
        beforeMount() {
             
        },
        methods: {
            init(action){
               this.$refs.fileinput.value = null;
               this.err_msg='';
               this.loading=false;

               this.action=action;
            },
            onBack(){
                this.$emit('cancel');
            },
            onFileChange(e) {
               
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)  return;
                   
                this.files = e.target.files;

                if(this.action=='import')  this.submitImport();
                if(this.action=='upload')  this.submitUpload();
            },
            submitImport() {
                this.loading=true;

                let form = new FormData();
                for (let i = 0; i < this.files.length; i++) {
                    form.append('file', this.files[i]);
                    
                }

                let store=Center.import(form)
                store.then(result => {
                       
                        Helper.BusEmitOK();
                        this.loading=false;
                        this.$emit('imported');
                    })
                    .catch(error => {
                        
                        let msg =error.response.data.errors.msg[0];
                        if(msg){
                            this.err_msg=msg;
                            
                        }else{
                            Helper.BusEmitError(error);
                        }


                        this.loading=false
                    })
            },
            submitUpload() {
                this.loading=true;

                let form = new FormData();
                for (let i = 0; i < this.files.length; i++) {
                    form.append('file', this.files[i]);                    
                }

                form.append('type', this.getFileName());

                let store=Files.upload(form);
                
                store.then(result => {
                       
                        Helper.BusEmitOK();
                        this.loading=false;
                        this.$emit('imported');
                    })
                    .catch(error => {
                        
                        let msg =Helper.getErrorMsg(error);
                        if(msg){
                            this.err_msg=msg;
                            
                        }else{
                            Helper.BusEmitError(error);
                        }

                        this.loading=false
                    })
            },
            onDownload(){
              
                window.open(`/files/Download/${this.getFileName()}`);
                
            },
            getFileName(){
                return 'centers';
            }
           
            
            
        },

    }
</script>