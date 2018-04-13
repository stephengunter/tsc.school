@extends('layouts.client')

@section('content')

<bill-edit-view  :signup="signup">

</bill-edit-view>

@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            signup:null,
         
           
        }
    },
    beforeMount() {
        this.signup = {!! json_encode($signup) !!};
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
