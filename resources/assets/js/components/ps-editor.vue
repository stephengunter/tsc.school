<template>
    <modal :showBtn="true"  :show.sync="showing" @ok="onConfirmed"  @closed="onClose" ok-text="確定"
        effect="fade" width="600">
            <div slot="modal-header" class="modal-header">
                <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onClose">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
                <h3> 備註 </h3>
            </div>
        <div slot="modal-body" class="modal-body">
            <textarea rows="6" cols="50" class="form-control" name="teacher.description"  v-model="ps">
            </textarea>
        </div>
    </modal>
        
</template>
<script>
    export default {
        name:'ReviewEditor',
        props: {
            showing:{
                type: Boolean,
                default: true
            },  
            text:{
                type: String,
                default: ''
            },  
            
        },
        data() {
            return {
                ps:''
            }
        },
        beforeMount() {
            this.ps = this.text;
        },
        methods: {
            onClose(){
                this.$emit('close');
            },
            onConfirmed(){
                this.$emit('save',this.ps);
            }
           
        }
    }
</script>
    