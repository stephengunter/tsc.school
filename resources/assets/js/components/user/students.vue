<template>
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width:15%">開課中心</th>
                <th>課程名稱</th>
                <th style="width:20%">課程日期</th>
               
                <th style="width:10%"></th>
                
            </tr>
        </thead>
                <tbody>
                    <tr v-for="(item,index) in students" :key="index">
                       
                        <td> {{ item.course.centerName }} </td>
                        <td> {{ item.course.number }} 
                                &nbsp;
                             {{ item.course.fullName }}
                        </td>
                        <td>{{ item.course.beginDate }}  ~  {{ item.course.endDate }}</td>
                      
                        <td>
                            <span v-if="item.status < 0" class="label label-default"> 已退出 </span>
                        </td>
                    </tr>
                    
                </tbody>
                

            </table>
</template>

<script>

export default {
    name: 'UserStudentRecords',
    props: {
        user: {
            type: Object,
            default: null
        }
    },
    data(){
        return{

            students:[],
           
        }
    },
    computed:{
        
    },
    beforeMount(){
        this.init()
    },
    methods:{
        init(){
            this.fetchData();
        },
        fetchData() {
            let getData=Student.getByUser(this.user.id);
            
            getData.then(students => {
                this.students = students.slice(0);
            })
            .catch(error=> {
              
                Helper.BusEmitError(error);
            })
        }
        
    }
    
}
</script>

