@extends('layouts.manage')


@section('content')



<users-index v-show="indexMode" :init_model="model"  :version="version" :can_import="can_import"
                v-on:selected="onSelected" v-on:import="beginImport">
</users-index>
<users-details v-if="selected" :id="selected"
                  v-on:back="backToIndex" v-on:user-deleted="backToIndex">
</users-details>

<users-import v-if="importing"  :can_import="can_import"  
                v-on:cancel="backToIndex" v-on:imported="backToIndex">
</users-import>

@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    can_import: false,     
                   
                    selected: 0,
                    importing: false,

                }
            },
            computed: {
                indexMode() {
                    if (this.selected) return false;
                    if (this.importing) return false;
                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
                this.can_import = Helper.isTrue('{!! $canImport !!}'); 
                

			},
            methods: {
                onSelected(id) {
                    this.selected = id;
                },
                beginImport() {

                    this.importing = true;
                },
                backToIndex() {
                    this.version += 1;
                    this.importing = false;
                    this.selected = 0;
                  
                }


			}

		});



    </script>


@endsection



