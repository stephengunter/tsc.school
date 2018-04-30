<template>
    <label  class="btn btn-sm btn-warning btn-file">
        <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
            {{ text }}
        <input type="file" :id="input_id" :name="name" style="display: none;"  
           @change="onFileChange" >
    </label>
</template>

<script>
export default {
    name: 'FileUpload',
    props: {
        input_id: {
            type: String,
            default: 'file_upload_input'
        },
        name:{
            type: String,
            default: 'file[]'
        },
        text: {
            type: String,
            default: '檔案上傳'
        },
    },
    methods: {
        onFileChange(e) {
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)  return;

            let upload=File.upload(files);
            upload.then(data => {
                       this.$emit('file-uploaded' , data);
                    })
                    .catch(error => {
                         Helper.BusEmitError(error,'上傳失敗');
                    })
        },
    }
}
    


</script>

<style scoped>
  .btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
</style>