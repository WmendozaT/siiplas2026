<template>
  <div class="container">
    <div class="card mt-5 mb-5">
      <div class="card-body">
        <div class="col-md-12">
          <div class="float-start">
            <img class="img-fluid" src="../assets/images/logo-adsib.png" alt="ADSIB">
          </div>
          <div class="float-end">
            <img class="img-fluid" src="../assets/images/logo-firma-digital.png" alt="FIRMA DIGITAL">
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <h1 class="card-title">
                  Documento PDF
                </h1>
                <h5 class="card-subtitle mb-2 text-body-secondary">
                  Firma de documentos con el Jacobitus Total
                </h5>
                <div class="col-md-12" v-if="!jacobitusVersionCompatible">
                  <div class="d-flex alert alert-warning">
                    <div class="flex-shrink-0"> <img width="100" class="img-fluid mx-auto rounded-circle"
                        src="../assets/images/tokencito.png" alt="..."> </div>
                    <div class="flex-grow-1 ms-3">
                      <h3>Atención</h3>
                      <p class="lead">
                        Usted esta utlizando <strong>Jacobitus {{ jacobitusVersionActual }}</strong>,
                        ésta
                        versión no es compatible con esta aplicación, por favor actualice su
                        versión mínimamente a la versión <strong> {{ jacobitusVersionMinima }}</strong>.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="archivo" class="form-label">Seleccione el documento PDF que desea
                    firmar</label>
                  <input class="form-control" type="file" id="archivo" accept=".pdf"
                    @change="cargarArchivoBase64('archivo', 'archivoPdf', 'firmas');">
                </div>
                <div class="row">
                  <div class="col-md-12" style="height: 500px;">
                    <embed class="form-control" id="archivoPdf" style="height: 100%; width: 100%">
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-12" v-if="!jacobitus">
                    <div class="d-flex alert alert-danger">
                      <div class="flex-shrink-0"> <img width="100" class="img-fluid mx-auto rounded-circle"
                          src="../assets/images/tokencito.png" alt="..."> </div>
                      <div class="flex-grow-1 ms-3">
                        <h3>Atención</h3>
                        <p class="lead">
                          Ejecute la aplicación <strong>Jacobitus de Escritorio</strong> y
                          actualice la página.
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12" v-if="!dispositivo">
                    <div class="d-flex alert alert-warning">
                      <div class="flex-shrink-0"> <img width="100" class="img-fluid mx-auto rounded-circle"
                          src="../assets/images/tokencito.png" alt="..."> </div>
                      <div class="flex-grow-1 ms-3">
                        <h3>Atención</h3>
                        <p class="lead">
                          Conecte su <strong>dispositivo criptográfico (token)</strong> y
                          actualice la página.
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12" v-if="jacobitus && dispositivo && jacobitusVersionCompatible">
                    <input @click="firmarPdf('archivoPdf', 'archivoPdf', 'firmas');" type="button"
                      class="btn btn-dark float-end" value="Firmar">
                    <input @click="firmarPdfModoSeguro('archivoPdf', 'archivoPdf', 'firmas');" type="button"
                      class="btn btn-dark float-end me-1" value="Firmar (Modo seguro)">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <h1 class="card-title">
                  Firmas
                </h1>
                <h5 class="card-subtitle mb-2 text-body-secondary">Validación de firmas en el documento
                </h5>
                <pre class="p-3 language-json" style="height: 650px; overflow: scroll;"><code id="firmas"></code></pre>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr class="p-0 m-0" />
      <div class="card-body bg-light">
        <div class="row mb-3">
          <div class="col-md-12 mb-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0">
                    <img width="70" src="../assets/images/resl.png" alt="...">
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6 class="text-muted">CÓDIGO FUENTE</h6>
                    <h5>
                      Repositorio Estatal de Software Libre
                    </h5>
                    <a class="text-decoration-none text-black text-opacity-75"
                      href="https://gitlab.softwarelibre.gob.bo/adsib/jacobitus-total/envolturas/javascript/-/tree/master/ejemplos/vuejs"
                      target="_blank">https://gitlab.softwarelibre.gob.bo/adsib/jacobitus-total/envolturas/javascript/-/tree/master/ejemplos/vuejs</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 mb-3">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-top">
                  <div class="flex-shrink-0">
                    <img src="../assets/images/jacobitus-escritorio.png" alt="...">
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="text-muted1">JACOBITUS DE ESCRITORIO</h5>
                    <span class="lead">Solución a todo requerimiento de firma digital</span>
                    <h4 class="text-muted mt-3">
                      Instalador
                    </h4>
                    <a class="text-decoration-none text-black text-opacity-75"
                      href="https://firmadigital.bo/herramientas/jacobitus-escritorio/"
                      target="_blank">https://firmadigital.bo/herramientas/jacobitus-escritorio/</a>
                    <h4 class="text-muted mt-3">
                      Documentación de interoperación
                    </h4>
                    <a class="text-decoration-none text-black text-opacity-75" href="https://localhost:9000/apidoc/"
                      target="_blank">https://localhost:9000/apidoc/</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="offset-md-9 col-md-3">
            <img class="img-fluid float-end" src="../assets/images/potenciado-por-ADSIB-1.png"
              alt="POTENCIADO POR ADSIB">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2'
