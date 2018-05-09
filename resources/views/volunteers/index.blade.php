@extends('layouts.manage')


@section('content')


<volunteers-index v-show="indexMode" :init_model="model" :centers="centers" :weekdays="weekdays"
               :can_import="can_import" :version="version"
               v-on:selected="onSelected" v-on:create="onCreate" v-on:import="beginImport" >
</volunteers-index>
<volunteers-details v-if="selected" :id="selected" 
                 v-on:back="backToIndex" v-on:volunteer-deleted="backToIndex">  
</volunteers-details>
<volunteers-create v-if="creating"  v-on:back="backToIndex">
</volunteers-create>

<volunteers-import v-if="importing"  :can_import="can_import"  :centers="centers"
                v-on:cancel="backToIndex" v-on:imported="backToIndex">
</volunteers-import>



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

                    centers: [],
                    weekdays: [],

                    creating:false,
                    selected: 0,

                    importing: false,

                 

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
                this.weekdays = {!! json_encode($weekdays) !!} ;
                
                let canImport= {!! json_encode($canImport) !!} ;
                this.can_import = Helper.isTrue(canImport);  

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
                },

			}

		});



    </script>


@endsection



