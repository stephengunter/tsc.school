@extends('layouts.manage')


@section('content')


<admins-index v-show="indexMode" :init_model="model" :centers="centers" :can_import="can_import" :version="version"
                v-on:selected="onSelected" v-on:create="onCreate" v-on:import="beginImport">
</admins-index>
<admins-details v-if="selected" :id="selected"
                  v-on:back="backToIndex" v-on:admin-deleted="backToIndex">
</admins-details>
<admins-create v-if="creating" v-on:cancel="backToIndex" v-on:saved="backToIndex">
</admins-create>

<admins-import v-if="importing" :can_import="can_import" :centers="centers" 
                 v-on:cancel="backToIndex" v-on:imported="backToIndex">
</admins-import>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    can_review: false,
                    can_import: false,

                    centers: [],

                    creating:false,
                    selected: 0,

                    importing: false

                }
            },
            computed: {
                indexMode() {
                    if (this.creating) return false;
                    if (this.selected) return false;
                    if (this.importing) return false;

                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
                this.centers = {!! json_encode($centers) !!} ;
             
                this.can_import = Helper.isTrue('{!! $canImport !!}');  

			},
            methods: {
                onCreate() {
                    this.creating = true;
                },
                onSelected(id) {
                    this.selected = id;
                },
                beginImport() {

                    this.importing = true;
                },
                backToIndex() {
                   
                    this.version += 1;

                    this.selected = 0;
                    this.creating = false;
                    this.importing = false;
                }

			}

		});



    </script>


@endsection



