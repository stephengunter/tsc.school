<template>
<div>
        
    <h2>變更密碼</h2>

    <div class="row">
        <div class="col-md-4">
            <form  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <div class="form-group">
                    <label>舊密碼</label>
                    <input type="password" name="current_password" class="form-control" v-model="form.current_password">
                    <small class="text-danger" v-if="form.errors.has('current_password')" v-text="form.errors.get('current_password')"></small>          
                    
                </div>
                <div class="form-group">
                    <label>新密碼</label>
                    <input type="password" name="password" class="form-control" v-model="form.password">
                    <small class="text-danger" v-if="form.errors.has('password')" v-text="form.errors.get('password')"></small>
                
                </div>  
                <div class="form-group">
                    <label>確認新密碼</label>
                    <input type="password" name="password_confirmation" class="form-control" v-model="form.password_confirmation">
                    <small class="text-danger" v-if="form.errors.has('password_confirmation')" v-text="form.errors.get('password_confirmation')"></small>
                
                </div>
                
            
                <div class="form-group">
                    <button type="submit" class="btn btn-success">確定</button>
                    &nbsp;&nbsp;
                    <small class="text-danger" v-if="err" >變更密碼失敗</small>
                </div>
                
            </form>
        </div>
    </div>
</div>

   
</template>

<script>
    export default {
        name: 'ChangePasswordView',

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
                    current_password:'',
                    password:'',
                    password_confirmation:'',
               })
            }, 
           
            clearErrorMsg(name) {
                this.form.errors.clear(name);
                this.err=false; 
            },
            onCancel(){
                this.init()
                this.$emit('canceled')
            },
            onSubmit() {
                this.submitForm()
            },
            submitForm(){
                let store=Auth.changePassword(this.form)
                
                store.then(() => {
                    Helper.BusEmitOK('變更密碼成功');
                    this.$emit('success');
                })
                .catch(error => {
                    this.err=true;
                    Helper.BusEmitError(error,'變更密碼失敗');
                   
                })
               
            }
            
            
           
        },

    }
</script>