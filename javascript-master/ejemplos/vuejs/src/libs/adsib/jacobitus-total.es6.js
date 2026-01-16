export class JacobitusTotal {
  constructor() {
    this.urlBase = 'https://localhost:9000/api';
  }

  async verificarEjecucion(versionMinima = null) {
    const parseVersion = (version) => version.split('.').map(Number);
    const compararVersion = (version, versionMinima) => {
      if (!versionMinima || versionMinima === '') return false;
      const [major, minor, patch] = parseVersion(version);
      const [minMajor, minMinor, minPatch] = parseVersion(versionMinima);

      if (major > minMajor) return true;
      if (major < minMajor) return false;

      if (minor > minMinor) return true;
      if (minor < minMinor) return false;

      return patch >= minPatch;
    };
    return await fetch(`${this.urlBase}/status`)
      .then((response) => response.json())
      .then((data) => {
        let versionCompatible = null;
        if (versionMinima !== null) {
          versionCompatible = compararVersion(data.datos.api_version, versionMinima);
        }

        if (data.finalizado) {
          if (versionCompatible !== null) {
            return {
              exito: true,
              mensaje: data.mensaje,
              datos: {
                compilacion: data.datos.compilacion,
                apiVersion: data.datos.api_version,
                versionCompatible
              }
            };
          } else {
            return {
              exito: true,
              mensaje: data.mensaje,
              datos: {
                compilacion: data.datos.compilacion,
                apiVersion: data.datos.api_version
              }
            };
          }
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error
      }));
  }

  async verificarDispositivo() {
    return await fetch(`${this.urlBase}/token/status`)
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: data.datos.connected,
            mensaje: data.mensaje,
            datos: {
              dispositivos: data.datos.tokens
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error
      }));
  }

  async obtenerDispositivos() {
    return await fetch(`${this.urlBase}/token/connected`)
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: data.datos.connected,
            mensaje: data.mensaje,
            datos: {
              dispositivos: data.datos.tokens.map((dispositivo) => ({
                slot: dispositivo.slot,
                serie: dispositivo.serial,
                nombre: dispositivo.name,
                modelo: dispositivo.model
              }))
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error
      }));
  }

  async obtenerCertificados(slot, pin) {
    return await fetch(`${this.urlBase}/token/data`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ slot, pin })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              dispositivo: {
                numCertificados: data.datos.data_token.certificates,
                certificados: [
                  ...data.datos.data_token.data.map((certificado) => {
                    if (certificado.tipo === 'PRIVATE_KEY') {
                      return {
                        tipo: certificado.tipo,
                        descripcion: certificado.tipo_desc,
                        alias: certificado.alias,
                        id: certificado.id,
                        tieneCertificado: certificado.tiene_certificado
                      };
                    } else {
                      return {
                        tipo: certificado.tipo,
                        descripcion: certificado.tipo_desc,
                        emitidoPorADSIB: certificado.adsib,
                        numSerie: certificado.serialNumber,
                        alias: certificado.alias,
                        id: certificado.id,
                        pem: certificado.pem,
                        validez: certificado.validez,
                        titular: certificado.titular,
                        nomTitular: certificado.common_name,
                        entidadEmisora: certificado.emisor
                      };
                    }
                  })
                ],
                numClavesPrivadas: data.datos.data_token.private_keys
              }
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error
      }));
  }

  async obtenerCertificadosParaFirmaDigital(slot, pin) {
    return await this.obtenerCertificados(slot, pin)
      .then((data) => {
        if (data.exito) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              certificados: data.datos.dispositivo.certificados.filter(
                (item) => item.tipo === 'X509_CERTIFICATE'
              )
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje,
            datos: {
              certificados: null
            }
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error
      }));
  }

  async firmarPdf(slot, pin, alias, pdfBase64) {
    pdfBase64 = pdfBase64.replace('data:application/pdf;base64,', '');
    return await fetch(`${this.urlBase}/token/firmar_pdf`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ slot, pin, alias, pdf: pdfBase64 })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              docFirmado: data.datos.pdf_firmado
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje,
            datos: {
              docFirmado: null
            }
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error
      }));
  }

  async firmarPdfModoSeguro(ci, pdfBase64) {
    pdfBase64 = pdfBase64.replace('data:application/pdf;base64,', '');
    return await fetch(`${this.urlBase}/token/sign`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        ci,
        pdfs: [{ id: 'documento.pdf', pdf: pdfBase64 }]
      })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado && data.datos && data.datos.pdfs_firmados && data.datos.pdfs_firmados.length > 0) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              docFirmado: data.datos.pdfs_firmados[0].pdf_firmado
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje,
            datos: {
              docFirmado: null
            }
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error
      }));
  }

  async firmarPkcs7(slot, pin, alias, archivoBase64) {
    return await fetch(`${this.urlBase}/token/firmar_pkcs7`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ slot, pin, alias, file: archivoBase64 })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              docFirmado: data.datos.pkcs7
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error,
        datos: {
          docFirmado: null
        }
      }));
  }

  async firmarXml(slot, pin, alias, xmlBase64) {
    return await fetch(`${this.urlBase}/token/firmar_xml`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ slot, pin, alias, file: xmlBase64 })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              docFirmado: data.datos.xml
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error,
        datos: {
          docFirmado: null
        }
      }));
  }

  async firmarJson(slot, pin, alias, jsonBase64) {
    return await fetch(`${this.urlBase}/token/firmar_json`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ slot, pin, alias, json: jsonBase64 })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              docFirmado: data.datos.json_firmado
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error,
        datos: {
          docFirmado: null
        }
      }));
  }

  async firmarJsonModoSeguro(ci, jsonBase64) {
    return await fetch(`${this.urlBase}/token/sign`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        ci,
        jsons: [{ id: 'documento.json', json: jsonBase64 }]
      })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado && data.datos && data.datos.jsons_firmados && data.datos.jsons_firmados.length > 0) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              docFirmado: data.datos.jsons_firmados[0].json_firmado
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje,
            datos: {
              docFirmado: null
            }
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error
      }));
  }

  async validarPdf(pdfBase64Firmado) {
    pdfBase64Firmado = pdfBase64Firmado?.replace(
      'data:application/pdf;base64,',
      ''
    );
    return await fetch(`${this.urlBase}/validar_pdf`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ pdf: pdfBase64Firmado })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              firmas: data.datos.firmas
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error,
        datos: {
          firmas: null
        }
      }));
  }

  async validarPkcs7(archivoBase64Firmado) {
    return await fetch(`${this.urlBase}/validar_pkcs7`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ file: archivoBase64Firmado })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              firmas: data.datos.firmas
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error,
        datos: {
          firmas: null
        }
      }));
  }

  async validarXml(xmlBase64Firmado) {
    return await fetch(`${this.urlBase}/validar_xml`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ file: xmlBase64Firmado })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              firmas: data.datos.firmas
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error,
        datos: {
          firmas: null
        }
      }));
  }

  async validarJws(jwsBase64Firmado) {
    return await fetch(`${this.urlBase}/validar_jws`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ file: jwsBase64Firmado })
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.finalizado) {
          return {
            exito: true,
            mensaje: data.mensaje,
            datos: {
              firmas: data.datos.firmas
            }
          };
        } else {
          return {
            exito: false,
            mensaje: data.mensaje
          };
        }
      })
      .catch((error) => ({
        exito: false,
        mensaje: error,
        datos: {
          firmas: null
        }
      }));
  }
}

export const jacobitusTotal = new JacobitusTotal();
