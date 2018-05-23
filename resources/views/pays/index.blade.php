@extends('layouts.manage')


@section('content')


<pays-index :init_model="model" :centers="centers" :courses="courses" 
           :payways="payways" :version="version"
            v-on:selected="onSelected">
</pays-index>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    centers: [],
                    payways:[],

                    canQuit:false,

                    params:{},

                   

                }
            },
            computed: {
                
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
              

                this.centers = {!! json_encode($centers) !!} ;
                this.payways = {!! json_encode($payways) !!} ;

			},
            methods: {
                

			}

		});



    </script>


@endsection



