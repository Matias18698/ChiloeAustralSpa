const Ziggy = {
  "url": "http:\/\/localhost",
  "port": null,
  "defaults": {},
  "routes": {
    // Rutas ya definidas
    "sanctum.csrf-cookie": { "uri": "sanctum/csrf-cookie", "methods": ["GET", "HEAD"] },
    "dashboard": { "uri": "dashboard", "methods": ["GET", "HEAD"] },
    "profile.edit": { "uri": "profile", "methods": ["GET", "HEAD"] },
    "profile.update": { "uri": "profile", "methods": ["PATCH"] },
    "profile.destroy": { "uri": "profile", "methods": ["DELETE"] },
    "trabajador.index": { "uri": "dashboard/trabajadores", "methods": ["GET", "HEAD"] },
    "trabajador.create": { "uri": "dashboard/trabajadores/create", "methods": ["GET", "HEAD"] },
    "trabajador.store": { "uri": "dashboard/trabajadores", "methods": ["POST"] },
    "trabajador.show": { "uri": "dashboard/trabajadores/{trabajador}", "methods": ["GET", "HEAD"], "parameters": ["trabajador"] },
    "trabajador.edit": { "uri": "dashboard/trabajadores/{trabajador}/edit", "methods": ["GET", "HEAD"], "parameters": ["trabajador"], "bindings": { "trabajador": "id" } },
    "trabajador.update": { "uri": "dashboard/trabajadores/{trabajador}", "methods": ["POST"], "parameters": ["trabajador"], "bindings": { "trabajador": "id" } },
    "trabajador.destroy": { "uri": "dashboard/trabajadores/{trabajador}", "methods": ["DELETE"], "parameters": ["trabajador"], "bindings": { "trabajador": "id" } },
    "asistencia.index": { "uri": "dashboard/asistencias", "methods": ["GET", "HEAD"] },
    "asistencia.create": { "uri": "dashboard/asistencias/create", "methods": ["GET", "HEAD"] },
    "asistencia.store": { "uri": "dashboard/asistencias", "methods": ["POST"] },
    "asistencia.show": { "uri": "dashboard/asistencias/{asistencia}", "methods": ["GET", "HEAD"], "parameters": ["asistencia"] },
    "asistencia.edit": { "uri": "dashboard/asistencias/{asistencia}/edit", "methods": ["GET", "HEAD"], "parameters": ["asistencia"] },
    "asistencia.update": { "uri": "dashboard/asistencias/{asistencia}", "methods": ["post"], "parameters": ["asistencia"] },
    "asistencia.destroy": { "uri": "dashboard/asistencias/{asistencia}", "methods": ["DELETE"], "parameters": ["asistencia"] },
    
    // Nuevas rutas de embarcaciones
    "embarcacion.index": { "uri": "dashboard/embarcaciones", "methods": ["GET", "HEAD"] },
    "embarcacion.create": { "uri": "dashboard/embarcaciones/create", "methods": ["GET", "HEAD"] },
    "embarcacion.store": { "uri": "dashboard/embarcaciones", "methods": ["POST"] },
    "embarcacion.show": { "uri": "dashboard/embarcaciones/{embarcacion}", "methods": ["GET", "HEAD"], "parameters": ["embarcacion"] },
    "embarcacion.edit": { "uri": "dashboard/embarcaciones/{embarcacion}/edit", "methods": ["GET", "HEAD"], "parameters": ["embarcacion"] },
    "embarcacion.update": { "uri": "dashboard/embarcaciones/{embarcacion}", "methods": ["post"], "parameters": ["embarcacion"] },
    "embarcacion.destroy": { "uri": "dashboard/embarcaciones/{embarcacion}", "methods": ["DELETE"], "parameters": ["embarcacion"] },

    // Nuevas rutas de autenticación y verificación
    "login": { "uri": "login", "methods": ["GET", "HEAD"] },
    "register": { "uri": "register", "methods": ["GET", "HEAD"] },
    "password.request": { "uri": "forgot-password", "methods": ["GET", "HEAD"] },
    "password.email": { "uri": "forgot-password", "methods": ["POST"] },
    "password.reset": { "uri": "reset-password/{token}", "methods": ["GET", "HEAD"], "parameters": ["token"] },
    "password.store": { "uri": "reset-password", "methods": ["POST"] },
    "verification.notice": { "uri": "verify-email", "methods": ["GET", "HEAD"] },
    "verification.verify": { "uri": "verify-email/{id}/{hash}", "methods": ["GET", "HEAD"], "parameters": ["id", "hash"] },
    "verification.send": { "uri": "email/verification-notification", "methods": ["POST"] },
    "password.confirm": { "uri": "confirm-password", "methods": ["GET", "HEAD"] },
    "password.update": { "uri": "password", "methods": ["post"] },
    "logout": { "uri": "logout", "methods": ["POST"] }
  }
};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
