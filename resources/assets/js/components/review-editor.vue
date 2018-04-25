<template>
    <modal :showBtn="true"  :show.sync="showing" @ok="onConfirmed"  @closed="onClose" ok-text="確定"
        effect="fade" width="600">
            <div slot="modal-header" class="modal-header modal-header-danger">
                <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onClose">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
                <h3> 更新審核狀態 </h3>
            </div>
        <div slot="modal-body" class="modal-body">
           
            <toggle :items="reviewedOptions"   :default_val="reviewed" @selected=onSelected></toggle>
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
            reviewed:{
                type: Boolean,
                default: false
            } 
            
        },
        data() {
            return {
                reviewedOptions : Helper.reviewedOptions(),
                selectedValue:false
            }
        },
        watch: {
            'active':'init',
	    },
        beforeMount() {
           this.init(this.reviewed);
        },
        methods: {
            init(reviewed){
                this.selectedValue=reviewed;
            },
            onClose(){
                this.$emit('close');
            },
            onConfirmed(){
                this.$emit('save',this.selectedValue);
            },
            onSelected(val){
                this.selectedValue=val;
            }
            
        }
    }
</script>
    