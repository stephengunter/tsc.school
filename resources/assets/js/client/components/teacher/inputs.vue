<template>
    <div>
        <div class="columns" >
			<div class="column" >
				<label class="label label-title">專長</label>
											
				<div class="control">
					<input name="teacher.specialty" v-model="form.teacher.specialty" class="input" type="text" >
					<p class="help is-danger" v-if="form.errors.has('teacher.specialty')" v-text="form.errors.get('teacher.specialty')"></p>
				</div>
				
			</div>
        	<div class="column" >
				<label class="label label-title">最高學歷</label>
				
				<div class="control">
					<input name="teacher.education" v-model="form.teacher.education"  class="input" type="text" >
					<p class="help is-danger" v-if="form.errors.has('teacher.education')" v-text="form.errors.get('teacher.education')"></p>
				</div>
        	</div>
			<div class="column" >
				
			</div>

    	</div>   <!--  End Row -->
		<div class="columns" >
			<div class="column is-one-third" >
				<label class="label label-title">經歷</label>
											
				<div class="control">
					<textarea rows="5"  class="textarea" name="teacher.experiences"  v-model="form.teacher.experiences">
                	</textarea>
					
					<p class="help is-danger" v-if="form.errors.has('teacher.experiences')" v-text="form.errors.get('teacher.experiences')"></p>
				</div>
				
			</div>
        	<div class="column" >
				<label class="label label-title">個人簡介</label>
				
				<div class="control">
					<textarea rows="5"  class="textarea" name="teacher.description"  v-model="form.teacher.description">
                	</textarea>
					
					<p class="help is-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></p>
				</div>
        	</div>
			

    	</div>   <!--  End Row -->
        
    </div>
    
    
</template>

<script>

export default {
    name:'TeacherInputs',
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
        clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

