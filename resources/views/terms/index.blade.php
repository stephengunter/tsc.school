@extends('layouts.manage')


@section('content')


<terms-index v-show="indexMode" :init_model="model" :can_edit="can_edit"  :version="version"
             v-on:selected="onSelected" v-on:create="onCreate" >
</terms-index>

<terms-details v-if="selected" :id="selected"
                 v-on:back="backToIndex">
</terms-details>

<terms-create v-if="creating" v-on:back="backToIndex"></terms-create>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: null,
                    can_edit:false,
                    can_import: false,

                    creating: false,
                    selected: 0,

                }
            },
            computed: {
                indexMode() {
                    if (this.creating) return false;
                    if (this.selected) return false;

                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
                this.can_edit = Helper.isTrue('{!! $canEdit !!}');

			},
            methods: {
                onCreate() {
                    this.creating = true;
                },
                onSelected(id) {
                    this.selected = id;
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                    this.creating = false;
                }


			}

		});



    </script>


@endsection



