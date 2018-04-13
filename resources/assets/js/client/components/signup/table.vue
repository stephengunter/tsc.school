<template>
    <table class="table">
        <thead>
            <tr>
                <th style="width:10%">
                   
                </th>
                <th style="width:15%">
                    報名日期
                </th>
                
                <th>
                    報名課程
                </th>
                <th style="width:20%">

                </th>
            </tr>
        </thead>
        <tbody  style="font-size:17px"  >
            <tr v-for="(signup,index) in signups" :key="index">
                <td v-html="$options.filters.signupStatusLabel(signup.status)" ></td>
              
                <td> {{ signup.date }} </td>    
                <td > 
                    <a @click.prevent="edit(signup)" href="#" v-html="courseNames(signup)"></a>
                  
                </td>  
              
                <td >
                    <button v-if="signup.canPay" class="button is-primary" @click.prevent="onPay(signup)">
                        <span class="icon is-small" style="vertical-align:middle;">
                            <i class="fa fa-check"></i> 
                        </span>
                        <span>繳費</span>
                        
                    </button>
                    <button v-if="signup.canDelete" class="button is-danger" @click.prevent="onDelete(signup)">
                        <span class="icon is-small" style="vertical-align:middle;">
                            <i class="fa fa-trash"></i> 
                        </span>
                        <span>刪除</span>
                        
                    </button>
                </td>
            </tr>
            
        </tbody>
    
    </table>
</template>

<script>
    
    export default {
        name:'SignupTable',
        props: {
            signups: {
               type: Array,
               default: null
            }
        },
        data() {
            return {
               

                

              
            }
        },
        computed:{
           
            
           
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
               
            },
            courseNames(signup){
                return  Signup.courseNames(signup);
            },
            onDelete(signup){
               
                this.$emit('remove',signup);   
            },
            edit(signup){
                this.$emit('edit',signup);   
            },
            onPay(signup){
               
                this.$emit('pay',signup);   
            },

            
        }
    }
</script>
