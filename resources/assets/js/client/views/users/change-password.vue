<template>
<div>
    <div class="columns is-vcentered register">
        <div class="column is-4 is-offset-4">
            <h1 class="title">
            變更密碼
            </h1>
            <div class="box">              
                <form @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                    <div class="field">
                        <label class="label">舊密碼</label>
                        <div class="control">
                            <input name="current_password" class="input" type="password" v-model="form.current_password" >
                            <p class="help is-danger" v-if="form.errors.has('current_password')" v-text="form.errors.get('current_password')"></p> 
                        </div>
                        <label class="label">新密碼</label>
                        <div class="control">
                            <input name="password" class="input" type="password" v-model="form.password" >
                            <p class="help is-danger" v-if="form.errors.has('password')" v-text="form.errors.get('password')"></p> 
                        </div>
                        <label class="label">確認新密碼</label>
                        <div class="control">
                            <input name="password_confirmation" class="input" type="password" v-model="form.password_confirmation" >
                            <p class="help is-danger" v-if="form.errors.has('password_confirmation')" v-text="form.errors.get('password_confirmation')"></p> 
                        </div>
                       
                        <div class="control" style="padding-top:1em">
                            <button type="submit" class="button is-primary" >確認送出</button>
                            
                            <p class="help is-danger" v-if="failed">變更密碼失敗</p>
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
        name:'ChangePasswordView',
        data(){
            return {
                form: new Form({
                    current_password:'',
                    password:'',
                    password_confirmation:'',
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
                if(!this.form.current_password) errors.current_password=['必須填寫舊密碼'];
                if(!this.form.password) errors.password=['必須填寫新密碼'];
                if(!this.form.password_confirmation) errors.password_confirmation=['必須填寫確認新密碼'];

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
                
                let url='/user/password/change';
                this.form.post(url)
                .then(() => {
                    
                    Bus.$emit('okmsg');
                    this.redirect();
                     
                }).catch(error => {
                    this.failed=true;
                })
            },
            redirect(){
                 window.location='/';
            }
            
        },



    }
</script>

<style scoped>
   
   .register{
     font-size: 16px;
   }
</style>