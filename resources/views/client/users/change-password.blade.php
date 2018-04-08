@extends('layouts.client')

@section('content')

<change-password-view ></change-password-view>

@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
           
        }
    },
    beforeMount() {
        
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
