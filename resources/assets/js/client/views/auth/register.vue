<template>
<div>
    <div class="columns register">
        <div class="column is-4">
            <h1 class="title">
            註冊 - 建立新帳號
            </h1>
            <div class="box">              
                <form @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                    <div class="field">
                        <label class="label">姓名</label>
                        <div class="control">
                            <input name="fullname" class="input" type="text" v-model="form.fullname" >
                            <p class="help is-danger" v-if="form.errors.has('fullname')" v-text="form.errors.get('fullname')"></p> 
                        </div>
                        <label class="label">身分證號</label>
                        <div class="control">
                            <input name="sid" class="input" type="text" v-model="form.sid" >
                            <p class="help is-danger" v-if="form.errors.has('sid')" v-text="form.errors.get('sid')"></p> 
                        </div>
                        <label class="label">密碼</label>
                        <div class="control">
                            <input name="password" class="input" type="password" v-model="form.password" >
                            <p class="help is-danger" v-if="form.errors.has('password')" v-text="form.errors.get('password')"></p> 
                        </div>
                        <label class="label">確認密碼</label>
                        <div class="control">
                            <input name="confirmation" class="input" type="password" v-model="form.confirmation" >
                            <p class="help is-danger" v-if="form.errors.has('confirmation')" v-text="form.errors.get('confirmation')"></p> 
                        </div>
                        <label class="label">生日</label>
                        <div class="control">
                           <datetime-picker :date="form.dob"  @selected="setDOB"></datetime-picker>
					       <p class="help is-danger" v-if="form.errors.has('dob')" v-text="form.errors.get('dob')"></p>
                        </div>
                        <label class="label">性別</label>
                        <div class="control">
                            <radio-group v-model="form.gender">
                                <radio val="1">男</radio>
                                <radio val="0">女</radio>
                            </radio-group>
                        </div>
                        <label class="label">聯絡電話</label>
                        <div class="control">
                            <input name="phone" class="input" type="text" v-model="form.phone" >
                            <p class="help is-danger" v-if="form.errors.has('phone')" v-text="form.errors.get('phone')"></p> 
                        </div>
                        <label class="label">Email</label>
                        <div class="control">
                            <input name="email" class="input" type="text" v-model="form.email" >
                            <p class="help is-danger" v-if="form.errors.has('email')" v-text="form.errors.get('email')"></p> 
                        </div>
                        <div class="control" style="padding-top:1em">
                            <button type="submit" class="button is-primary" >確認送出</button>
                            <p class="help is-danger" v-if="failed">建立新帳號失敗</p>
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
        name:'RegisterView',
        data(){
            return {
                form: new Form({
                    fullname:'',
                    sid:'',

                    email:'',
                    phone:'',
                  
                    gender:'1',
                    dob:'',

                    password:'',
                    confirmation:''
                    
                }),

                failed:false,
                

               
            }
        },
        beforeMount(){
            
        },
        methods: {
            setDOB(val){
                this.form.dob=val;
                this.clearErrorMsg('dob');
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name)
                this.failed = this.form.errors.any()
            },
            getErrors(){
                let errors={ };
                if(!this.form.sid) errors.sid=['必須填寫身分證號'];
                if(!this.form.fullname) errors.fullname=['必須填寫姓名'];
                if(!this.form.password) errors.password=['必須填寫密碼'];
                if(!this.form.confirmation) errors.confirmation=['必須填寫確認密碼'];
                if(!this.form.email) errors.email=['必須填寫Email'];
                if(!this.form.phone) errors.phone=['必須填寫聯絡電話'];
                if(!this.form.dob) errors.dob=['必須填寫生日'];

                return errors;
            },
            onSubmit(){
                this.form.errors.clear();
                let errors=this.getErrors();
                if(!Helper.isEmptyObj(errors)){
                    this.form.errors.record(errors);
                    return;
                }
                
                let url='/register';
                this.form.post(url)
                .then(() => {
                    
                    Bus.$emit('okmsg','成功建立新帳號');
                    this.redirect();
                     
                }).catch(error => {
                    this.failed=true
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