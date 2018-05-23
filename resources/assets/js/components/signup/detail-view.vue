<template>
<div >
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
           
            <div >
                <button v-if="can_edit" @click.prevent="onAdd" class="btn btn-primary btn-sm" >
                    <i class="fa fa-plus-circle"></i> 
                    新增
                </button>
               
            </div>
        </div>  
        <div class="panel-body">
            <table  class="table">
                <thead>
                    <tr>
                        <th style="width:10%">
                            開課中心
                        </th>
                        <th style="width:10%">
                            課程編號
                        </th>
                        <th style="width:15%">
                            課程名稱
                        </th>
                        <th style="width:15%">
                            上課時間
                        </th>
                        <th >
                            課程日期
                        </th>
                        <th style="width:10%">
                            學費
                        </th>
                        <th style="width:10%">
                            材料費
                        </th>
                        <th style="width:10%">
                            備註
                        </th>
                        <th v-if="can_edit" style="width:8%"></th>
                    </tr>
                </thead>
                <tbody>
                    
                    <row v-for="(item,index) in signup.details" :key="index"
                      :can_edit="canRemove" :model="item" 
                     
                      @delete="onDelete">
                    </row>
                </tbody>
            
            </table>
        </div>
            
        
    </div>

    
</div>
</template>
<script>
    import Row from './detail-row.vue';
    export default {
        name:'SignupDetails',
        components: {
            Row
        },
        props: {
            model: {
               type: Object,
               default: null
            },
            text:{
               type: String,
               default: ''
            },
            can_edit:{
               type: Boolean,
               default: true
            }
        },
        data() {
            return {
               

                signup:null,

              
            }
        },
        computed:{
           
            canRemove(){
                if(!this.can_edit) return false;
                if(!this.signup) return false;
                return  true;

            },
            title(){
                if(this.text) return this.text;
                return this.icon + ' 報名課程';
            }
           
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
                this.signup={
                    ...this.model
                };
                this.creating=false;
            },
            onAdd(){
                this.$emit('add-detail');
            },
            onDelete(item){
                this.$emit('remove-detail',item);   
            },

            
        }
    }
</script>
