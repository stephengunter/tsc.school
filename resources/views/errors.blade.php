@extends('layouts.manage')


@section('content')

    @if (isset($msg))
        <h1 style="color:red"> {{ $msg }}</h1>
    @else
        <h1 style="color:red"> 系統目前無回應,請稍候再試. </h1>
    @endif

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
                

			}

		});



    </script>


@endsection



