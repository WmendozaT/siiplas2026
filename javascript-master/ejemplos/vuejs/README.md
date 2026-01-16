<img src="./assets/images/logo-adsib.png" alt="ADSIB" align="center" />

# VueJS 2 - Jacobitus TOTAL

Este módulo para VueJS 2 abstrae y encapsula el uso del software de firma digital **Jacobitus TOTAL** para su uso en cualquier aplicación web de manera rápida y sencilla.

> También puede ser utilizado con el Jacobitus FIDO.

## Jacobitus TOTAL
Esta aplicación permite administrar un dispositivo criptográfico (token) y certificados digitales almacenados en software, de manera intuitiva y sencilla.

También le permite interactuar con el sistema de solicitud de Firma Digital y firmar archivos PDF y otros formatos (PKCS7).

La aplicación Jacobitus TOTAL ofrece compatibilidad para su uso sobre el puerto 3200 (Asistente Firmador) y el puesto 4637 (Firmatic), ofreciendo de esta manera una solución integral de firma digital.

## Descargar
Descarga el proyecto desde el Repositorio Estatal de Software Libre
```bash
git clone https://gitlab.softwarelibre.gob.bo/adsib/vuejs-jacobitus-total.git
```

## Dependencias
- [axios v0.19.2+](https://axios-http.com/)

## Instalación

Copia el archivo **adsib.firma-digital.js** en tu proyecto.

Importa el módulo en el componente en el cual desees utilizarlo.
```javascript
import ADSIB from '@/libs/adsib.firma-digital'
```

## Como usar
El módulo permite firmar y validar documentos, la firma se realiza de la siguiente manera:
```javascript
const archivoPdfBase64Firmado = await ADSIB.firmaDigital.jacobitusTotal.firmarPdf({archivoPdfBase64, pin}, progresoCallback);
```

La validación esta soportada solamente por el Jacobitus Total y el Jacobitus FIDO y se realiza de la siguiente manera.
```javascript
const validacionPdfBase64Firmado = await ADSIB.firmaDigital.jacobitusTotal.validarPdf({archivoPdfBase64}, progresoCallback);
```

## Parámetros
El módulo tiene compatibilidad con los siguientes firmadores:

- ADSIB.firmaDigital.jacobitusFIDO
- ADSIB.firmaDigital.jacobitusTotal
- ADSIB.firmaDigital.asistenteFirmador
- ADSIB.firmaDigital.firmatic

<table>
<thead>
<th>Parámetro</th><th>Tipo</th><th>Valor por defecto</th><th>Descripción</th>
</thead>
<tbody>
<tr>
<td>datos</td><td>object</td><td>null</td><td>

```javascript
{
  archivoBase64: ''
}
```

En caso de utilizar la compatibilidad con el Firmatic

```javascript
{
  ci: '',
  archivoBase64: ''
}
```

</td>
</tr>
</tbody>
</table>

Puede ejecutar el proyecto de ejemplo de uso a través del commando
```bash
$ npm run serve
```
Esto iniciará un servidor local para la prueba del módulo.

## Licencia
[LICENCIA PÚBLICA GENERAL](LICENSE.md)<br>
de Consideraciones y Registro de Software Libre en Bolivia<br>(LPG-Bolivia)

<img src="./assets/images/potenciado-por-ADSIB-1.png" width="300" alt="FIRMA DIGITAL potenciada por ADSIB" align="right" />
