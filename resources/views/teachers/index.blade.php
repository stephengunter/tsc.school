@extends('layouts.manage')


@section('content')


<teachers-index v-show="indexMode" :init_model="model" :centers="centers" :can_review="can_review" :can_import="can_import" :version="version"
               v-on:selected="onSelected" v-on:create="onCreate" v-on:import="beginImport" v-on:group-changed="setGroup">
</teachers-index>
<teachers-details v-if="selected" :id="selected" :group="group"
                 v-on:back="backToIndex" v-on:teacher-deleted="backToIndex">  
</teachers-details>
<teachers-create v-if="creating" :group="group" v-on:back="backToIndex">
</teachers-create>

<teachers-import v-if="importing"  :can_import="can_import"  :centers="centers"
                v-on:cancel="backToIndex" v-on:imported="backToIndex">
</teachers-import>



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

                    importing: false,

                    group:false

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
                
                this.can_review = Helper.isTrue('{!! $canReview !!}');  
                this.can_import = Helper.isTrue('{!! $canImport !!}');  

			},
            methods: {
                onCreate() {
                    this.creating = true;
                },
                onSelected(id,group) {
                    this.selected = id;
                    this.group = Helper.isTrue(group);
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
                setGroup(val){
                    this.group=val;
                }

			}

		});



    </script>


@endsection



