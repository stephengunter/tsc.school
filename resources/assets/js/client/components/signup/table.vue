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
                <th style="width:10%">

                </th>
            </tr>
        </thead>
        <tbody  style="font-size:17px"  >
            <tr v-for="(signup,index) in signups" :key="index">
                <td v-html="$options.filters.signupStatusLabel(signup.status)" ></td>
              
                <td> {{ signup.date }} </td>    
                <td v-html="courseNames(signup)"> 
                    
                  
                </td>  
              
                <td >
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

            
        }
    }
</script>
