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
        </div> 
        <div  class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    
                    <div  class="form-group" >
                        <drop-down :items="centerOptions" :selected="center"
                            @selected="onCenterSelected">
                        </drop-down>
                        <small class="text-danger" v-if="!center">請選擇開課中心</small>
                    </div>
                    
                </div>
            </div>
            <div class="row" style="margin-top: 1em;">
                <div class="col-sm-4" >
                    
                    <toggle :items="groupOptions"   :default_val="group" @selected="setGroup"></toggle>
                </div>
                <div class="col-sm-6" >
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
            

        </div>
       
    </div>  

  

</div>   
</template>

<script>
    
    export default {
        name: 'TeacherImport',
        props: {
            can_import:{
                type:Boolean,
                default:false
            },
            centers:{
                type:Array,
                default:null
            },
        },
        data() {
            return {
                title: Menus.getIcon('teachers')  + '  匯入教師資料',
                
                loading:false,

                action:'upload',

              
                center:0,
                centerOptions:[],

                groupOptions:Teacher.groupOptions(),

                group:false,
                files: [],

                err_msg:''
               
            }
        },
        computed:{
            
            hasError(){
                if(this.err_msg) return true;
                return false;
            }
        },
        beforeMount() {

            this.centerOptions=this.centers.filter((item)=>{
                return Helper.tryParseInt(item.value) > 0;
            });
            this.center=this.centerOptions[0].value;
           
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
            setGroup(val){
                this.group=val;
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

                form.append('group', this.group);

                let store=Teacher.import(form)
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
                form.append('center', this.center);

                let store=Teacher.upload(form);
                
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

                        this.loading=false;
                    })
            },
            onTermSelected(term){
                this.term=term.value;
               
            },
            onCenterSelected(center){
                  this.center=center.value;
            },
            onDownload(){
              
                let url=Files.downloadUrl(this.getFileName());
                window.open(url);
            },
            getFileName(){
                if(this.group)  return 'group_teachers';
                return 'teachers';
            }
           
            
            
        },

    }
</script>