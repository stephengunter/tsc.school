@extends('layouts.manage')


@section('content')


<discounts-index  :init_model="model" :keys="keys" :key_val="key" :can_edit="canEdit">
</discounts-index>





@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},

                    keys: [],
                    key:'',
                    canEdit:false,

                }
            },
            computed: {
                
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
                this.keys = {!! json_encode($keys) !!} ;
                this.key = {!! json_encode($key) !!} ;
                this.canEdit = {!! json_encode($canEdit) !!} ;
                
			},
            methods: {
                



			}

		});



    </script>


@endsection



