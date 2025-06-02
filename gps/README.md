# 🏍️ GPX Navigator PWA - Navegación GPS para Motos

Una Progressive Web App (PWA) profesional para navegación GPS con archivos GPX, optimizada específicamente para uso en motocicleta.

## ✨ Características principales

### 🎯 Navegación profesional
- **Navegación GPS en tiempo real** con indicaciones paso a paso
- **Detección automática de desvío de ruta** y recálculo
- **Instrucciones de voz visual** optimizadas para moto
- **Modo pantalla completa** para navegación inmersiva
- **Mantiene la pantalla encendida** durante la navegación

### 🗺️ Mapas y rutas
- **Múltiples capas de mapa**: OpenStreetMap, Vista satélite, Terreno
- **Carga archivos GPX** arrastrando y soltando
- **Visualización detallada de rutas** con marcadores de inicio/fin
- **Cálculo automático de distancias** y tiempos estimados

### 📱 Optimización móvil y PWA
- **Funciona offline** una vez cargada
- **Instalable como app nativa** en Android/iOS
- **Interface táctil optimizada** para guantes de moto
- **Responsive design** para todos los tamaños de pantalla
- **Controles grandes** fáciles de usar en movimiento

### 🔧 Características técnicas
- **Service Worker avanzado** para cache inteligente
- **Geolocalización de alta precisión**
- **Brújula y velocímetro integrados**
- **Modo seguimiento automático**
- **Integración con OSRM** para routing dinámico

## 📋 Requisitos

- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- GPS habilitado en el dispositivo
- Conexión a internet para carga inicial y tiles de mapa
- Permisos de geolocalización

## 🚀 Instalación

### Opción 1: Acceso directo en navegador
1. Abre el archivo `index.html` en tu navegador web
2. Permite el acceso a la ubicación cuando se solicite
3. ¡Listo para usar!

### Opción 2: Instalación como PWA
1. Abre la aplicación en tu navegador móvil
2. Busca el botón "📱 Instalar App" en la esquina inferior derecha
3. Selecciona "Añadir a pantalla de inicio" o "Instalar"
4. La app se instalará como una aplicación nativa

### Opción 3: Servidor web local
```bash
# Usando Python (recomendado para desarrollo)
python -m http.server 8000

# Usando Node.js
npx serve .

# Acceder a http://localhost:8000
```

### Archivos necesarios
```
gpx-navigator/
├── index.html          # Aplicación principal
├── sw.js              # Service Worker
├── manifest.json      # Manifest PWA
└── README.md          # Esta guía
```

## 🎮 Cómo usar

### 1. Cargar archivo GPX
- Arrastra un archivo `.gpx` a la zona de carga
- O haz clic en "Seleccionar archivo GPX"
- La ruta se visualizará automáticamente en el mapa

### 2. Iniciar navegación
- Toca el botón 🧭 en los controles flotantes
- Permite acceso a ubicación GPS
- El panel de navegación aparecerá automáticamente

### 3. Durante la navegación
- **Panel principal**: Muestra instrucción actual y distancia
- **Velocímetro**: Velocidad actual y rumbo
- **Controles flotantes**: Cambiar capas de mapa, centrar posición
- **Modo pantalla completa**: Para navegación inmersiva

### 4. Controles de navegación
- **📍 Seguimiento automático**: Centra el mapa en tu posición
- **🎯 Centrar posición**: Centra manualmente en tu ubicación
- **🗺️ Capas de mapa**: Cambia entre vista normal, satélite y terreno
- **⏹️ Detener**: Termina la navegación

## 🔧 Características avanzadas

### Navegación fuera de ruta
- **Detección automática** cuando te sales de la ruta (50m tolerancia)
- **Recálculo inteligente** después de 5 segundos fuera de ruta
- **Ruta de retorno** usando OpenStreetMap Routing Machine (OSRM)
- **Indicaciones visuales** claras para volver a la ruta

### Modo offline
- **Cache inteligente** de tiles de mapa visitados
- **Funcionamiento offline** una vez cargada la ruta
- **Fallback automático** a cache cuando no hay conexión
- **Limpieza automática** de cache antiguo

### Optimización para moto
- **Controles grandes** fáciles de usar con guantes
- **Colores contrastados** para visibilidad con casco
- **Pantalla siempre encendida** durante navegación
- **Interface minimalista** para evitar distracciones

## 📱 Compatibilidad de dispositivos

