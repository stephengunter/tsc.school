<template>
    <div v-if="form">
        <quit-details :quit_details="form.details" :percents_options="percents_options"  :editting_id="edittingId"
           @cancel-edit="edittingId=0" @edit="beginEditRow">
        </quit-details>
        <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
            <quit-inputs :form="form" :payway_options="paywayOptions"></quit-inputs>
            <submit-buttons :form="form" :submitting="submitting" :error_text="errorText"
                @cancel="cancel">

            </submit-buttons>
        </form>
    </div>
</template>

<script>
import QuitDetails from './details.vue';
import QuitInputs from './inputs.vue';
import SubmitButtons from './submit-buttons.vue';
export default {
    name:'EditQuit',
    props: {
        id:{
            type: Number,
            default: 0
        },
        percents_options:{
            type: Array,
            default: null
        },
	},
    components: {
		'quit-details':QuitDetails,
		'quit-inputs':QuitInputs,
        'submit-buttons':SubmitButtons
    },
    data(){
		return {
			
            paywayOptions:[],
			form:null,

            edittingId:0,
            
            submitting:false,

            errorText:''
			
		}
	},
    computed:{
		
	},
    beforeMount() {
		this.init();
	},
    methods:{
		init(){

            let fetchData=Quit.edit(this.id);
            fetchData.then(data => {
                    this.form = new Form({
                        quit:{
                            ...data.quit
                        },
                        details:data.quit.details.slice(0)
                    })
                    
					this.paywayOptions=data.paywayOptions.slice(0);
				})
				.catch(error => {
					
					Helper.BusEmitError(error);
				})

        },
        cancel(){
			this.$emit('cancel');
        },
        beginEditRow(item){
            this.errorText='';
            this.edittingId = parseInt(item.signupDetailId);
        },
		setDate(val){
			this.form.quit.date=val;
        },
        onPaywaySelected(item){
            this.form.quit.paywayId=item.value;
        },
		onSubmit(){
            this.errorText='';
            this.submitting=true;


            let validDetails=0;
            this.form.details.forEach(item=>{
                if(Helper.tryParseInt(item.percents) > 0) validDetails+=1;
            })

           

            if(validDetails<1){
                this.errorText='您沒有填寫任何一條退費比例';
                this.submitting=false;
                return;
            }

          
            this.submitQuit();
            
			
        },
        submitQuit(){
            let save= Quit.update(this.id,this.form);
			save.then(() => {
					this.submitting=false;
                    this.$emit('saved');
					Helper.BusEmitOK('資料已存檔');
				})
				.catch(error => {
					this.submitting=false;
					Helper.BusEmitError(error,'存檔失敗');
				})
        },
		clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

