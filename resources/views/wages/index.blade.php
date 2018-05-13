@extends('layouts.manage')


@section('content')


<wages-index  :init_model="model" :ps="ps" :can_edit="canEdit">
</wages-index>





@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},

                    ps:[],

                    canEdit:false,

                }
            },
            computed: {
                
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
              
                this.canEdit = {!! json_encode($canEdit) !!} ;

                this.ps = {!! json_encode($ps) !!} ;
                
			},
            methods: {
                



			}

		});



    </script>


@endsection



