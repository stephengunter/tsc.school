@extends('layouts.client')

@section('content')

<reset-password-view :token="token"></reset-password-view>

@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            token:'',
        }
    },
    beforeMount() {
        this.token = {!! json_encode($token) !!} ; 
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
