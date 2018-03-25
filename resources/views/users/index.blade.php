@extends('layouts.manage')


@section('content')



<users-index v-show="indexMode" :init_model="model"  :version="version"
                v-on:selected="onSelected" >
</users-index>
<users-details v-if="selected" :id="selected"
                  v-on:back="backToIndex" v-on:user-deleted="backToIndex">
</users-details>

@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},

                   
                    selected: 0,


                }
            },
            computed: {
                indexMode() {
                    if (this.selected) return false;
                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
              

			},
            methods: {
                onSelected(id) {
                    this.selected = id;
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                  
                }


			}

		});



    </script>


@endsection



