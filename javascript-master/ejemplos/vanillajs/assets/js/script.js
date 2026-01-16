async function jacobitusEnEjecucion(versionMinima = null) {
  const estado = await jacobitusTotal.verificarEjecucion(versionMinima);
  const versionActual = estado.datos.apiVersion;
  const versionCompatible = estado.datos.versionCompatible ?? true;
  return {
    enEjecucion: estado.exito,
    versionActual,
    versionCompatible
  };
}

async function dispositivoConectado() {
  const estado = await jacobitusTotal.verificarDispositivo();
  return estado.exito;
}

function obtenerBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
  });
}

function cargarArchivoBase64(origenId, destinoId, validacionId) {
  FreezeUI({ text: 'Cargando documento' });
  const archivoPdf = document.getElementById(origenId).files[0];
  obtenerBase64(archivoPdf).then((archivoPdfBase64) => {
    document.getElementById(destinoId).src = archivoPdfBase64;
    document.getElementById(destinoId).onload = function () {
      if (validacionId) {
        validarPdf(destinoId, validacionId);
      } else {
        UnFreezeUI();
      }
    }
  });
}

async function firmarPdf(origenId, destinoId, validacionId) {
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
      FreezeUI({ text: 'Firmando documento' });
      const slot = 1;
      let respuesta = await jacobitusTotal.obtenerCertificadosParaFirmaDigital(slot, pin);
      if (respuesta.datos.certificados && respuesta.datos.certificados.length > 0) {
        const alias = respuesta.datos.certificados[0].alias;
        respuesta = await jacobitusTotal.firmarPdf(slot, pin, alias, archivoPdf);
        document.getElementById(destinoId).src = `data:application/pdf;base64,${respuesta.datos.docFirmado}`;
        document.getElementById(destinoId).onload = function () {
          if (validacionId) {
            validarPdf(destinoId, validacionId);
          } else {
            UnFreezeUI();
          }
        }
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

async function firmarPdfModoSeguro(origenId, destinoId, validacionId) {
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
      FreezeUI({ text: 'Firmando documento' });
      const respuesta = await jacobitusTotal.firmarPdfModoSeguro(ci, archivoPdf);
      if (respuesta.exito) {
        document.getElementById(destinoId).src = `data:application/pdf;base64,${respuesta.datos.docFirmado}`;
        document.getElementById(destinoId).onload = function () {
          if (validacionId) {
            validarPdf(destinoId, validacionId);
          } else {
            UnFreezeUI();
          }
        }
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

async function validarPdf(origenId, destinoId) {
  FreezeUI({ text: 'Validando firmas' });
  const archivoPdf = document.getElementById(origenId).src;
  const respuesta = await jacobitusTotal.validarPdf(archivoPdf);
  document.getElementById(destinoId).innerHTML = JSON.stringify(respuesta.datos.firmas, null, 4);
  Prism.highlightAll();
  UnFreezeUI();
}
