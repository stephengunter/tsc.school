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
            
            <div class="row" style="margin-top: 1em;">
                
                <div class="col-sm-6" >
                    <button v-if="loading" class="btn btn-default">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                    </button>
                    
                    <div v-else>
                        <label  :disabled="loading" class="btn  btn-success btn-file" @click="init">
                            <i class="fa fa-upload"></i>
                                匯入
                            <input :disabled="loading" type="file"  ref="fileinput"  name="file" style="display: none;"  
                            @change="onFileChange" >
                        </label>

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
        name: 'SignupImport',
        data() {
            return {
                title: Menus.getIcon('signups')  + '  匯入報名資料',
                
                loading:false,

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

            
           
        },
        methods: {
            init(){
               this.$refs.fileinput.value = null;
               this.err_msg='';
               this.loading=false;
            },
            onBack(){
                this.$emit('cancel');
            },
            onFileChange(e) {
               
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)  return;
                   
                this.files = e.target.files;

                this.submitImport();
               
            },
            submitImport() {
                this.loading=true;

                let form = new FormData();
                for (let i = 0; i < this.files.length; i++) {
                    form.append('file', this.files[i]);
                    
                }
               

                let store=Signup.import(form)
                store.then(result => {
                       
                        Helper.BusEmitOK();
                        this.loading=false;
                        //this.$emit('imported');
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
           
            
            
        },

    }
</script>