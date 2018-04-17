<template>
    <div>
        <quit-details :quit_details="quitDetails" :percents_options="percents_options"  :editting_id="edittingId"
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
    name:'CreateQuit',
    props: {
        signup:{
            type: Object,
            default: null
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

            quitDetails:[],
            
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
			this.quitDetails=this.signup.details.map(item=>{
                return {
                    'course':item.course,
                    'signupDetailId':item.id,
                    'percents' : '',
                    'tuition' : '',
                    'ps' : ''
                }
            });

            let fetchData=Quit.create();
            fetchData.then(data => {
                    this.form = new Form({
                        quit:{
                            ...data.quit
                        },
                        details:[]
                    })
                    let paywayExist=data.paywayOptions.find(item=>{
                        return Helper.tryParseInt(item.value)==Helper.tryParseInt(this.signup.bill.paywayId);
                    })
                    if(paywayExist) this.form.quit.paywayId=this.signup.bill.paywayId;
                    else this.form.quit.paywayId=data.paywayOptions[0].value;
                    
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
            this.edittingId = item.signupDetailId;
        },
		onSubmit(){
            this.submitting=true;

            let validDetails=this.quitDetails.filter(item=>{
                return  Helper.tryParseInt(item.percents) > 0;
            })

            if(!validDetails.length){
                this.errorText='您沒有填寫任何一條退費比例';
                this.submitting=false;
                return;
            }

            this.form.details=validDetails.slice(0);
            this.submitQuit();
            
			
        },
        submitQuit(){
            let save= Quit.store(this.form);
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

