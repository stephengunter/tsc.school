<template>
    
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        
		<center-inputs :form="form" :area_options="areaOptions" :key_options="keyOptions">
		</center-inputs>
		
		
		<div v-if="submitting"  class="row">
            <div class="col-sm-3">
            </div>
			<div class="col-sm-4">
				<div class="form-group">                           
					<button class="btn btn-default">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                    </button> 
				</div>
			</div>
    	</div>
		<div v-else class="row">
            <div class="col-sm-3">
            </div>
			<div class="col-sm-4">
				<div class="form-group">   
					<button type="submit" class="btn btn-success">
						<i class="fa fa-save"></i>
						確認存檔
					</button> 
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button type="button" class="btn btn-default" @click.prevent="cancel">
						取消
					</button>  
				</div>
			</div>
    	</div>
        
    </form>
    
</template>

<script>
import CenterInputs from './inputs.vue'
export default {
    name:'EditCenter',
    props: {
        id: {
            type: Number,
            default: 0
        },
	},
	components: {
		'center-inputs':CenterInputs,
		
	},
	data(){
		return {

            form:null,

			areaOptions:[],
			keyOptions:[],
            
            submitting:false,
			
		}
	},
	computed:{
		
	},
	beforeMount() {
		this.init();
	}, 
	methods:{
		init(){
			this.fetchData();
		},
		cancel(){
			
			this.$emit('cancel');
		},
		fetchData(){
            let getData=Center.edit(this.id);

			getData.then(model => {
				
				this.form = new Form({
					center:{
						...model.center
					},
					
                });
                
				this.areaOptions=model.areaOptions.slice(0);
				this.keyOptions=model.keyOptions.slice(0);
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
		onSubmit(){
           
            let save=Center.update(this.id,this.form);

			save.then(center => {
                    this.$emit('saved');
					Helper.BusEmitOK('資料已存檔');
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
			
		},
		clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

