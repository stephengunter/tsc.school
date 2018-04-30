<template> 
    <modal v-if="isDelete" :show.sync="show" @ok="onDeletePhotoConfirmed"  @closed="cancelDeletePhoto" ok-text="確定"
        effect="fade" :width="deleteModal.width">
          <div slot="modal-header" class="modal-header modal-header-danger">
            <h3><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 警告</h3>
        </div>
        <div slot="modal-body" class="modal-body">
            <h3> 確定要刪除相片？</h3>
        </div>
    </modal> 
    <modal  v-else :showbtn="editor.showbtn"  title="上傳圖片"  :show.sync="show" 
        @closed="cancelEditPhoto" effect="fade" :width="editor.width">
      
        <div slot="modal-body" class="modal-body">
            <image-upload :width="200" :height="200" :user="user_id" @uploaded="onPhotoUploaded"></image-upload>
        </div>
    </modal>
                
</template>
<script>

    export default {
        name: 'PhotoEditor',
        props: {
            user_id: {
               type: Number,
               default: 0
            },
            entity_type:{
               type: String,
               default: ''
            },
            entity_id: {
               type: Number,
               default: 0
            },
            action:{
               type: String,
               default: 'upload'
            },
            show:{
               type: Boolean,
               default: false
            }
        },
        watch: {
           
        }, 
        data() {
             return {
                editor:{
                    showbtn:false,
                    width:800
                },

                deleteModal:{
                    showbtn:false,                  
                },

            }
        },
        computed:{
            isDelete(){
                return this.action.toLowerCase() == 'delete'
            }

        },
        beforeMount() {
            this.init()
        },  
        methods: {    
            init(){
                

              
            },   
           
            cancelEditPhoto(){
                this.$emit('canceled')
            },
            cancelDeletePhoto(){
                this.$emit('canceled')
            },
            onDeletePhotoConfirmed(){
                this.updatePhoto()                
            },
            onPhotoUploaded(photo) {
                this.updatePhoto(photo)  
            },
            updatePhoto(photo){
               
                let photoId = 0
                if(photo){
                    photoId = photo.id
                }

                let updatePhoto=null

                let entity_type=this.entity_type.toLowerCase()
                if(entity_type=='user'){
                    let userId = this.entity_id
                    updatePhoto=User.updateUserPhoto(userId, photoId)
                }else if(entity_type=='center'){
                    let centerId = this.entity_id
                    updatePhoto=Center.updatePhoto(centerId, photoId)
                }
                
                
                updatePhoto.then(result => {
                    this.$emit('photo-updated', photoId)
                })
                .catch(error => {
                    this.$emit('photo-update-failed', photoId)
                                         
                })
            },
           
            

            
        }
    }
</script>