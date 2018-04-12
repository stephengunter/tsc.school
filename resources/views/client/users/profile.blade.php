@extends('layouts.client')

@section('content')

<user-profile-view :user="user"></user-profile-view>

@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            user:null
        }
    },
    beforeMount() {
        this.user = {!! json_encode($user) !!} ;
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
