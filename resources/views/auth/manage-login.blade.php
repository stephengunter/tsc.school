@extends('layouts.manage')


@section('content')

<login-view @logined="onLogined"></login-view>


@endsection



@section('scripts')

<script type="text/babel">

    new Vue({
        el: '#main',
        data() {
            return {
                

            }
        },
        computed: {
       
        },
        beforeMount() {
           

        },
        methods: {
           
            onLogined(){
                document.location = '/manage';
            }
        }

    });



</script>


@endsection