<template>
   
    <div v-if="model" >
        <div class="row">
            
            <div class="col-sm-10 form-inline" style="margin-top: 20px;">
               
                
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
                    <drop-down :items="courseOptions" :selected="params.course"
                        @selected="onCourseSelected">
                    </drop-down>
                </div>
                <div  class="form-group" style="padding-left:1em;">
                    <toggle :items="reviewedOptions"   :default_val="params.reviewed" @selected="setReviewed"></toggle>
                    
                </div>
                <div  class="form-group" style="padding-left:1em;">
                    <datetime-picker :date="params.beginDate" :can_clear="true" placeholder="日期起"
                        @selected="setBeginDate">
                    </datetime-picker>
                </div>
                <div  class="form-group" style="padding-left:1em;">
                    <datetime-picker :date="params.endDate" :can_clear="true" placeholder="日期止"
                        @selected="setEndDate">
                    </datetime-picker>
                </div>
            </div>
            
            <!-- <div v-if="showReviewBtn" class="col-sm-1 pull-right" align="right" style="margin-top: 20px;">

                <a v-if="canReview" @click.prevent="onReviewOk" :disabled="!canSubmitReview"  href="#" class="btn btn-success">
                    <i class="fa fa-check-circle"></i>
                    審核通過
                </a>
                
            </div> -->
            <div class="col-sm-2 pull-right" style="margin-top: 20px;">
               
                <a v-show="hasBeginDate" @click.prevent="onInitByDate" href="#" class="btn btn-warning btn-sm">
                 自動產生
                </a>
                <a v-if="showReviewBtn" @click.prevent="onReviewOk" :disabled="!canSubmitReview"  href="#" class="btn btn-sm btn-success">
                   
                    審核通過
                </a>
            </div>
        </div>

        <hr/>    
        
        <lesson-table :model="model" :can_review="showReviewBtn" ref="lessonsTable"
            @selected="onSelected" @check-changed="onCheckIdsChanged">
            <div v-show="model.totalItems > 0" slot="table-footer" class="panel-footer pagination-footer">
                <page-controll   :model="model" >
                    
                </page-controll>
        
            </div>
        </lesson-table>
        
        

    </div>

        
        
   
</template>


<script>
    import LessonTable from '../../components/lesson/table';
    
    export default {
        name:'LessonIndexView',
        components: {
            'lesson-table':LessonTable
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
            terms:{
                type:Array,
                default:null
            },
            centers:{
                type:Array,
                default:null
            },
            courses:{
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
                title: Menus.getIcon('lessons')  + '  課堂紀錄',
               
                loaded:false,

                model:null,

                courseOptions:[],
                
                params:{
                    term:'0',
                    center:'0',
                    course:'0',
                    reviewed:false,
                    beginDate:'',
                    endDate:''
                },
              
                canReview:false,
                reviewedOptions:Helper.reviewedOptions(),

                checkedIds:[]
            }
        },
        watch: {
            'version':'fetchData',
	    },
        beforeMount() {
            if(this.init_model){
                this.model={...this.init_model };
            }

            this.courseOptions=this.courses.slice(0);
            this.params.term=this.terms[0].value;
            this.params.center=this.centers[0].value;
            	
            this.canReview=this.can_review;	 	
        },
        computed:{
            hasCourse(){
                return Helper.tryParseInt(this.params.course) > 0;
            },
            hasBeginDate(){
                if(this.params.beginDate) return true;
                return false;
            },
            showReviewBtn(){
                
                if(this.params.reviewed) return false;
                if(!this.params.center) return false;
                return true;
            },
            canSubmitReview(){
                return this.checkedIds.length > 0;
            },
            
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onSelected(id){
               this.$emit('selected',id);
            },
            onTermSelected(item){
                this.params.term=item.value;
                this.params.course='0';
                this.fetchData()
            },
            onCenterSelected(item){
                this.params.center = item.value;
                this.params.course='0';
                this.fetchData();
            },
            onCourseSelected(item){
                this.params.course = item.value;
                this.fetchData();
            },
            setReviewed(val) {
                this.params.reviewed = val;
                this.fetchData();
            },
            setBeginDate(val){
                this.params.beginDate=val;
                this.fetchData();
            },
            setEndDate(val){
                this.params.endDate=val;
                this.fetchData();
            },
            onInitByDate(){
                let center=this.params.center;
                let date=this.params.beginDate;
                let form=new Form({
                    center:center,
                    date:date
                });
                let init=Lesson.init(form);
                init.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.checkedIds=[];
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})

                
            },
            fetchData() {
              
                let params={
                    ...this.params
                };
                if(!params.beginDate) params.beginDate='';
                
                let getData = Lesson.index(params);

                getData.then(model => {

                    this.model={ ...model.model };
                    this.courseOptions=model.courseOptions.slice(0);

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

                let lessons= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       reviewed:true
                   };
                })

                let form=new Form({
                    lessons:lessons
                });

                let save=Lesson.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.checkedIds=[];
                    this.$refs.lessonsTable.unCheckAll();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            
        }
    }
</script>





