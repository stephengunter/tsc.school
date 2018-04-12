@extends('layouts.client')

@section('content')

<signup-edit-view :form="form" :identity_options="identityOptions">

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
            canBack:false
        }
    },
    beforeMount() {
        let signup = {!! json_encode($signup) !!};
        let user = {!! json_encode($user) !!};
        let courseIds = {!! json_encode($courseIds) !!};
      
        let identityIds = {!! json_encode($identityIds) !!};
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
							identityIds:identityIds.slice(0)
						});
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