### ✅ Totalmente compatible
- **Android**: Chrome, Firefox, Samsung Internet
- **iOS**: Safari, Chrome
- **Desktop**: Chrome, Firefox, Edge, Safari

### ⚠️ Limitaciones conocidas
- iOS Safari: Algunas limitaciones en background processing
- Navegadores antiguos: Pueden requerir polyfills
- GPS indoor: Precisión limitada en interiores

## 🔒 Privacidad y seguridad

### 🛡️ Características de privacidad
- **Procesamiento local**: Los archivos GPX no se envían a servidores
- **No tracking**: No se recopilan datos de ubicación
- **Cache local**: Los mapas se guardan solo en tu dispositivo
- **Sin registro**: No requiere cuentas ni registro

### 🔐 Permisos requeridos
- **Geolocalización**: Para navegación GPS
- **Almacenamiento**: Para cache de mapas offline
- **Pantalla activa**: Para mantener encendida durante navegación

## 🛠️ Desarrollo y personalización

### Estructura del código
```javascript
// Variables principales
let map;                    // Instancia de Leaflet
let routePoints = [];      // Puntos de la ruta GPX
let navigationActive;      // Estado de navegación
let currentPosition;       // Posición GPS actual

// Funciones principales
initializeApp()           // Inicialización
loadGPXContent()         // Carga archivo GPX
startNavigation()        // Inicia GPS
updatePosition()         // Actualiza posición
calculateReturnToRoute() // Recalcula ruta
```

### Personalización de mapas
```javascript
// Añadir nuevas capas de mapa
const customLayer = L.tileLayer('https://tu-servidor/{z}/{x}/{y}.png', {
    attribution: 'Tu atribución'
});

// Modificar estilos de ruta
const routeStyle = {
    color: "#007bff",
    weight: 5,
    opacity: 0.8
};
```

### Configuración de navegación
```javascript
// Tolerancia para detección de desvío (metros)
const OFF_ROUTE_TOLERANCE = 50;

// Tiempo antes de recalcular ruta (ms)
const RECALCULATION_DELAY = 5000;

// Precisión GPS requerida
const GPS_OPTIONS = {
    enableHighAccuracy: true,
    maximumAge: 1000,
    timeout: 10000
};
```

## 🐛 Resolución de problemas

### GPS no funciona
1. Verifica permisos de ubicación en el navegador
2. Asegúrate de tener señal GPS clara
3. Reinicia la navegación si es necesario
4. Prueba en modo incógnito para descartar cache

### Mapas no cargan
1. Verifica conexión a internet
2. Limpia cache del navegador
3. Recarga la página completamente
4. Prueba con otra capa de mapa

### Archivo GPX no se carga
1. Verifica que sea un archivo `.gpx` válido
2. Asegúrate de que contiene puntos de track/ruta
3. Prueba con un archivo GPX más simple
4. Verifica el tamaño del archivo (< 10MB recomendado)

### Navegación imprecisa
1. Espera a obtener mejor señal GPS
2. Verifica que los datos del archivo GPX son correctos
3. Calibra la brújula del dispositivo
4. Reinicia la navegación

### Performance lento
1. Cierra otras pestañas del navegador
2. Limpia cache de la aplicación
3. Reduce el zoom del mapa
4. Usa archivo GPX con menos puntos

## 🔄 Actualizaciones

### Auto-actualización
- La PWA se actualiza automáticamente al detectar cambios
- Cache se regenera con nuevas versiones
- Service Worker maneja las actualizaciones transparentemente

### Actualización manual
1. Cierra completamente la aplicación
2. Limpia cache del navegador
3. Recarga la página
4. Reinstala la PWA si es necesario

## 📞 Soporte

### Para desarrolladores
- Inspecciona la consola del navegador para logs detallados
- Usa las herramientas de desarrollador para debugging
- Verifica el estado del Service Worker en `chrome://serviceworker-internals/`

### Issues comunes
- **"GPS not supported"**: Navegador muy antiguo
- **"Location denied"**: Permisos de ubicación denegados
- **"Route calculation failed"**: Sin conexión o servidor OSRM caído
- **"GPX parse error"**: Archivo GPX corrupto o inválido

## 📜 Licencia

Este proyecto es de código abierto. Puedes usar, modificar y distribuir libremente.

## 🙏 Agradecimientos

- **Leaflet**: Biblioteca de mapas interactivos
- **OpenStreetMap**: Datos de mapas libres
- **OSRM**: Motor de routing
- **Esri**: Imágenes satelitales

---

**¡Disfruta navegando con seguridad en tu moto! 🏍️✨**