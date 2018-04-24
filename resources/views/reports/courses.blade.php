@extends('layouts.manage')


@section('content')


<courses-report  :init_model="model" :terms="terms" :centers="centers" :courses="courses" 
               :version="version" >
              
</courses-report>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    summary:{},

                    terms: [],
                    centers: [],
                    courses: [],
 

                }
            },
            computed: {
                
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;

                this.terms = {!! json_encode($terms) !!} ;
                this.centers = {!! json_encode($centers) !!} ;
                this.courses = {!! json_encode($courses) !!} ;
              
              

			},
            methods: {
               


			}

		});



    </script>


@endsection