// import '../libs/prism/prism.min.css';
// require('../libs/prism/prism.min');
import '../libs/FreezeUI/freeze-ui.min.css';
require('../libs/FreezeUI/freeze-ui.min');

import { jacobitusTotal } from '@/libs/adsib/jacobitus-total.es6'

export default {
  name: 'HomeComponente',
  props: {
  },
  data() {
    return {
      jacobitus: false,
      jacobitusVersionActual: '',
      jacobitusVersionMinima: '1.3.0',
      jacobitusVersionCompatible: true,
      dispositivo: false
    }
  },
  computed: {
  },
  async mounted() {
    this.jacobitus = await this.jacobitusEnEjecucion(this.jacobitusVersionMinima);
    this.dispositivo = await this.dispositivoConectado();
  },
  methods: {
    async jacobitusEnEjecucion(versionMinima) {
      const estado = await jacobitusTotal.verificarEjecucion(versionMinima);
      this.jacobitusVersionActual = estado.datos.apiVersion;
      this.jacobitusVersionCompatible = estado.datos.versionCompatible;
      return estado.exito;
    },
    async dispositivoConectado() {
      const estado = await jacobitusTotal.verificarDispositivo();
      return estado.exito;
    },
    obtenerBase64(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
      });
    },
    cargarArchivoBase64(origenId, destinoId, validacionId) {
      const self = this;
      window.FreezeUI({ text: 'Cargando documento' });
      const archivoPdf = document.getElementById(origenId).files[0];
      this.obtenerBase64(archivoPdf).then((archivoPdfBase64) => {
        document.getElementById(destinoId).src = archivoPdfBase64;
        document.getElementById(destinoId).onload = function () {
          if (validacionId) {
            self.validarPdf(destinoId, validacionId);
          } else {
            window.UnFreezeUI();
          }
        }
      });
    },
    async firmarPdf(origenId, destinoId, validacionId) {
      const self = this;
      const archivoPdf = document.getElementById(origenId).src;
      if (archivoPdf) {
        const { value: pin } = await Swal.fire({
          title: "Ingrese su PIN",
          input: "password",
          inputLabel: "PIN",
          inputPlaceholder: "Ingrese su PIN",
          inputAttributes: {
            autocapitalize: "off",
            autocorrect: "off"
          },
          confirmButtonText: 'Aceptar',
          width: '20em'
        });
        if (pin) {
          window.FreezeUI({ text: 'Firmando documento' });
          const slot = 1;
          let respuesta = await jacobitusTotal.obtenerCertificadosParaFirmaDigital(slot, pin);
          if (respuesta.datos.certificados && respuesta.datos.certificados.length > 0) {
            const alias = respuesta.datos.certificados[0].alias;
            respuesta = await jacobitusTotal.firmarPdf(slot, pin, alias, archivoPdf);
            document.getElementById(destinoId).src = `data:application/pdf;base64,${respuesta.datos.docFirmado}`;
            document.getElementById(destinoId).onload = function () {
              if (validacionId) {
                self.validarPdf(destinoId, validacionId);
              } else {
                window.UnFreezeUI();
              }
            }
          } else {
            window.UnFreezeUI();
            Swal.fire({
              title: "Jacobitus Total",
              text: respuesta.mensaje,
              icon: "error"
            });
          }
        }
      }
    },
    async firmarPdfModoSeguro(origenId, destinoId, validacionId) {
      const self = this;
      const archivoPdf = document.getElementById(origenId).src;
      if (archivoPdf) {
        const { value: ci } = await Swal.fire({
          title: "Número de documento",
          input: "text",
          inputLabel: "C.I.",
          inputPlaceholder: "Número de documento",
          confirmButtonText: 'Continuar',
          width: '20em'
        });
        if (ci) {
          window.FreezeUI({ text: 'Firmando documento' });
          const respuesta = await jacobitusTotal.firmarPdfModoSeguro(ci, archivoPdf);
          if (respuesta.exito) {
            document.getElementById(destinoId).src = `data:application/pdf;base64,${respuesta.datos.docFirmado}`;
            document.getElementById(destinoId).onload = function () {
              if (validacionId) {
                self.validarPdf(destinoId, validacionId);
              } else {
                window.UnFreezeUI();
              }
            }
          } else {
            window.UnFreezeUI();
            Swal.fire({
              title: "Jacobitus Total",
              text: respuesta.mensaje,
              icon: "error"
            });
          }
        }
      }
    },
    async validarPdf(origenId, destinoId) {
      window.FreezeUI({ text: 'Validando firmas' });
      const archivoPdf = document.getElementById(origenId).src;
      const respuesta = await jacobitusTotal.validarPdf(archivoPdf);
      document.getElementById(destinoId).innerHTML = JSON.stringify(respuesta.datos.firmas, null, 4);
      window.Prism.highlightAll();
      window.UnFreezeUI();
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
