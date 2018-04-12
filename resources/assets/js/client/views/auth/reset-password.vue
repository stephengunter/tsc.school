<template>
    
    <div  class="columns is-vcentered register">
        <div class="column is-4">
            <h1 class="title">
            重設您的密碼
            </h1>
            <div class="box">              
                <form @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                    <div class="field">
                       
                        <label class="label">Email</label>
                        <div class="control">
                            <input name="email" class="input" type="text" v-model="form.email" >
                            <p class="help is-danger" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></p> 
                        </div>
                        <label class="label">密碼</label>
                        <div class="control">
                            <input name="password" class="input" type="password" v-model="form.password" >
                            <p class="help is-danger" v-if="form.errors.has('password')" v-text="form.errors.get('password')"></p> 
                        </div>
                        <label class="label">確認密碼</label>
                        <div class="control">
                            <input name="password_confirmation" class="input" type="password" v-model="form.password_confirmation" >
                            <p class="help is-danger" v-if="form.errors.has('password_confirmation')" v-text="form.errors.get('password_confirmation')"></p> 
                        </div>
                        
                        <div class="control" style="padding-top:1em">
                            <button type="submit" class="button is-primary" >確認送出</button>
                            <p class="help is-danger" v-if="form.errors.has('token')" v-text="form.errors.get('token')"></p> 
                            <a v-if="form.errors.has('token')" href="/password/reset">重新開始密碼重設流程</a>
                        </div>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</template>


<script>
    export default{
        name:'ResetPasswordView',
        props: {
            token: {
                type: String,
                default: ''
            }
        },
        data(){
            return {
                form: new Form({
                    token:'',
                    email:'',
                    password:'',
                    password_confirmation:''
                    
                }),
               

                failed:false,
               
            }
        },
        beforeMount(){
          
        },
        methods: {
            clearErrorMsg(name) {
                this.form.errors.clear(name)
                this.failed = this.form.errors.any()
            },
            getErrors(){
                let errors={ };
                if(!this.form.email) errors.email=['必須填寫Email'];
               

                return errors;
            },
            onSubmit(){
                
                this.form.errors.clear();
                this.failed = false;

                let errors=this.getErrors();
                if(!Helper.isEmptyObj(errors)){
                    this.form.errors.record(errors);
                    return;
                }

               this.form.token = this.token;
                
                let url='/password/reset';
                this.form.post(url)
                .then(() => {
                    Bus.$emit('okmsg','密碼重設成功');
                    this.redirect();
                     
                }).catch(error => {

                    this.failed=true;
                })
            },
            redirect(){
              
                 window.location='/';
            }
            
        }
        



    }
</script>

<style scoped>
   
   .register{
     font-size: 16px;
   }
</style>