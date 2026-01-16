# Libreria de envoltura para interoperar el Jacobitus Total

Este módulo para javascript abstrae y encapsula el uso del software de firma digital **Jacobitus TOTAL** para su uso en cualquier aplicación web de manera rápida y sencilla.

## Jacobitus TOTAL
Esta aplicación permite administrar un dispositivo criptográfico (token) y certificados digitales almacenados en software, de manera intuitiva y sencilla.

Asimismo, permite firmar y validar documentos PDF entre otros formatos (PKCS7).

## Vanilla JS

### Instalación

Copie el archivo **jacobitus-total.browser.js** en la carpeta **assets/vendor/adsib** de su proyecto.

Debe agregar la siguiente importación en la página en la cual desea utilizarlo.
```html
<script type="text/javascript" src="./assets/vendor/adsib/jacobitus-total.browser.js"></script>
```

### Como usar
El módulo permite firmar y validar documentos, la firma se realiza de la siguiente manera:
```javascript
const respuesta = await jacobitusTotal.firmarPdf(slot, pin, alias, archivoPdf);
const docFirmado = respuesta.datos.docFirmado;
```

Donde:
- **slot**: Es el número de slot que ocupa el dispositivo criptográfico (softoken = -1, token >= 1)
- **pin**: Código de seguridad de acceso al dispositivo criptográfico.
- **alias**: Identificador del certificado de firma digital.
    - Para obtener el alias puede utilizar el siguiente código:
        ```javascript
        let respuesta = await jacobitusTotal.obtenerCertificadosParaFirmaDigital(slot, pin);
        const alias = respuesta.datos.certificados[0].alias;
        ```
- **archivoPdf**: Es el documento PDF en formato base64 que se desea firmar digitalmente.

La validación se realiza de la siguiente manera:
```javascript
const respuesta = await jacobitusTotal.validarPdf(archivoPdf);
const firmas = respuesta.datos.firmas;
```

Donde:
- **archivoPdf**: Es el documento PDF firmado digitalmente en formato base64 que se desea validar.

>
> **NOTA**
>
> Puede ejecutar un proyecto de ejemplo desde la carpeta **ejemplos/vanillajs**.
>

## VueJS

### Instalación

Copie el archivo **jacobitus-total.es6.js** en la carpeta **src/libs/adsib** de su proyecto.

Importe el módulo en el componente en el cual desea utilizarlo.
```javascript
import { jacobitusTotal } from '@/libs/adsib/jacobitus-total.es6'
```

### Como usar
El módulo permite firmar y validar documentos, la firma se realiza de la siguiente manera:
```javascript
const respuesta = await jacobitusTotal.firmarPdf(slot, pin, alias, archivoPdf);
const docFirmado = respuesta.datos.docFirmado;
```

Donde:
- **slot**: Es el número de slot que ocupa el dispositivo criptográfico (softoken = -1, token >= 1)
- **pin**: Código de seguridad de acceso al dispositivo criptográfico.
- **alias**: Identificador del certificado de firma digital.
    - Para obtener el alias puede utilizar el siguiente código:
        ```javascript
        let respuesta = await jacobitusTotal.obtenerCertificadosParaFirmaDigital(slot, pin);
        const alias = respuesta.datos.certificados[0].alias;
        ```
- **archivoPdf**: Es el documento PDF en formato base64 que se desea firmar digitalmente.

La validación se realiza de la siguiente manera:
```javascript
const respuesta = await jacobitusTotal.validarPdf(archivoPdf);
const firmas = respuesta.datos.firmas;
```

Donde:
- **archivoPdf**: Es el documento PDF firmado digitalmente en formato base64 que se desea validar.

>
> **NOTA**
>
> Puede ejecutar un proyecto de ejemplo desde la carpeta **ejemplos/vuejs**.
>

## NextJS

### Instalación

Copie el archivo **jacobitus-total.es6.js** en la carpeta **src/libs/adsib** de su proyecto.

Importe el módulo en el componente en el cual desea utilizarlo.
```javascript
import { jacobitusTotal } from '../libs/adsib/jacobitus-total.es6';
```

### Como usar
El módulo permite firmar y validar documentos, la firma se realiza de la siguiente manera:
```javascript
const respuesta = await jacobitusTotal.firmarPdf(slot, pin, alias, archivoPdf);
const docFirmado = respuesta.datos.docFirmado;
```

Donde:
- **slot**: Es el número de slot que ocupa el dispositivo criptográfico (softoken = -1, token >= 1)
- **pin**: Código de seguridad de acceso al dispositivo criptográfico.
- **alias**: Identificador del certificado de firma digital.
    - Para obtener el alias puede utilizar el siguiente código:
        ```javascript
        let respuesta = await jacobitusTotal.obtenerCertificadosParaFirmaDigital(slot, pin);
        const alias = respuesta.datos.certificados[0].alias;
        ```
- **archivoPdf**: Es el documento PDF en formato base64 que se desea firmar digitalmente.

La validación se realiza de la siguiente manera:
```javascript
const respuesta = await jacobitusTotal.validarPdf(archivoPdf);
const firmas = respuesta.datos.firmas;
```

Donde:
- **archivoPdf**: Es el documento PDF firmado digitalmente en formato base64 que se desea validar.

>
> **NOTA**
>
> Puede ejecutar un proyecto de ejemplo desde la carpeta **ejemplos/nextjs**.
>

## Licencia
[LICENCIA PÚBLICA GENERAL](LICENSE.md)<br>
de Consideraciones y Registro de Software Libre en Bolivia<br>(LPG-Bolivia)

<img src="./assets/images/potenciado-por-ADSIB-1.png" width="300" alt="FIRMA DIGITAL potenciada por ADSIB" align="right" />
