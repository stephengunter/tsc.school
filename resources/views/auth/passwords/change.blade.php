@extends('layouts.manage')


@section('content')

    <change-password-view @success="onSuccess"></change-password-view>

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
           
            onSuccess(){
                document.location = '/manage';
            }
        }

    });



</script>


@endsection