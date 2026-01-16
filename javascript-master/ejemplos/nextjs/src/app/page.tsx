'use client';
import { ChangeEvent, useEffect, useState } from 'react';
import Swal from 'sweetalert2'

import { jacobitusTotal } from '../libs/adsib/jacobitus-total.es6';

declare function FreezeUI(params: any);
declare function UnFreezeUI();
declare const Prism: any;

export default function Home() {

  const [jacobitus, setJacobitus] = useState<boolean>(true);
  const [dispositivo, setDispositivo] = useState<boolean>(true);
  const jacobitusVersionMinima = '1.3.0';
  const [jacobitusVersionActual, setJacobitusVersionActual] = useState<string>('');
  const [jacobitusVersionCompatible, setJacobitusVersionCompatible] = useState<boolean>(true);
  const [archivo, setArchivo] = useState<string | undefined>(undefined);
  const [firmas, setFirmas] = useState<string | undefined>(undefined);

  const jacobitusEnEjecucion = async (versionMinima: string = null) => {
    const estado = await jacobitusTotal.verificarEjecucion(versionMinima);
    setJacobitusVersionActual(estado.datos?.apiVerision);
    setJacobitusVersionCompatible(estado.datos?.versionCompatible);
    setJacobitus(estado.exito);
  };

  const dispositivoConectado = async () => {
    const estado = await jacobitusTotal.verificarDispositivo();
    setDispositivo(estado.exito);
  };

  const obtenerBase64 = (file: File): Promise<string | undefined> => {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = () => resolve(reader.result?.toString());
      reader.onerror = error => reject(error);
    });
  };

  const cargarArchivoBase64 = async (event: ChangeEvent<HTMLInputElement>) => {
    if (event.target.files) {
      FreezeUI({ text: 'Cargando documento' });
      const archivoPdf = await obtenerBase64(event.target.files[0]);
      setArchivo(archivoPdf);
    }
    // UnFreezeUI();
  };

  const firmarPdf = async () => {
    if (archivo) {
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
        FreezeUI({ text: 'Firmando documento' });
        const slot = 1;
        let respuesta: any = await jacobitusTotal.obtenerCertificadosParaFirmaDigital(slot, pin);
        if (respuesta.datos?.certificados && respuesta.datos?.certificados.length > 0) {
          const alias = respuesta.datos?.certificados[0].alias;
          respuesta = await jacobitusTotal.firmarPdf(slot, pin, alias, archivo);
          setArchivo(`data:application/pdf;base64,${respuesta.datos?.docFirmado}`);
          // UnFreezeUI();
        } else {
          UnFreezeUI();
          Swal.fire({
            title: "Jacobitus Total",
            text: respuesta.mensaje,
            icon: "error"
          });
        }
      }
    }
  };

  const firmarPdfModoSeguro = async () => {
    if (archivo) {
      const { value: ci } = await Swal.fire({
        title: "Número de documento",
        input: "text",
        inputLabel: "C.I.",
        inputPlaceholder: "Número de documento",
        confirmButtonText: 'Continuar',
        width: '20em'
      });
      if (ci) {
        FreezeUI({ text: 'Firmando documento' });
        const respuesta: any = await jacobitusTotal.firmarPdfModoSeguro(ci, archivo);
        if (respuesta.exito) {
          setArchivo(`data:application/pdf;base64,${respuesta.datos.docFirmado}`);
          // UnFreezeUI();
        } else {
          UnFreezeUI();
          Swal.fire({
            title: "Jacobitus Total",
            text: respuesta.mensaje,
            icon: "error"
          });
        }
      }
    }
  }

  const validarPdf = async () => {
    FreezeUI({ text: 'Validando firmas' });
    const respuesta = await jacobitusTotal.validarPdf(archivo);
    setFirmas(JSON.stringify(respuesta.datos?.firmas, null, 4));
    UnFreezeUI();
  };

  useEffect(() => {
    jacobitusEnEjecucion(jacobitusVersionMinima);
    dispositivoConectado();
    if (firmas) {
      Prism.highlightAll();
    }
  }, []);

  useEffect(() => {
    if (archivo) {
      validarPdf();
    }
  }, [archivo]);

  useEffect(() => {
    if (firmas) {
      Prism.highlightAll();
    }
  }, [firmas]);

  return (
    <div className="container">
      <div className="card mt-5 mb-5">
        <div className="card-body">
          <div className="col-md-12">
            <div className="float-start">
              <img className="img-fluid" src="assets/images/logo-adsib.png" alt="ADSIB" />
            </div>
            <div className="float-end">
              <img className="img-fluid" src="assets/images/logo-firma-digital.png" alt="FIRMA DIGITAL" />
            </div>
          </div>
        </div>
        <div className="card-body">
          <div className="row">
            <div className="col-md-6">
              <div className="card">
                <div className="card-body">
                  <h1 className="card-title">
                    Documento PDF
                  </h1>
                  <h5 className="card-subtitle mb-2 text-body-secondary">
                    Firma de documentos con el Jacobitus Total
                  </h5>
                  {
                    !jacobitusVersionCompatible && (
                      <div className="col-md-12">
                        <div className="d-flex alert alert-warning">
                          <div className="flex-shrink-0"> <img width="100" className="img-fluid mx-auto rounded-circle"
                            src="assets/images/tokencito.png" alt="..." /> </div>
                          <div className="flex-grow-1 ms-3">
                            <h3>Atención</h3>
                            <p className="lead">
                              Usted esta utlizando <strong>Jacobitus {jacobitusVersionActual}</strong>, ésta versión no es
                              compatible con esta aplicación, por favor actualice su versión mínimamente a la 
                              versión <strong>{jacobitusVersionMinima}</strong>.
                            </p>
                          </div>
                        </div>
                      </div>
                    )
                  }
                </div>
                <div className="card-body">
                  <div className="mb-3">
                    <label htmlFor="archivo" className="form-label">Seleccione el documento PDF que desea
                      firmar</label>
                    <input className="form-control" type="file" id="archivo" accept=".pdf"
                      onChange={(event) => cargarArchivoBase64(event)} />
                  </div>
                  <div className="row">
                    <div className="col-md-12" style={{ height: '500px' }}>
                      <embed className="form-control" id="archivoPdf"
                        style={{ height: '100%', width: '100%' }} src={archivo} />
                    </div>
                  </div>
                  <div className="row mt-2">
                    {
                      !jacobitus && (
                        <div className="col-md-12">
                          <div className="d-flex alert alert-danger">
                            <div className="flex-shrink-0">
                              <img width="100"
                                className="img-fluid mx-auto rounded-circle"
                                src="assets/images/tokencito.png" alt="..." />
                            </div>
                            <div className="flex-grow-1 ms-3">
                              <h3>Atención</h3>
                              <p className="lead">
                                Ejecute la aplicación <strong>Jacobitus de Escritorio</strong> y
                                actualice la página.
                              </p>
                            </div>
                          </div>
                        </div>
                      )
                    }
                    {
                      !dispositivo && (
                        <div className="col-md-12">
                          <div className="d-flex alert alert-warning">
                            <div className="flex-shrink-0">
                              <img width="100"
                                className="img-fluid mx-auto rounded-circle"
                                src="assets/images/tokencito.png" alt="..." />
                            </div>
                            <div className="flex-grow-1 ms-3">
                              <h3>Atención</h3>
                              <p className="lead">
                                Conecte su <strong>dispositivo criptográfico (token)</strong> y
                                actualice la página.
                              </p>
                            </div>
                          </div>
                        </div>
                      )
                    }
                    {
                      jacobitus && dispositivo && jacobitusVersionCompatible && (
                        <div className="col-md-12">
                          <input onClick={() => firmarPdf()}
                            type="button" className="btn btn-dark float-end" value="Firmar" />
                          <input onClick={() => firmarPdfModoSeguro()}
                            type="button" className="btn btn-dark float-end me-1" value="Firmar (Modo seguro)" />
                        </div>
                      )
                    }
                  </div>
                </div>
              </div>
            </div>
            <div className="col-md-6">
              <div className="card">
                <div className="card-body">
                  <h1 className="card-title">
                    Firmas
                  </h1>
                  <h5 className="card-subtitle mb-2 text-body-secondary">Validación de firmas en el documento
                  </h5>
                  <pre className="p-3 language-json"
                    style={{ height: '650px', overflow: 'scroll' }} ><code id="firmas" dangerouslySetInnerHTML={{ __html: firmas }}></code></pre>
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr className="p-0 m-0" />
        <div className="card-body bg-light">
          <div className="row mb-3">
            <div className="col-md-12 mb-3">
              <div className="card">
                <div className="card-body">
                  <div className="d-flex align-items-center">
                    <div className="flex-shrink-0">
                      <img width="70" src="assets/images/resl.png" alt="..." />
                    </div>
                    <div className="flex-grow-1 ms-3">
                      <h6 className="text-muted">CÓDIGO FUENTE</h6>
                      <h5>
                        Repositorio Estatal de Software Libre
                      </h5>
                      <a className="text-decoration-none text-black text-opacity-75"
                        href="https://gitlab.softwarelibre.gob.bo/adsib/jacobitus-total/envolturas/javascript/-/tree/master/ejemplos/nextjs"
                        target="_blank">https://gitlab.softwarelibre.gob.bo/adsib/jacobitus-total/envolturas/javascript/-/tree/master/ejemplos/nextjs</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="col-md-12 mb-3">
              <div className="card">
                <div className="card-body">
                  <div className="d-flex align-items-top">
                    <div className="flex-shrink-0">
                      <img src="assets/images/jacobitus-escritorio.png" alt="..." />
                    </div>
                    <div className="flex-grow-1 ms-3">
                      <h5 className="text-muted1">JACOBITUS DE ESCRITORIO</h5>
                      <span className="lead">Solución a todo requerimiento de firma digital</span>
                      <h4 className="text-muted mt-3">
                        Instalador
                      </h4>
                      <a className="text-decoration-none text-black text-opacity-75"
                        href="https://firmadigital.bo/herramientas/jacobitus-escritorio/"
                        target="_blank">https://firmadigital.bo/herramientas/jacobitus-escritorio/</a>
                      <h4 className="text-muted mt-3">
                        Documentación de interoperación
                      </h4>
                      <a className="text-decoration-none text-black text-opacity-75" href="https://localhost:9000/apidoc/"
                        target="_blank">https://localhost:9000/apidoc/</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="offset-md-9 col-md-3">
              <img className="img-fluid float-end" src="assets/images/potenciado-por-ADSIB-1.png"
                alt="POTENCIADO POR ADSIB" />
            </div>
          </div>
        </div>
      </div>
    </div >
  )
}
