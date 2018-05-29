<template>
    <div>
        <div class="columns" >
			<div class="column" >
				<label class="label label-title">姓名</label>
											
				<div class="control">
					<input name="user.profile.fullname" v-model="form.user.profile.fullname" class="input" type="text" >
					<p class="help is-danger" v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')"></p>
				</div>
				
			</div>
        	<div class="column" >
				<label class="label label-title">Email</label>
				
				<div class="control">
					<input name="user.email" v-model="form.user.email"  class="input" type="text" >
					<p class="help is-danger" v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')"></p>
				</div>
        	</div>
			<div class="column" >
				<label class="label label-title">電話</label>
				
				<div class="control">
					<input name="user.phone" v-model="form.user.phone"  class="input" type="text" >
					<p class="help is-danger" v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')"></p>
				</div>
			</div>

    	</div>   <!--  End Row -->
		<div class="columns" >
			<div class="column" >
				<label class="label label-title">性別</label>
											
				<div class="control">
					<radio-group v-model="form.user.profile.gender">
						<radio val="1">男</radio>
						<radio val="0">女</radio>
					</radio-group>
				
				</div>
				
			</div>
        	<div class="column" >
				<label class="label label-title">身分證號</label>
				
				<div class="control">
					<input name="user.profile.sid" v-model="form.user.profile.sid"  class="input" type="text" >
					<p class="help is-danger" v-if="form.errors.has('user.profile.sid')" v-text="form.errors.get('user.profile.sid')"></p>
				</div>
        	</div>
			<div class="column" >
				<label class="label label-title">生日</label>
				
				<div class="control">
					<datetime-picker :date="form.user.profile.dob"  @selected="setDOB"></datetime-picker>
					<p class="help is-danger" v-if="form.errors.has('user.profile.dob')" v-text="form.errors.get('user.profile.dob')"></p>
				</div>
			</div>

    	</div>   <!--  End Row -->
        
    </div>
    
    
</template>

<script>

export default {
    name:'ProfileInputs',
    props: {
        form: {
            type: Object,
            default: 0
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
		init(){
			
		},
		setGender(val){
			this.form.user.profile.gender=val;
		},
		setDOB(val){
			this.form.user.profile.dob=val;
			this.clearErrorMsg('user.profile.dob');
        },
        clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

