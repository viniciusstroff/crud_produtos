<template>
  <div id="product">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Produtos</h4>
            <div class="card-tools" style="position: absolute;right: 1rem;top: .5rem;">
              <button type="button" class="btn btn-info" @click="create">
                Adicionar
                <i class="fas fa-plus"></i>
              </button>
              <button type="button" class="btn btn-primary" @click="reload">
                Recarregar
                <i class="fas fa-sync"></i>
              </button>
            </div>
          </div>

          <div class="card-body">
            <div class="mb-3">
              <div class="row">
                <div class="col-md-2">
                  <strong>Pesquisa :</strong>
                </div>
                <div class="col-md-3">
                  <select v-model="queryFiled" class="form-control" id="fileds">
                    <option value="name">Nome</option>
                    <option value="price">Preço</option>
                  </select>
                </div>
                <div class="col-md-7">
                  <input v-model="query" type="text" class="form-control" placeholder="Search">
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-hover table-bordered table-striped">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Preço</th>
                    <th scope="col" class="text-center">Opções</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-show="products.length"
                    v-for="(product, index) in products"
                    :key="products.id"
                  >
                    <th scope="row">{{ index + 1 }}</th>
                    <td>{{ product.name}}</td>
                    <td>R$ {{ formatPrice(product.price) }}</td>
                    <td class="text-center">
                      <button type="button" @click="show(product)" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button type="button" @click="edit(product)" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        type="button"
                        @click="destroy(product)"
                        class="btn btn-danger btn-sm"
                      >
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-show="!products.length">
                    <td colspan="6">
                      <div class="alert alert-danger" role="alert">Desculpa, Não encontramos as informações.</div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <pagination
                v-if="pagination.last_page > 1"
                :pagination="pagination"
                :offset="5"
                @paginate="query === '' ? getData() : searchData()"
              ></pagination>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="productModalLong"
      tabindex="-1"
      role="dialog"
      aria-labelledby="productModalLongTitle"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5
              class="modal-title"
              id="productModalLongTitle"
            >{{ editMode ? "Editar" : "Adicionar novo" }} Produto</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="editMode ? update() : store()" @keydown="form.onKeydown($event)">
            <div class="modal-body">
              <alert-error :form="form"></alert-error>
              <div class="form-group">
                <label>Nome</label>
                <input
                  v-model="form.name"
                  type="text"
                  name="name"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('name') }"
                >
                <has-error :form="form" field="name"></has-error>
              </div>
              <div class="form-group">
                <label>Preço</label>
                <input
                  v-model="form.price"
                  type="text"
                  name="price"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('price') }"
                >
                <has-error :form="form" field="price"></has-error>
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
              <button :disabled="form.busy" type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div
      class="modal fade"
      id="showModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="showModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="showModalLabel"><strong>Produto</strong> {{ form.name }}</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <strong>Nome : {{ form.name }}</strong>
            <br>
            <strong>Preço : {{ form.price }}</strong>
            <br>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <vue-progress-bar></vue-progress-bar>
    <vue-snotify></vue-snotify>
  </div>
</template>

<script>


export default {
  data() {
    return {
      editMode: false,
      query: "",
      queryFiled: "name",
      products: [],
      form: new Form({
        id: "",
        name: "",
        price: ""
      }),
      pagination: {
        current_page: 1
      }
    };
  },
  watch: {
    query: function(newQ, old) {
      if (newQ === "") {
        this.getData();
      } else {
        this.searchData();
      }
    }
  },
  mounted() {
    this.getData();
  },
  methods: {
    formatPrice(value) {
        let val = (value/1).toFixed(2).replace('.', ',')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    },
    getData() {
      this.$Progress.start();
      axios
        .get("/api/products?page=" + this.pagination.current_page)
        .then(response => {
          this.products = response.data.data;
          this.pagination = response.data.meta;
          this.$Progress.finish();
        })
        .catch(e => {
          console.log(e);
          this.$Progress.fail();
        });
    },
    searchData() {
      this.$Progress.start();
      axios
        .get(
          "/api/search/products/" +
            this.queryFiled +
            "/" +
            this.query +
            "?page=" +
            this.pagination.current_page
        )
        .then(response => {
          this.products = response.data.data;
          this.pagination = response.data.meta;
          this.$Progress.finish();
        })
        .catch(e => {
          console.log(e);
          this.$Progress.fail();
        });
    },
    reload() {
      this.getData();
      this.query = "";
      this.queryFiled = "name";
      this.$snotify.success("Dados recarregados com sucesso", "Success");
    },
    create() {
      this.editMode = false;
      this.form.reset();
      this.form.clear();
      $("#productModalLong").modal("show");
    },
    store() {
      this.$Progress.start();
      this.form.busy = true;
      this.form
        .post("/api/products")
        .then(response => {
          this.getData();
          $("#productModalLong").modal("hide");
          if (this.form.successful) {
            this.$Progress.finish();
            this.$snotify.success("Produto Salvo com Sucesso", "Success");
          } else {
            this.$Progress.fail();
            this.$snotify.error(
              "Algo deu errado tente novamente mais tarde.",
              "Error"
            );
          }
        })
        .catch(e => {
          this.$Progress.fail();
          console.log(e);
        });
    },
    show(product) {
      this.form.reset();
      this.form.fill(product);
      $("#showModal").modal("show");
    },
    edit(product) {
      this.editMode = true;
      this.form.reset();
      this.form.clear();
      this.form.fill(product);
      $("#productModalLong").modal("show");
    },
    update() {
      this.$Progress.start();
      this.form.busy = true;
      this.form
        .put("/api/products/" + this.form.id)
        .then(response => {
          this.getData();
          $("#productModalLong").modal("hide");
          if (this.form.successful) {
            this.$Progress.finish();
            this.$snotify.success("Produto Atualizado com Sucesso", "Success");
          } else {
            this.$Progress.fail();
            this.$snotify.error(
              "Algo deu errado tente novamente mais tarde.",
              "Error"
            );
          }
        })
        .catch(e => {
          this.$Progress.fail();
          console.log(e);
        });
    },
    destroy(product) {
      this.$snotify.clear();
      this.$snotify.confirm(
        "Ao deletar vai se perder os dados",
        "Você realmente quer deletar esse Produto?",
        {
          showProgressBar: false,
          closeOnClick: false,
          pauseOnHover: true,
          buttons: [
            {
              text: "Sim",
              action: toast => {
                this.$snotify.remove(toast.id);
                this.$Progress.start();
                axios
                  .delete("/api/products/" + product.id)
                  .then(response => {
                    this.getData();
                    this.$Progress.finish();
                    this.$snotify.success(
                      "Produto deletado com sucesso",
                      "Success"
                    );
                  })
                  .catch(e => {
                    this.$Progress.fail();
                    console.log(e);
                  });
              },
              bold: true
            },
            {
              text: "Não",
              action: toast => {
                this.$snotify.remove(toast.id);
              },
              bold: true
            }
          ]
        }
      );
    }
  }
};
</script>
