<template>

<div v-if="form">
    <div class="row">
        <div class="col-sm-3">
            <div class="text-center">
                <!-- <photo :id="photo_id"></photo>
                <h5>個人相片</h5>
                <button @click.prevent="editPhoto(1)" title="編輯相片" class="btn btn-info btn-xs">                                 
                    <span class="glyphicon glyphicon-pencil"></span>
                </button> 
                <button v-show="photo_id" @click.prevent="editPhoto(0)" type="button" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="刪除相片">
                    <span class="glyphicon glyphicon-trash"></span>
                </button> -->
                
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">                           
                <label>名稱</label>
                <input name="center.name" v-model="form.center.name" class="form-control" />
				<small class="text-danger" v-if="form.errors.has('center.name')" v-text="form.errors.get('center.name')"></small>
			</div>
            <div class="form-group">
                <label>所在地</label>
                <div>
                    <toggle :items="overseaOptions"   :default_val="form.center.oversea" @selected="setOversea"></toggle>
                </div>
            
            </div>
            
            
            
        </div>
        <div class="col-sm-3">
            
            <div class="form-group">
                <label>代碼</label>
                <input name="center.code" v-model="form.center.code" class="form-control" />
				<small class="text-danger" v-if="form.errors.has('center.code')" v-text="form.errors.get('center.code')"></small>
				
            </div>
            <div v-show="!form.center.oversea" class="form-group">
                <label>區域</label>

                <select v-model="form.center.areaId" class="form-control">
                    <option v-for="(item,index) in area_options" :key="index" :value="item.value" v-text="item.text" >
                    </option>
                </select>
            </div>
            
        </div>
        <div class="col-sm-3">
            
            <div class="form-group">
                <label>課程洽詢電話</label>

                <input name="center.courseTel" v-model="form.center.courseTel" class="form-control" />
                <small class="text-danger" v-if="form.errors.has('center.courseTel')" v-text="form.errors.get('center.courseTel')"></small>
            
            </div>
            <div class="form-group">
                <label>狀態</label>
                <div>
                    <toggle :items="activeOptions"   :default_val="form.center.active" @selected="setActive"></toggle>
                </div>
               
            </div>
            
        </div>
    </div>
    
                    
                
       
    
</div>  
</template>


<script>
    export default {
        name: 'CenterInputs',
        props: {
            form: {
                type: Object,
                default: null
            },
            area_options:{
                type: Array,
                default: null
            }
        },
        data() {
            return {
                overseaOptions:Center.overseaOptions(),
                activeOptions:Helper.activeOptions()
            }
        },
        computed:{
            
            
        },
        watch:{
           
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                
            },
            setOversea(val){
                this.form.center.oversea=val;
                if(!Helper.isTrue(val)){
                    let areaId=Helper.tryParseInt(this.form.center.areaId);
                   
                    if(areaId < 1){
                        this.form.center.areaId=this.area_options[0].value;
                    }
                }
            },
            setActive(val){
                this.form.center.active=val;
            },
            onCanceled(){
                this.$emit('canceled')
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
        }
    }
</script>