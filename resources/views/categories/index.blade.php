@extends('layouts.manage')


@section('content')



<categories-index v-show="indexMode" ref="categoryIndex"   :init_model="model" :can_edit="can_edit" :can_import="can_import" 
                v-on:import="beginImport">
</categories-index>


<categories-import v-if="importing" :can_import="can_import"
                   v-on:cancel="backToIndex" v-on:imported="backToIndex">
                  
</categories-import>



@endsection

@section('scripts')

     <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    model: null,
                    can_edit:false,
                    can_import: false,


                    importing:false

                }
            },
            computed: {
                indexMode() {
                 
                    if (this.importing) return false;

                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;
                this.can_edit = Helper.isTrue('{!! $canEdit !!}');
                this.can_import =Helper.isTrue('{!! $canImport !!}');

			},
            methods: {
                beginImport() {
                    this.importing = true;
                },
                backToIndex() {
                    this.$refs.categoryIndex.fetchData();
                    this.importing = false;
                }
			}

		});



    </script>


@endsection



