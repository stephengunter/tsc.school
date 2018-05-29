<template>

    <div>
    
        <table  class="table" style="font-size:1.2em">
            <tbody>
                <tr v-for="(notice,index) in model.viewList" :key="index">
                    <td>{{ notice.date }}</td>
                    <td>
                        <a :href="`notices/${notice.id}`">
                            {{ notice.title }}
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
        name:'NoticeTable',
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

