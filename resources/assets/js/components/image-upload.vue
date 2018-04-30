<template>  
      <div class="form-group">
            <div class="imageupload panel panel-default">
                
                <div class="file-tab panel-body">
                    <form id="form-upload"  v-if="!hasImage" method="post" enctype="multipart/form-data" action="" >
  
                        <label class="btn btn-info btn-file">
                            <span>選擇檔案</span>
                        
                            <input type="file" id="image_file" name="image_file" @change="onFileChange">
                        
                        </label>
                    </form>
                    <img v-show="hasImage" class="thumbnail" style="max-width:200px;max-height:200px" :src="image" />
                    <button v-show="hasImage" @click="submitImage" :enable="submitting"  class="btn btn-success" type="button" id="submit-imgurl" >確認送出</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   
                    <button v-show="hasImage" @click="removeImage" :enable="submitting"  type="button" class="btn btn-danger" >刪除</button>
                </div>
              
            </div>

        </div>
   
 

</template>


<script>
    export default {
        props: {
            width: {
              type: Number,
              default: 0
            },
            height: {
              type: Number,
              default: 0
            },
            type:{
               type: String,
               default: ''
            }, 
            user_id:{
               type: Number,
               default: 0
            }  
        },
        data() {
            return {
                image: '',
                files: [],
                title: '',
                description: '',
                err: [],
                submitting: false
            }
        },
        computed:{
            hasImage(){
                if (this.image) {
                    return true;
                }
                return false;
            }

        },
        methods: {
            onFileChange(e) {
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)  return;
                   
                this.files = e.target.files;

                this.createImage(files[0]);
            },
            createImage(file) {
                var image = new Image();
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.image = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            removeImage(e) {
                this.image = ''
                this.files = []
                this.title = ''
                this.description = ''
            },
            submitImage() {
                this.submitting = true
                var form = new FormData(document.querySelector('form-upload'));
                
                form.append('width',this.width);
                form.append('height',this.height);
                form.append('type',this.type);
                if(this.user_id){
                    form.append('user_id',this.user_id);
                }
                

                for (let i = 0; i < this.files.length; i++) {
                    form.append('image_file', this.files[i]);
                }

                let store=Photo.store(form);
                store.then(photo => {
                        this.$emit('uploaded', photo);
                        this.removeImage();
                        this.submitting = false;
                    })
                    .catch(error => {
                        this.removeImage();
                        Helper.BusEmitError(error,'上傳失敗');
                        this.submitting = false;
                    })
            }
        }

    }
</script>


<style>
    .imageupload .alert,
    .imageupload .file-tab .thumbnail {
        margin-bottom: 10px
    }
    
    .imageupload.imageupload-disabled {
        cursor: not-allowed;
        opacity: .6
    }
    
    .imageupload.imageupload-disabled>* {
        pointer-events: none
    }
    
    .imageupload .panel-title {
        margin-right: 15px;
        padding-top: 8px
    }
    
    .imageupload .btn-file {
        overflow: hidden;
        position: relative
    }
    
    .imageupload .btn-file input[type=file] {
        cursor: inherit;
        display: block;
        font-size: 100px;
        min-height: 100%;
        min-width: 100%;
        opacity: 0;
        position: absolute;
        right: 0;
        text-align: right;
        top: 0
    }
    
    .imageupload .url-tab .thumbnail {
        margin: 10px 0
    }
</style>