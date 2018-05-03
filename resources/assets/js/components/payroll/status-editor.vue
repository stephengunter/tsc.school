<template>
    <modal :showBtn="true"  :show.sync="showing" @ok="onConfirmed"  @closed="onClose" ok-text="確定"
        effect="fade" width="600">
            <div slot="modal-header" class="modal-header modal-header-danger">
                <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onClose">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
                <h3> 更新教師鐘點費狀態 </h3>
            </div>
        <div slot="modal-body" class="modal-body">
           
            <select v-model="selectedValue"  class="form-control">
                <option v-for="(item,index) in statusOptions" :key="index" :value="item.value" v-text="item.text" >
                </option>
            </select>
        </div>
    </modal>
        
</template>
<script>
    export default {
        name:'PayrollStatusEditor',
        props: {
           
            showing:{
                type: Boolean,
                default: true
            },  
            status:{
                type: Number,
                default: 0
            } 
            
        },
        data() {
            return {
                statusOptions : Payroll.statusOptions(),
                selectedValue:0
            }
        },
        watch: {
            'status':'init',
	    },
        beforeMount() {
           this.init(this.status);
        },
        methods: {
            init(status){
                this.selectedValue=status;
            },
            onClose(){
                this.$emit('close');
            },
            onConfirmed(){
                this.$emit('save',this.selectedValue);
            }
            
        }
    }
</script>
    