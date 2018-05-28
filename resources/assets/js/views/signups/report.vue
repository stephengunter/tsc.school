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
                    <toggle :items="activeOptions"   :default_val="params.active" @selected="setActive"></toggle>
                    
                </div>
                
            </div>
            <div class="col-sm-2"> 
            
            </div>
            <div class="col-sm-2 pull-right" align="right" style="margin-top: 20px;">
                <a v-if="canReview" v-show="params.active" :disabled="!showReviewBtn" @click.prevent="onSubmit" href="#" class="btn btn-danger">
                    <i class="fa fa-times-circle"></i>
                    課程停開
                </a>
                
            </div>
        </div>

        <hr>

        <signup-report-table ref="coursesTable" :model="model" :can_review="canReview" :can_checked="canReview" 
             @check-changed="onCheckIdsChanged">
            
        </signup-report-table>
        
        

    </div>

        
        
   
</template>


<script>
    import SignupReportTable from '../../components/signup/report-table';
    export default {
        name:'SignupReportView',
        components: {
            'signup-report-table':SignupReportTable
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
        },
        data(){
            return {
                title: '學員統計',
               
                loaded:false,

                model:null,

                activeOptions:Course.activeOptions(),
                
                params:{
                    term:'0',
                    center:'0',
                    active:true
                },

               
              
                canReview:false,

                checkedIds:[]
            }
        },
        watch: {
           
	    },
        beforeMount() {
            if(this.init_model){
                this.model={...this.init_model };
              
            }  
           
            this.params.term=this.terms[0].value;
            this.params.center=this.centers[0].value;
            	
            this.canReview=this.can_review;	 	
        },
        computed:{
            
            showReviewBtn(){
               return this.checkedIds.length > 0;
            },
            
           
        }, 
        methods:{
            getList(){
                if(this.model) return this.model.viewList;
                return [];
            },
            onTermSelected(item){
                this.params.term=item.value;
                this.fetchData();
            },
            onCenterSelected(item){
                this.params.center = item.value;
                this.fetchData();
            },
            setActive(val){
                this.params.active =val;
                this.fetchData();
            },
            fetchData() {
                
                let getData = Signup.report(this.params);

                getData.then(data => {

                    this.model={ ...data.model };
                    this.canReview= data.canReview;

                })
                .catch(error => {
                    Helper.BusEmitError(error);
                
                })
            },
            onCheckIdsChanged(ids){
                this.checkedIds=ids.slice(0);
            },
            onSubmit(){
               
                if(!this.checkedIds.length) return;

                let courses= this.checkedIds.map((item)=>{
                   return {
                       id:item,
                       active:!this.params.active
                   };
                })

                let form=new Form({
                    courses:courses
                });

                let save=Course.active(form);
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





