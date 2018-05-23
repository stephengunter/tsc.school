<template>
    
    <tr>
        <td> {{ model.course.center.name }}</td>
        <td> {{ model.course.number }}</td>    
        <td> {{ model.course.fullName }}</td>  
        <td v-html="$options.filters.classTimesHtml(model.course)">  
        <td>{{ model.course.beginDate }}  ~  {{ model.course.endDate }}</td>
        <td> {{ model.course.tuition | formatMoney }}  </td>
        <td> {{ model.course.cost | formatMoney }}  </td>
        <td> {{ model.ps }}  </td>
        <td v-if="can_edit" >
            <button @click.prevent="onRemove" class="btn btn-xs btn-danger">
                <i class="fa fa-trash"></i> 
            </button>
        </td>
    </tr>
</template>
<script>
    export default {
        name:'SignupDetailRow',
        props: {
            
            model:{
               type: Object,
               default: null
            },
            edit:{
               type: Boolean,
               default: false
            },
            can_edit:{
               type: Boolean,
               default: true
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
            onRemove(){
                
                this.$emit('delete',this.model);
            },
                
        }
    }
</script>
