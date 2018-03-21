<template>

    <table class="table table-striped">
        <tbody>
            <tr v-for="(center,index) in model.viewList" :key="index">
                
                <td>{{  center.code }}</td>
               
                <td>
                    <button class="btn btn-sm btn-default" v-if="can_order" @click.prevent="up(center,index)">
                        <i class="fa fa-arrow-up" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-sm btn-default" v-if="can_order"  @click.prevent="down(center,index)">
                        <i class="fa fa-arrow-down" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>    
        </tbody>

    </table> 
</template>

<script>
    export default {
        name: 'ShowTeacher', 
        props: {
            teacher: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {

            }
        },
        computed:{
            
            hasReviewedBy(){
                if(!this.teacher) return false;
                if(!this.teacher.reviewedBy) return false;
                return true;
            }
        }, 
        methods: { 
            editReview(){
                this.$emit('edit-review')
            }
            
        }
    }
</script>
