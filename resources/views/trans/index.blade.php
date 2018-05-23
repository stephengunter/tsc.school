@extends('layouts.manage')


@section('content')


<trans-index :init_model="model" :terms="terms" :keys="keys" :init_params="params"
               :version="version" >
               
</trans-index>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    params:{},

                    terms: [],
                    keys: [],
                   

                   

                }
            },
            computed: {
                
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
                this.params = {!! json_encode($params) !!} ;

                this.terms = {!! json_encode($terms) !!} ;
                this.keys = {!! json_encode($keys) !!} ;

			},
            methods: {
                


			}

		});



    </script>


@endsection



