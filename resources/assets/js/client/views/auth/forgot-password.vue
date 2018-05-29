<template>
<div>
    <div v-if="success" class="notification is-info" style="font-size:1.2em">
       請至您的電子信箱 {{ email }} 開啟重設密碼認證信.

       
    </div>
    <div v-else >
        <div class="columns">
            <div class="column">
                <h1 class="title">
                    請輸入您的身分證號和Email來啟動密碼重設程序
                </h1>
            </div>
            
            
        </div>
        <div class="columns">
            
            <div class="column is-vcentered register">
                <div class="box" style="max-width:600px">              
                    <form @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                        <div class="field">
                            <label class="label">身分證號</label>
                            <div class="control">
                                <input name="name" class="input" type="text" v-model="form.name" >
                                <p class="help is-danger" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></p> 
                            </div>
                            <label class="label">Email</label>
                            <div class="control">
                                <input name="email" class="input" type="text" v-model="form.email" >
                                <p class="help is-danger" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></p> 
                            </div>
                            
                            <div class="control" style="padding-top:1em">
                                
                                <a v-if="submitting" class="button is-loading">Loading</a>
                                <button v-else type="submit" class="button is-primary" >確認送出</button>
                                <p class="help is-danger" v-if="failed">啟動密碼重設失敗</p>
                            </div>
                        </div>
                    </form>
                </div>
                <ul style="list-style-type: disc;margin-left: 0; padding-left: 20px;">
                    <li >如果您沒有使用Email，請來電校本部由工作人員為您重設密碼。</li>
                </ul>
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
                    name:'',
                    email:''
                    
                }),

                email:'',

                success:false,

                failed:false,

                submitting:false
               
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
                if(!this.form.name) errors.name=['必須填寫身分證號'];
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

                
                this.submitting=true;

                this.email=this.form.email;
                
                let url='/password/email';
                this.form.post(url)
                .then(() => {
                    
                    this.success=true;
                    this.submitting=false;
                     
                }).catch(error => {
                    this.failed=true;
                    this.submitting=false;
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