<template>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>加入日期</label>
                <datetime-picker :date="form.volunteer.joinDate" :can_clear="true" @selected="setJoinDate"></datetime-picker>
            </div>
        </div>
        
    </div> <!--  row   -->

</template>


<script>
    export default {
        name: 'VolunteerInputs',
        props: {
            form: {
                type: Object,
                default: null
            },
            centers:{
                type: Array,
                default: null
            }
        },
        data() {
            return {
              
               
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
            onCanceled(){
                this.$emit('canceled')
            },
            clearDOB(){
                    this.dob.time=''
            },
            setJoinDate(val){
                this.form.volunteer.joinDate=val;
            },
        }
    }
</script>