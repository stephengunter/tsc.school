<template>
    <table class="table" style="width: 95%;font-size:17px;">
        
        <tbody>
            <tr v-for="(item,index) in processes" :key="index">
                <td class="order-td" style="width:5%">
                    {{ item.order }}
                </td>
                <td style="width:25%">
                    {{  item.title  }}
                </td>
                <td>
                    {{  item.content  }}
                </td>
            </tr>              
        </tbody>
    </table>
</template>

<script>
    export default {
        name:'CourseProcesses',
        props: {
            processes:{
                type: Array,
                default: null
            },
        },
        computed: {
            hasData(){
                if(!this.processes) return false
                return this.processes.length > 0
            },
        },
    }
</script>


<style scoped>
.order-td{
    width: 10%;

}
  
</style>