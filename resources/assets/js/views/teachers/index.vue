<template>
   <div>
        <div>
            <div class="row">
                <div class="col-sm-2" style="margin-top: 3px;"> 
                    <h3 v-html="title">
                   </h3>
                </div>
                <div class="col-sm-5 form-inline" style="margin-top: 20px;">
                    <div class="form-group" style="padding-left:1em;">
						<toggle :items="groupOptions"   :default_val="params.group" @selected="setGroup"></toggle>
					</div>
                    <div  class="form-group" style="padding-left:1em;">
						<drop-down :items="centers" :selected="params.center"
							@selected="onCenterSelected">
						</drop-down>
					</div>
					
					<div class="form-group" style="padding-left:1em;">
                       <toggle  v-if="!isGroup" :items="reviewedOptions"   :default_val="params.reviewed" @selected="setReviewed"></toggle>
                        
					</div>
					
					
				</div>
               
                <div class="col-sm-3" style="margin-top: 20px;">
                    <searcher @search="onSearch">
                    </searcher>
                </div>
                <div v-if="showReviewBtn" class="col-sm-1"  style="margin-top: 20px;">
                    <a v-if="canReview " v-show="!isGroup" :disabled="!canSubmitReview"  @click.prevent="onReviewOk" href="#" class="btn btn-success">
                        <i class="fa fa-check-circle"></i>
                        審核通過
                    </a> 
                    
                </div>
                <div  :class="showReviewBtn ? 'col-sm-1' : 'col-sm-2'" align="right" style="margin-top: 20px;">
                
                  
                    <drop-down :items="actions" :selected="action" btn_style="primary"
                        @selected="onActionSelected">
                    </drop-down>
                    
                </div>
                
                
            </div>

            <hr/>
            <div v-if="model">
                <group-table v-if="isGroup" :model="model" @selected="onSelected" >
                    
                    <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                        <page-controll   :model="model" @page-changed="onPageChanged"
                            @pagesize-changed="onPageSizeChanged">
                        </page-controll>
                
                    </div>
                </group-table>

                <teacher-table v-else ref="teachersTable"  :model="model" :can_review="canReview && !params.reviewed" :center="center!=null"
                    @selected="onSelected" @check-changed="onCheckIdsChanged">

                    <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                        <page-controll   :model="model" @page-changed="onPageChanged"
                            @pagesize-changed="onPageSizeChanged">
                        </page-controll>
                
                    </div>
                </teacher-table>
            </div> 
            

        </div>

        
        
    </div> 
</template>


<script>
    import TeacherTable from '../../components/teacher/table';
    import TeacherGroupTable from '../../components/teacher/group-table';
    
    export default {
        name:'TeacherIndexView',
        components: {
            'teacher-table':TeacherTable,
            'group-table':TeacherGroupTable,
        },
        props: {
            init_model: {
                type: Object,
                default: null
            },
            can_review:{
                type:Boolean,
                default:false
            },
            can_import:{
                type:Boolean,
                default:false
            },
            centers:{
                type:Array,
                default:null
            },
            version:{
                type:Number,
                default:0
            },
        },
        data(){
            return {
                title: Menus.getIcon('teachers')  + '  教師管理',

                loaded:false,

                model:null,
                
                params:{
                    group:false,
                    reviewed:true,
                  
                    center:'0',

                    keyword:'',
                    page:1,
                    pageSize:999
                },

                canReview:false,

                groupOptions:Teacher.groupOptions(),
                reviewedOptions:Helper.reviewedOptions(),
                
                center:null,

                checkedIds:[],

                action:'none'
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            
            if(this.init_model){
                this.model={...this.init_model };
                this.params.page=this.init_model.pageNumber;
                this.params.pageSize=this.init_model.pageSize;
            }  

            this.canReview=this.can_review;
            	
        },
        computed:{
            showImportBtn(){
                return this.can_import;
            },
            actions(){
                let actions=[{
                    value:'none' , text:'執行'
                }];
                actions.push({
                    value:'create' , text:'新增'
                });

                if(this.showImportBtn){
                    actions.push({
                      value:'import' , text:'匯入'
                   });
                }


                return actions;
            },
            isGroup(){
                return this.params.group;
            },
            showReviewBtn(){
                if(this.params.reviewed) return false;
                if(this.isGroup) return false;
                if(!this.center) return false;
                return true;
            },
            canSubmitReview(){
                return this.checkedIds.length > 0;
            }
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            beginImport(){
                this.$emit('import');
            },
            onCreate(){
                this.$emit('create');
            },
            onSelected(id){
               this.$emit('selected',id , this.isGroup);
            },
            onActionSelected(item){
                let action=item.value;
                if(action=='create') this.onCreate();
                else if(action=='import') this.beginImport();

               
            },
            onReviewedSelected(item){
                this.params.reviewed=item.value;
                this.fetchData();
            },
            onSearch(keyword){
				this.params.keyword=keyword;
				this.fetchData();
			},
            onPageChanged(page){
				this.params.page=page;
				this.fetchData();
            },
            onPageSizeChanged(){
                
                this.params.pageSize=this.model.pageSize;
				this.fetchData();
            },
            onCenterSelected(center){
                this.setCenter(center);
                this.fetchData();
            },
            setGroup(val){
                this.params.group=val;
                this.$emit('group-changed', this.isGroup);
                this.fetchData();
            },
            setCenter(center){
                if(parseInt(center.value)>0){
                    this.center={ ...center };
                    this.params.center = center.value;
                }else{
                    this.center=null;
                    this.params.center = '0';
                }
                
               
            },
            setReviewed(val) {
               
                this.params.reviewed = val;
                this.fetchData();
            },
            fetchData() {
                this.model=null;
                let params={
                    group:this.params.group,
                    center:this.params.center,

                    keyword:this.params.keyword,
                    page:this.params.page,
                    pageSize:this.params.pageSize,
                };
                if(this.isGroup) params.active =this.params.active;
                else  params.reviewed=this.params.reviewed;

                let getData = Teacher.index(params);

                getData.then(data => {

                    this.model={ ...data.model };

                    this.canReview=data.canReview;

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onCheckIdsChanged(ids){
               
                this.checkedIds=ids.slice(0);
            },
            onReviewOk(){
                
                if(!this.checkedIds.length) return;

                let teachers= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       reviewed:true
                   };
                })

                let form=new Form({
                    teachers:teachers
                });

                let save=Teacher.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.checkedIds=[];
                    this.$refs.teachersTable.unCheckAll();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        }
    }
</script>





