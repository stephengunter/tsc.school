@extends('layouts.client')

@section('content')

<signup-show-view :signup="signup" >

</signup-show-view>

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
