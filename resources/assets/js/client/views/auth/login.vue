<template>
<div>
    <div class="columns register" >
        <div class="column is-4">
            <h1 class="title">
            登入
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
                        <div class="control">
                            <checkbox v-model="remember" val="true" >在這台電腦記住我</checkbox>
                           
                        </div>
                        <div class="control" style="padding-top:1em">
                            <button type="submit" class="button is-primary" >確認送出</button>
                            
                            <p class="help is-danger" v-if="failed">登入失敗</p>
                        </div>
                    </div>
                </form>
            </div>
            <p class="has-text-centered" style="margin-top: 20px;">
                還沒有帳號?&nbsp;  
                <a href="/register" >註冊</a>&nbsp;|&nbsp;<a href="/password/reset">忘記密碼</a> 
              
              
            </p>    
        </div>
    </div>
    
</div>    
</template>


<script>
    export default{
        name:'LoginView',
        props: {
            intend: {
                type: String,
                default: '/'
            }
        },
        data(){
            return {
                form: new Form({
                    email:'',
                    password:'',
                    remember:0
                }),

                remember:false,

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
                if(!this.form.password) errors.password=['必須填寫密碼'];

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

                this.form.remember=Helper.isTrue(this.remember);
                
                let url='/login';
                this.form.post(url)
                .then(() => {
                    
                    Bus.$emit('okmsg','您已成功登入');
                    this.redirect();
                     
                }).catch(error => {
                    this.failed=true;
                })
            },
            redirect(){
                 window.location=this.intend;
            }
            
        },



    }
</script>

<style scoped>
   
   .register{
     font-size: 16px;
   }
</style>