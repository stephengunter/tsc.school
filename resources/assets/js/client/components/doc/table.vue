<template>

    <div>
    
        <table  class="table" style="font-size:1.2em">
            <tbody>
                <tr v-for="(doc,index) in model.viewList" :key="index">
                    
                    <td>
                        <a :href="doc.url">
                            {{ doc.title }}
                        </a>
                    </td>
                </tr>
            
            </tbody>
            
        </table>
        <slot name="table-footer"> 
        
        </slot> 
        
    </div>
</template>

<script>
   
    export default {
        name:'DocTable',
        props: {
            model:{
                type: Object,
                default: null
            },
        },
        beforeMount(){
          
        },
        data(){
            return{
               
            }
        },
        computed: {
            
        },
        methods:{
            init(){
               
            },
            onSelected(id){
                this.$emit('selected',id);
            }
        },
        
    }
</script>

<style scoped>
a {
    color: #3273dc;
    cursor: pointer;
    text-decoration: none;
    transition: none 86ms ease-out;
}
</style>

