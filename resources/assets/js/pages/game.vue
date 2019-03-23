<template>
    <div class="container">

  <div class="row">

               <div class="col-md-12">

                   <div class="panel panel-default">

                       <div class="panel-heading">

                    <h3><span class="glyphicon glyphicon-dashboard"></span> {{ $t('games_list')}}</h3> <br>

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
      return { title: this.$t('games_list') }
    },
    data(){
      return {
        searchQuery: '',
        gridColumns: ['console','categories','libelle', 'pegi', 'nb_players'],
        gridData: [],
        myModel: "game",
        formModel: [
          { fName: 'console_id', placeholder: 'Votre console', model:'form.console_id'},
          { fName: 'select', placeholder: 'Vos catégories', model:'form.select'},
      { fName: 'libelle', placeholder: 'Votre Libelle', model:'form.libelle'},
      { fName: 'pegi', placeholder: 'Votre Pegi', model:'form.pegi'},
      { fName: 'nb_players', placeholder: 'Nombre de joueurs', model:'form.nb_players'},

        ],
        childForm: {
          libelle: '',
          pegi: null,
          nb_players: null,
          console_id:null,
          select: []
        },

      }
    }
  }
</script>
