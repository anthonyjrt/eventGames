<template>
    <div class="container">

  <div class="row">

               <div class="col-md-12">

                   <div class="panel panel-default">

                       <div class="panel-heading">

                    <h3><span class="glyphicon glyphicon-dashboard"></span> {{ $t('consoles_list')}}</h3> <br>

                       </div>



                       <div class="panel-body">
    <form id="search" class="pull-right">
      {{ $t('search')}} <input name="query" v-model="searchQuery"><br/><br/>
    </form>
    <table-demo
      :myModel="myModel"
      :columns="gridColumns"
      :filter-key="searchQuery"
      :action="true"
      :my-t-model="$t(myModel)"
      :form-model="formModel"
      :child-form="childForm">
    </table-demo>


                       </div>

                   </div>

               </div>

           </div>

     </div>

</template>

<script>
    import TableDemo from "../components/Table";
    export default {
      components: {TableDemo},
      middleware: 'auth',

      metaInfo () {
        return { title: this.$t('consoles_list') }
      },
      data(){
        return {
          searchQuery: '',
          gridColumns: ['libelle'],
          gridData: [],
          myModel: "console",
          formModel: [
            { fName: 'libelle', placeholder: 'Votre Libelle', model:'form.libelle'},
          ],
          childForm: {
            libelle: ''
          },

        }
      }
    }
</script>
