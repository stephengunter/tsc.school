@extends('layouts.client')

@section('content')

<signup-edit-view :form="form" :identity_options="identityOptions" 
:center="center" :bird_date_text="birdDateText">

</signup-edit-view>

@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            form:null,
            identityOptions:[],
         
            canEdit:true,
            canBack:false,

            center:null,
            birdDateText:'',

            
        }
    },
    beforeMount() {
        this.init();
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        init(){

            this.center={!! json_encode($center) !!};
            this.birdDateText={!! json_encode($birdDateText) !!};

            let signup = {!! json_encode($signup) !!};
            let user = {!! json_encode($user) !!};
        
        
            let identityIds = {!! json_encode($identityIds) !!};
            let courseIds = {!! json_encode($courseIds) !!};
            let lotus = {!! json_encode($lotus) !!};

            this.identityOptions = {!! json_encode($identityOptions) !!};
            
            this.form = new Form({
                                signup:{
                                    ...signup
                                },
                                user:{
                                    ...user
                                },
                                lotus:lotus,
                                courseIds:courseIds.slice(0),
                                identityIds:identityIds.map(item=> parseInt(item))
                            });

        }
    }

});



</script>

@endsection
