<template>

  <!--<h1 v-if="data.treasory[0]">{{ data.treasory[0].total}}</h1>-->
<div class="container">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3><span class="glyphicon glyphicon-dashboard"></span> {{ $t('treasury')}}</h3> <br>
      </div>
      <div class="panel-body">
        <table  class="table table-striped">
          <thead class="thead-dark">
          <tr>
            <th>Dépenses</th>
            <th>Recettes</th>
          </tr>
          </thead>
          <tbody>
          <tr v-if="data.lots && data2">

            <td>
              {{ this.data.lots}}
            </td>
            <td>
              {{ this.data2 }}
            </td>
          </tr>
          <tr>
            <td class="table-dark"><b>Bénéfice</b></td>
            <td v-if="this.benef <  0" class="red"><b>{{ this.benef}}</b></td>
          <td v-else class="green"><b>{{ this.benef}}</b></td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


</template>

<script>
  const getUrl = window.location;
  const baseUrl = getUrl .protocol + "//" + getUrl.host +"/api/";

  export default {
        name: "treasury",
    data: function () {
      return {
        data:[],
        data2:[],
        benef:''

      }
    },
    mounted() {
      this.read('treasury');

    },
      methods: {
        async read(model)
        {
         await   axios.get(baseUrl+model).then((response) => {
            console.log(response);
              var mymodel = this.myModel;
              if (response.data.modelRead){
                this.data = response.data.modelRead;
                this.data2 = response.data.modelRead2;
                console.log(this.data2[0].treasury);
                if (response.data.modelRead3){
                  this.data3 = response.data.modelRead3;
                }

              }
              else {
                this.data = response.data;
                this.data2 = this.data.treasury[0].total;
                console.log(this.data.treasury[0].total);
                this.benef = (this.data2-this.data.lots).toFixed(2);
              }
            }
          )
        },
      }
    }

</script>

<style scoped>
.red  {
  background-color: red;
  color: white;
}
.green  {
  background-color: forestgreen;
  color: white;
}
</style>
