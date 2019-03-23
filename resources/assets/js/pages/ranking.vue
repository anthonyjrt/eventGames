<template>
  <div class="container">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3><span class="glyphicon glyphicon-dashboard"></span> {{ $t('ranking')}}</h3> <br>
        </div>
        <div class="panel-body">
          <table  class="table table-striped">
            <thead class="thead-dark">
            <tr>
              <th>Jeu</th>
              <th>Joueur</th>
              <th>ID Joueur</th>
              <th>Vie</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="entry in data">
              <td>{{ entry.game.libelle}}</td>
              <td v-if="b !== entry.player.name ? counter++  : entry.player.name " >{{ entry.player.name|uppercase}} {{ entry.player.firstname|capitalize}}
              </td>
              <td>
                {{ entry.player_number}}
              </td>
              <td>{{ entry.life}}</td>

            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!--<ul class="nav nav-tabs" id="myTab" role="tablist" >
      <li class="nav-item">
        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Contact</a>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">1</div>
      <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">2</div>
      <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">3</div>
    </div>-->
  </div>

</template>

<script>
  const getUrl = window.location;
  const baseUrl = getUrl .protocol + "//" + getUrl.host +"/api/";

  export default {
    name: "ranking",
    data: function () {
      return {
        data:[],
        data2:[],
        benef:'',
        b:null,
        counter:1

      }
    },
    mounted() {
      this.read('ranking');
    },filters: {
      uppercase: function (value){
        if (!value) return ''
        value = value.toString()
        return value.toUpperCase()
      },
      capitalize: function (value) {
        if (!value) return ''
        value = value.toString()
        return value.charAt(0).toUpperCase() + value.slice(1)
      },
    },computed: {
      reverse: function() {
        return this.data.sort();
      }
    },
    methods: {
      async read(model)
      {
        await   axios.get(baseUrl+model).then((response) => {

            var mymodel = this.myModel;
            if (response.data.modelRead){
              console.log('raté');

            }
            else {
              this.data = response.data.rankings;
              this.data.sort();
              console.log(this.data);
            }
          }
        )
      },
    }
  }
</script>

<style scoped>

</style>
