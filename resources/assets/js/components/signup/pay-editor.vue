<template>
    <modal :showbtn="false"  :show.sync="showing" @closed="onClose" 
        effect="fade" width="600">
            <div slot="modal-header" class="modal-header modal-header-danger">
                <button id="close-button" type="button" class="close" data-dismiss="modal" @click="onClose">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
                <h3> 編輯繳費紀錄 </h3>
            </div>
        <div slot="modal-body" class="modal-body">
            <form v-if="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form-horizontal">
                <div class="form-group">
                    <label class="col-md-2 control-label">日期</label>
                    <div class="col-md-4">
                       <datetime-picker :date="form.payDate" :can_clear="false" @selected="setDate"></datetime-picker>
                
                    </div>
                   
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">繳費金額</label>
                    <div class="col-md-4">
                       
                        <input readonly type="text" name="amount" class="form-control" v-model="form.amount"  >
                       
                    </div>
                   
                </div>
                
                <div class="form-group">
                    <label class="col-md-2 control-label">繳費方式</label>
                    <div class="col-md-4">
                       
                        <select  v-model="form.paywayId"  name="paywayId" class="form-control" >
                            <option v-for="(item,index) in payways" :key="index" :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                   
                </div>
			
            
                <div class="form-group">
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
                        <button @click.prevent="cancel" class="btn btn-default">
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
        name:'SignupPayEditor',
        props: {
            signup:{
                type: Object,
                default: null
            },
            payways:{
                type: Array,
                default: null
            },
            showing:{
                type: Boolean,
                default: true
            },
            
        },
        data() {
            return {
               
              
                form:null,
            
                submitting:false,
            }
        },
        beforeMount() {
            
        },
        methods: {
            
            init(){
               
                let fetchData=Pay.create(this.signup.id);
                fetchData.then(data => {
                    this.form = new Form({...data });

                    this.form.amount=Helper.formatMoney(data.amount);
                    
                    this.form.paywayId=this.payways[0].value
				})
				.catch(error => {
					
					Helper.BusEmitError(error);
				})
               
            },
            setDate(val){
                if(!this.form) return;
               
                this.form.payDate=val;
            },
            cancel(){
                this.$emit('close');
            },
            onClose(){
                this.$emit('close');
            },
            onSubmit(){
                
                let save=Pay.store(this.form);
				save.then(() => {
                    this.$emit('saved');
                    Helper.BusEmitOK('資料已存檔');
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            onSelected(val){
                this.selectedValue=val;
            }
            
        }
    }
</script>
    