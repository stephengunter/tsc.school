@extends('layouts.manage')


@section('content')


<identities-index  :init_model="model" :can_edit="canEdit">
</identities-index>





@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},

                    canEdit:false,

                }
            },
            computed: {
                
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
              
                this.canEdit = {!! json_encode($canEdit) !!} ;
                
			},
            methods: {
                



			}

		});



    </script>


@endsection



