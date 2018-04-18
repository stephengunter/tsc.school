<template>
   
    <div v-if="model" >
        <div class="row">
            <div class="col-sm-2" style="margin-top: 3px;"> 
                <h3 v-html="title">
                </h3>
            </div>
            <div class="col-sm-6 form-inline" style="margin-top: 20px;">
                <div class="form-group" style="padding-left:1em;">
                    <drop-down :items="terms" :selected="params.term"
                        @selected="onTermSelected">
                    </drop-down>
                </div>
                <div  class="form-group" style="padding-left:1em;">
                    <drop-down :items="centers" :selected="params.center"
                        @selected="onCenterSelected">
                    </drop-down>
                </div>
                 <div  class="form-group" style="padding-left:1em;">
                    <drop-down :items="categories" :selected="params.category"
                        @selected="onCategorySelected">
                    </drop-down>
                </div>
                <div class="form-group" style="padding-left:1em;">
                    <toggle :items="reviewedOptions"   :default_val="params.reviewed" @selected="setReviewed"></toggle>
                    
                </div>
                
                
            </div>
            
            <div class="col-sm-3" style="margin-top: 20px;">
                <searcher @search="onSearch">
                </searcher>
            </div>
            
            <div v-if="showReviewBtn" class="col-sm-1 pull-right" align="right" style="margin-top: 20px;">

                <a v-if="canReview" @click.prevent="onReviewOk" :disabled="!canSubmitReview"  href="#" class="btn btn-success">
                    <i class="fa fa-check-circle"></i>
                    審核通過
                </a>
                
            </div>
            <div v-else class="col-sm-1 pull-right" align="right" style="margin-top: 20px;">
                <a v-if="showImportBtn"  @click.prevent="beginImport" href="#" class="btn btn-warning pull-right">
                    <i class="fa fa-upload"></i>
                    匯入
                </a>
            </div>
        </div>

        <hr/>
            
        

        <course-table ref="coursesTable"  :model="model" :can_review="canReview" :center="center!=null"
            @selected="onSelected" @check-changed="onCheckIdsChanged">
            <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                <page-controll   :model="model" @page-changed="onPageChanged"
                    @pagesize-changed="onPageSizeChanged">
                </page-controll>
        
            </div>
        </course-table>
        
        

    </div>

        
        
   
</template>


<script>
    import CourseTable from '../../components/course/table';
    
    export default {
        name:'CourseIndexView',
        components: {
            'course-table':CourseTable
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
            terms:{
                type:Array,
                default:null
            },
            centers:{
                type:Array,
                default:null
            },
            categories:{
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
                title: Menus.getIcon('courses')  + '  課程管理',


                model:null,
                
                params:{
                    term:'0',
                    center:'0',
                    category:'0',
                    reviewed:true,
                    keyword:'',
                    page:1,
                    pageSize:999
                },

                canReview:false,
                reviewedOptions:Helper.reviewedOptions(),
                
                center:null,

                checkedIds:[]
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

            this.params.term=this.terms[0].value;
            this.canReview=this.can_review;	
        },
        computed:{
           
            showImportBtn(){
                return this.can_import;
            },
            showReviewBtn(){
                if(this.params.reviewed) return false;
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
            onTermSelected(term){
                this.params.term=term.value;
                this.fetchData()
            },
            onCenterSelected(center){
                this.setCenter(center);
                this.fetchData();
            },
            onCategorySelected(category){
                this.params.category = category.value;
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
                
                let getData = Course.index(this.params);

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

                let courses= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       reviewed:true
                   };
                })

                let form=new Form({
                    courses:courses
                });

                let save=Course.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.$refs.coursesTable.unCheckAll();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        }
    }
</script>





