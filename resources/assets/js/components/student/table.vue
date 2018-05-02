<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:5%" v-if="can_check">
                            <check-box v-show="dataCounts" :value="0" :default="checkAll"
							 @selected="onCheckAll" @unselected="unCheckAll">
							</check-box>
                        </th>
                        <th style="width:15%">姓名</th>
                        <th v-if="edittingScore" style="width:15%">分數
                            <button @click="onSubmitScores" class="btn btn-success btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-floppy-disk" ></span>
                            </button>
                            <button @click="cancelEditScore" class="btn btn-default btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-refresh"></span>
                            </button>
                        </th>
                        <th v-else style="width:15%">分數
                           
                            <button v-if="can_edit_score" @click.prevent="onEditScore" class="btn btn-primary btn-xs">
                                <span aria-hidden="true" class="glyphicon glyphicon-pencil"></span>
                            </button>
                        </th>
                        <th >Email</th>
                        <th style="width:10%">手機</th>
                        <th style="width:10%">缺席次數</th>
                        <th style="width:10%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(student,index) in model.viewList" :key="index">
                        <td v-if="can_check">
							<check-box :value="student.id" :default="beenChecked(student.id)"
								@selected="onChecked" @unselected="unChecked">
							</check-box>
                        </td>
                        <td>
                            <a href="#" @click.prevent="onSelected(student.id)" v-text="student.user.profile.fullname"> </a> 
                        </td>
                        <td v-if="edittingScore">
                            <input @keydown="clearErrorMsg(student)" type="text" name="student.score" class="form-control" v-model="student.score">
                            
                            <small class="text-danger" v-text="getError(student)"></small>
                        </td>
                        <td v-else>{{  student.score | formatMoney }}</td>
                       
                       
                        <td>{{  student.user.email }}</td>
                        <td>{{  student.user.phone }}</td>
                        <td>
                            {{  student.absenceCount }}
                        </td>
                        <td>
                            <span v-if="student.status < 0" class="label label-default"> 已退出 </span>
                        </td>
                        
                       
                        
                    </tr>    
                </tbody>

            </table>
        </div>
        <slot name="table-footer"> 
        
        </slot> 
            
    </div>
</template>

<script>
import Row from './row.vue'
export default {
    name:'StudentTable',
    components: {
        Row
    },
    props: {
        model: {
            type: Object,
            default: null
        },
        can_check: {
            type: Boolean,
            default: false
        },
        can_edit_score: {
            type: Boolean,
            default: false
        },
	},
	data() {
		return {
			checked_ids:[],
            checkAll: false,
            
            edittingScore:false,
            hasError:false,

            errors:[]
		};
	},
	computed:{
		
        dataCounts(){
            let viewList=this.getViewList();
            if(!viewList) return 0;
            return viewList.length;
        }
		
    }, 
	watch: {
		checked_ids() {
			this.$emit('check-changed',this.checked_ids);
		}
	},
    methods:{
        getViewList(){
			if(this.model) return this.model.viewList;
			return null;
        },
        onSelected(id){
           
           this.$emit('selected',id);
        },
        beenChecked(id){
            return this.checked_ids.includes(id);
		},
        onChecked(id){
				
			if(!this.beenChecked(id))  this.checked_ids.push(id);
		},
		unChecked(id){
				
			let index= this.checked_ids.indexOf(id);
			if(index >= 0)  this.checked_ids.splice(index, 1); 
				
		},
		onCheckAll(){
			this.checkAll=true;
			
			let studentList = this.getViewList();
			if(!studentList)  return false;

			studentList.forEach( student => {
				this.onChecked(student.id)
			});
		},
		unCheckAll(){
			this.checkAll=false;
			this.checked_ids=[];
        },
        onEditScore(){
            this.edittingScore=true;
        },
        cancelEditScore(){
            this.edittingScore=false;
            this.$emit('refresh');
        },
        getError(student){
            let index= this.errors.findIndex((item)=>{
               return item.id==student.id;
            });
            if(index >= 0) return this.errors[index].msg;
            return '';
        },
        clearErrorMsg(student){
           let id=student.id;
           let index= this.errors.findIndex((item)=>{
               return item.id==id;
           });


		   if(index >= 0)  this.errors.splice(index, 1); 
         
        },
        onSubmitScores(){
           
            this.errors=[];

            let students=this.getViewList();
            students.forEach((student)=>{
                let val=student.score
                if(isNaN(val)){
                 
                   this.errors.push({
                       id:student.id,
                       msg:'必須為數字'
                   });
                }
            });
           
            
            if(this.errors.length) return;

            this.updateScores()
        },
        updateScores(){
            let students = this.getViewList().map(item=>{
                return {
                            id:item.id,
                            score:item.score
                        }
            })

            
            let save=Student.updateScores(students)
            save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.edittingScore=false;
                    this.$emit('refresh');    
                })
                .catch(error => {
                    Helper.BusEmitError(error,'存檔失敗');
                    this.edittingScore=false;
                    this.$emit('refresh');   
                })
        },
        
   }
}
</script>

