<template>
   
    <div v-if="center" class="panel-body show-data">
        <div class="row">
            <div class="col-sm-3">
                <!-- <img v-show="photo.path" :src="photo.path"  :class="class_name" :alt="title" > -->
            </div>
            <div class="col-sm-9"> 
                <div class="row">
                    <div class="col-sm-4">
                        <label class="label-title">名稱</label>
                        <p> {{ center.name }} </p>
                    </div> 
                    <div class="col-sm-4">
                        <label class="label-title">代碼</label>
                        <p> {{ center.code }} </p>
                    </div>
                    <div class="col-sm-4">
                        <label class="label-title">狀態</label>
                        <p v-html="$options.filters.activeLabel(center.active)">                       
                        </p>
                        
                    </div>
                </div>    <!-- End Row -->     
                <div class="row">
                    <div class="col-sm-4">
                        <label class="label-title">分類</label>
                        <p> {{ center.keyText }} </p>
                    </div> 
                    <div v-if="!isOverSea" class="col-sm-4">
                        <label class="label-title">區域</label>
                        <p> {{ center.area.name }} </p>
                    </div> 
                    <div class="col-sm-4">
                        <label class="label-title">課程洽詢電話</label>
                        <p> {{ center.courseTel }} </p>
                    </div> 
                    
                   
                    
                </div>  <!-- End Row -->  
            </div>     
        </div>   
    </div>  <!-- End panel-body-->
    
</template>

<script>
    export default {
        name: 'ShowCenter', 
        props: {
            center: {
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
        },
        data() {
            return {

            }
        },
        computed:{
            isOverSea(){
               
                if(this.center) return this.center.key == 'oversea' ;
                return false;
            }
           
        }, 
        methods: {  
            
            
        }
    }
</script>
