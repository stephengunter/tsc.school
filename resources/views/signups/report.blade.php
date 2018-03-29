@extends('layouts.manage')


@section('content')


<signups-report :init_model="model" :terms="terms" :centers="centers" :can_review="can_review">
</signups-report>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    model: {},
                    

                    terms: [],
                    centers: [],

                    can_review: false,

                    

                }
            },
            computed: {
               
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;

                this.terms = {!! json_encode($terms) !!} ;
                this.centers = {!! json_encode($centers) !!} ;
              
                this.can_review = {!! json_encode($canReview) !!} ;

			},
            methods: {
               


			}

		});



    </script>


@endsection



