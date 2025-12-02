# Guía para Cambiar a Modo Sandbox de PayPal

## ¿Qué es el Modo Sandbox?

El modo **Sandbox** de PayPal es un entorno de pruebas que te permite:
- ✅ Probar pagos sin dinero real
- ✅ Verificar que todo funciona correctamente
- ✅ Crear cuentas de prueba de compradores y vendedores
- ✅ Simular transacciones completas

## 📋 Pasos para Cambiar a Modo Sandbox

### 1. Accede a la Configuración de Pagos

1. Inicia sesión en tu CRM como **superadmin**
2. Ve a **Configuración** (⚙️ en el menú)
3. Haz clic en **Pagos** o **Configuración de Pagos**

### 2. Configura las Credenciales de Sandbox

1. En el campo **"Modo"**, selecciona **"Sandbox (Pruebas)"**
2. Si aún no tienes credenciales de Sandbox, obtén las aquí:

#### Cómo obtener credenciales de Sandbox:

**a) Accede al Portal de Desarrolladores de PayPal:**
   - Ve a: https://developer.paypal.com
   - Inicia sesión con tu cuenta de PayPal

**b) Crea o Selecciona una App:**
   - En el Dashboard, ve a **"My Apps & Credentials"**
   - Asegúrate de estar en la pestaña **"Sandbox"** (no "Live")
   - Si no tienes una app, haz clic en **"Create App"**
     - Nombre de la app: "CRM Total" (o el nombre que prefieras)
     - Selecciona tu cuenta Sandbox como vendedor
     - Haz clic en "Create App"

**c) Obtén las Credenciales:**
   - Una vez creada la app, verás:
     - **Client ID** - Cópialo completo
     - **Secret** - Haz clic en "Show" y cópialo

**d) Configura en el CRM:**
   - Pega el **Client ID** en el campo correspondiente
   - Pega el **Secret** en el campo correspondiente
   - Asegúrate de que **Modo** esté en **"Sandbox (Pruebas)"**
   - Haz clic en **"Guardar Configuración"**

### 3. Crea Cuentas de Prueba

Para probar pagos, necesitas cuentas de prueba:

**a) Crear Cuenta de Comprador (Buyer):**
   - Ve a: https://developer.paypal.com/dashboard/accounts
   - Haz clic en **"Create Account"**
   - Selecciona:
     - Country: Mexico
     - Account Type: Personal
     - Email: (se genera automáticamente)
     - Password: (elige uno y guárdalo)
   - Haz clic en "Create Account"

**b) Anotar Credenciales:**
   - Guarda el email y password que te proporciona PayPal
   - Estas credenciales las usarás para "pagar" en las pruebas

### 4. Prueba el Flujo de Pago

1. **Crea o abre una membresía** en el CRM
2. Copia el **"Enlace de Pago Público"**
3. Abre el enlace en una ventana de incógnito o en otro navegador
4. Haz clic en el botón de **PayPal**
5. Inicia sesión con la **cuenta de comprador (buyer)** que creaste
6. Completa el pago (recuerda: es dinero ficticio)
7. Verifica que el sistema te redirija correctamente

## 🔍 Verificar la Configuración

Puedes usar el script de verificación para asegurarte de que todo está configurado correctamente:

```
https://enlacecanaco.org/crmtotal/43/verificar_paypal.php
```

Este script te mostrará:
- ✅ Si la autenticación funciona
- 📦 Los productos creados en PayPal Sandbox
- 📋 Los planes de suscripción
- 💾 Las membresías vinculadas con PayPal

## ⚠️ Importante

### Diferencias entre Sandbox y Live:

| Aspecto | Sandbox (Pruebas) | Live (Producción) |
|---------|------------------|-------------------|
| Dinero | Ficticio | Real |
| Cuentas | De prueba | Reales |
| API URL | `sandbox.paypal.com` | `paypal.com` |
| Propósito | Desarrollo y pruebas | Pagos reales de clientes |

### Cuándo usar cada modo:

- **Sandbox**: 
  - Durante el desarrollo
  - Para hacer pruebas
  - Para capacitar al personal
  - Para demos a clientes

- **Live**: 
  - Cuando estés 100% seguro de que todo funciona
  - Cuando tengas las credenciales de producción
  - Cuando tengas una cuenta Business de PayPal verificada
  - Cuando estés listo para recibir pagos reales

## 🚀 Cambiar a Producción (Live)

Cuando estés listo para recibir pagos reales:

1. Obtén credenciales **Live** de PayPal:
   - Ve a https://developer.paypal.com
   - En "My Apps & Credentials", cambia a la pestaña **"Live"**
   - Crea una nueva app o usa una existente
   - Copia el **Client ID** y **Secret** de producción

2. En el CRM:
   - Ve a **Configuración** → **Pagos**
   - Cambia el **Modo** a **"Live (Producción)"**
   - Actualiza las credenciales con las de producción
   - Guarda los cambios

3. **¡IMPORTANTE!**: Antes de cambiar a Live:
   - ✅ Prueba exhaustivamente en Sandbox
   - ✅ Verifica todos los flujos de pago
   - ✅ Asegúrate de que las notificaciones funcionen
   - ✅ Confirma que la cuenta de PayPal Business esté verificada
   - ✅ Ten un plan de respaldo en caso de problemas

## 🆘 Solución de Problemas

### El botón de PayPal no aparece:
- ✅ Verifica que la membresía tenga un `paypal_product_id`
- ✅ Confirma que las credenciales estén configuradas
- ✅ Revisa la consola del navegador (F12) en busca de errores

### Error al crear la orden:
- ✅ Verifica que el modo esté en "Sandbox"
- ✅ Confirma que las credenciales sean de Sandbox
- ✅ Revisa los logs del servidor

### El pago no se completa:
- ✅ Usa una cuenta de prueba válida
- ✅ Verifica que la cuenta tenga "fondos" suficientes
- ✅ Revisa los webhooks de PayPal

## 📞 Soporte

Si necesitas ayuda adicional:
- 📧 Email: soporte@tudominio.com
- 📚 Documentación de PayPal: https://developer.paypal.com/docs/
- 🔧 Script de verificación: `/verificar_paypal.php`

---

**Última actualización**: Diciembre 2025
