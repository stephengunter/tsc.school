<template>
<div>
    <h2>登入</h2>

    <div class="row">
        <div class="col-md-4">
            <section>
                <form  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                    <div class="form-group">
                        <label>Email</label>
                        <input name="email" class="form-control" v-model="form.email"/>
                        <small class="text-danger" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></small>
                    </div>
                    <div class="form-group">
                        <label asp-for="Password"></label>
                        <input type="password" name="password" class="form-control" v-model="form.password">
                        <small class="text-danger" v-if="form.errors.has('password')" v-text="form.errors.get('password')"></small>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">登入</button>
                        &nbsp;&nbsp;
                        <small class="text-danger" v-if="err" >登入失敗</small>
                    </div>
                    <hr />
                    <div class="form-group">
                        <p>
                            <a href="#" @click.prevent="forgotPassword">忘記密碼 ?</a>
                        </p>
                    
                    </div>
                </form>
            </section>
        </div>

    </div>
</div>   
</template>


<script>
    export default {
        name: 'ManageLoginView',
        beforeMount() {
           this.init()
        },
        data() {
            return {
                form:{},
                err:false
            }
        },
        methods: {
            init() {
                this.form=new Form({
                    email:'',
                    password:''
                })
            }, 
            forgotPassword(){
                alert('forgotPassword');
                this.$emit('forgot-password')
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
                this.err=false;
            },
            onSubmit(){
                let login=Auth.login(this.form);
                login.then(()=>{
                    Helper.BusEmitOK('登入成功');
                    this.$emit('logined');            
                }).catch(error=>{
                    this.err=true;
                    Helper.BusEmitError(error,'登入失敗');
                })
               
            },
            
            
            
           
        },

    }
</script>
