<template>
<div>
    <div v-if="success" class="notification is-info" style="font-size:1.2em">
       請至您的電子信箱 {{ email }} 開啟重設密碼認證信.

       
    </div>
    <div v-else class="columns is-vcentered register">
        <div class="column is-6">
            <h1 class="title">
            請輸入您的Email來啟動密碼重設程序
            </h1>
            <div class="box">              
                <form @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input name="email" class="input" type="text" v-model="form.email" >
                            <p class="help is-danger" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></p> 
                        </div>
                        
                        <div class="control" style="padding-top:1em">
                            <button type="submit" class="button is-primary" >確認送出</button>
                            
                            <p class="help is-danger" v-if="failed">啟動密碼重設失敗</p>
                        </div>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
    
</div>    
</template>


<script>
    export default{
        name:'ForgotPasswordView',
        data(){
            return {
                form: new Form({
                    email:'',
                    
                }),

                email:'',

                success:false,

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

                this.email=this.form.email;
                
                let url='/password/email';
                this.form.post(url)
                .then(() => {
                    
                    this.success=true;
                     
                }).catch(error => {
                    this.failed=true;
                })
            }
            
        },



    }
</script>

<style scoped>
   
   .register{
     font-size: 16px;
   }
</style>