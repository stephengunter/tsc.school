@extends('layouts.client')

@section('content')

<register-view></register-view>

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
