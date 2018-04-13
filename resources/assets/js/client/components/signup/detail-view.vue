<template>
<div>
    <div class="panel">
        <div class="panel-heading panel-title heading" >
            {{ title }}
        </div>  
        <div class="panel-block">
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
                        <th v-if="can_edit" style="width:8%"></th>
                    </tr>
                </thead>
                <tbody>
                    
                    <row v-for="(item,index) in signup.details" :key="index"
                      :can_edit="canRemove" :model="item" 
                     
                      @remove="onDelete">
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
                return  this.signup.details.length > 1;

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
