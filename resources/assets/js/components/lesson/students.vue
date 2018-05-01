<template>
    <table class="table table-striped" >
        <thead>
            <tr>
                <th style="width:10%"></th>
                <th style="width:20%">出席狀況</th>
                <th style="width:20%">姓名</th>
                <th>
                    備註
                </th>    
                
                
            </tr>
        </thead>
        <tbody>
            <row v-for="(member,index) in getStudents()" :edit="isEditting(member)" :key="index" 
                :member="member" :form="form"  @edit="editMember" @cancel="form.id=0" @submit="update">
            </row>    
        </tbody>

    </table>
</template>

<script>
import Row from './student-row.vue';
export default {
    name:'LessonStudents',
    components: {
        Row
    },
    props: {
        lesson: {
            type: Object,
            default: null
        }
	},
	data() {
		return {
            form:{
                id:0,
            },
		};
	},
	computed:{
		
		
    }, 
	watch: {
		
	},
    methods:{
        getStudents(){
            if(!this.lesson) return null;
            let students=this.lesson.members.filter(member=>{
                return member.role=='Student';
            });
            return  students;

        },
        isEditting(member){
            if(!this.form) return false;
            return  this.form.id==member.id;
        },
        editMember(member){
            this.form=new Form({
                id:member.id,
                absence:Helper.isTrue(member.absence),
                ps:member.ps

            });
        },
        update(){
            let save=Lesson.updateMember(this.form);
            save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.$emit('saved');
				})
				.catch(error => {
                    
					Helper.BusEmitError(error,'存檔失敗');
				})
        }
       
        
   }
}
</script>

