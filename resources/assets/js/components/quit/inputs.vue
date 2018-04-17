<template>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">                           
                    <label>日期</label>
                    <div>
                        <datetime-picker :date="form.quit.date" :can_clear="false" @selected="setDate"></datetime-picker>
                
                    </div>
                </div>
            </div>  
            
            <div class="col-sm-4">
                <div class="form-group">                           
                    <label>退款方式</label>
                    <drop-down :items="payway_options" :selected="form.quit.paywayId"
                        @selected="onPaywaySelected">
                    </drop-down>
                </div>
            </div>  

            <div class="col-sm-4">
                <div class="form-group">                           
                    <label>銀行帳號</label>
                    <input type="text" name="quit.account" class="form-control" v-model="form.quit.account">
                    <small class="text-danger" v-if="form.errors.has('quit.account')" v-text="form.errors.get('quit.account')"></small>
                </div>
            </div>  
        </div>
         
           
    </div>
</template>

<script>
export default {
    name:'QuitInputs',
    props: {
        form:{
            type: Object,
            default: null
        },
        payway_options:{
            type: Array,
            default: null
        }
	},
    data(){
		return {
			
		}
	},
    computed:{
		
	},
    beforeMount() {
		
	},
    methods:{
		
		setDate(val){
			this.form.quit.date=val;
        },
        onPaywaySelected(item){
            this.form.quit.paywayId=item.value;
        },
		clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

