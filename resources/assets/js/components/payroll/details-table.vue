<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        
                        <th style="width:10%">教師姓名</th>
                        <th style="width:15%">課程</th>
                        <th style="width:10%">課堂日期</th>
                        <th style="width:10%">上課時間</th>
                        <th style="width:10%">時數</th>
                        <th style="width:10%">學員人數</th>
                        <th style="width:10%">鐘點費</th>
                    </tr>
                    
                </thead>
                <tbody>
                    <tr v-for="(detail,index) in payroll.details" :key="index">
                        
                        <td> 
                           {{ payroll.user.profile.fullname }}
                        </td>
                        <td> 
                            {{ detail.lesson.course.fullName }} 
                        </td>
                        <td>
                             {{ detail.date }} 
                        </td>
                        <td>
                             {{ detail.timeString }} 
                        </td>
                        <td> {{ detail.hours }} </td>

                        <td>{{ detail.studentCount }}</td>
                        <td> 
                             {{ detail.money | formatMoney }} 
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
export default {
    name:'PayrollDetailsTable',
    props: {
        payroll: {
            type: Object,
            default: null
        }
	},
	data() {
		return {
           
			checked_ids:[],
            checkAll: false,
            
		};
	},
	computed:{
	
		
    }, 
	watch: {
		
	},
    methods:{
        
        isTrue(val){
            return Helper.isTrue(val);
        }
        
   }
}
</script>

