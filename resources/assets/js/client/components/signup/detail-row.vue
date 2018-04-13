<template>
    
    <tr>
        <td> {{ model.course.center.name }}</td>
        <td> {{ model.course.number }}</td>    
        <td> {{ model.course.fullName }}</td>  
        <td v-html="$options.filters.classTimesHtml(model.course)">  
        <td>{{ model.course.beginDate }}  ~  {{ model.course.endDate }}</td>
        <td> {{ model.course.tuition | formatMoney }}  </td>
        <td> {{ model.course.cost | formatMoney }}  </td>
        <td v-if="can_edit" >
            <button @click.prevent="onRemove" class="button is-small is-danger" >
                <span class="icon is-small" style="vertical-align:middle;">
                    <i class="fa fa-trash"></i> 
                </span>
                <span>刪除</span>
                
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
              
                this.$emit('remove',this.model);
            },
                
        }
    }
</script>
