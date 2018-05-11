@extends('layouts.manage')


@section('content')


<centers-index v-show="indexMode" :init_model="model" :keys="keys" :key_val="key" :can_edit="can_edit" 
:can_import="can_import" :version="version" v-on:selected="onSelected"   v-on:create="onCreate" v-on:import="beginImport">
    

</centers-index>

<centers-details v-if="selected" :id="selected" v-on:center-deleted="backToIndex"
     v-on:back="backToIndex" >

</centers-details>

<centers-create v-if="creating" v-on:saved="backToIndex" v-on:cancel="backToIndex">
</centers-create>


<centers-import v-if="importing" :areas="areas" :can_import="can_import"
                v-on:cancel="backToIndex" v-on:imported="backToIndex">
</centers-import>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    can_edit: false,
                    can_import: false,

                    areas:[],

                    keys: [],
                    key:'',

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
                this.areas = {!! json_encode($areas) !!} ;
                this.keys = {!! json_encode($keys) !!} ;
                this.key = {!! json_encode($key) !!} ;
                this.can_edit = Helper.isTrue('{!! $canEdit !!}');
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



