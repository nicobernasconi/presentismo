# API REST - Documentación para Flutter

## Base URL
```
https://tu-servidor.com/public/index.php?route=/api/v1
```

## Autenticación

La API usa tokens JWT (Bearer Token). Después del login, incluir el token en todas las peticiones:

```dart
headers: {
  'Authorization': 'Bearer $token',
  'Content-Type': 'application/json',
}
```

---

## Endpoints

### 🔐 Autenticación

#### POST /auth/login
Iniciar sesión y obtener token.

**Request:**
```json
{
  "email": "empleado@empresa.com",
  "password": "contraseña",
  "device_name": "Samsung Galaxy S21",
  "platform": "android",
  "app_version": "1.0.0"
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 2592000,
    "user": {
      "id": 1,
      "tenant_id": 1,
      "name": "Juan Pérez",
      "first_name": "Juan",
      "last_name": "Pérez",
      "email": "juan@empresa.com",
      "employee_code": "EMP001",
      "dni": "12345678A",
      "phone": "666123456",
      "position": "Desarrollador",
      "department": "Tecnología",
      "work_center": "Oficina Central",
      "work_center_id": 1,
      "avatar_url": "/uploads/avatars/juan.jpg",
      "role": {
        "id": 3,
        "name": "Empleado"
      },
      "shift": {
        "id": 1,
        "name": "Turno Mañana",
        "start_time": "08:00:00",
        "end_time": "16:00:00",
        "working_days": [1, 2, 3, 4, 5]
      },
      "clock_status": {
        "is_clocked_in": false,
        "clock_in_time": null,
        "clock_out_time": null,
        "elapsed_time": "0:00",
        "status": "out"
      },
      "is_active": true,
      "language": "es"
    }
  }
}
```

**Response 401:**
```json
{
  "success": false,
  "message": "Credenciales incorrectas"
}
```

---

#### POST /auth/logout
Cerrar sesión e invalidar token.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "message": "Sesión cerrada correctamente",
  "data": []
}
```

---

#### GET /auth/me
Obtener datos del usuario autenticado.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "user": { ... }
  }
}
```

---

#### POST /auth/refresh
Renovar token antes de que expire.

**Headers:** `Authorization: Bearer {token_actual}`

**Response 200:**
```json
{
  "success": true,
  "message": "Token renovado",
  "data": {
    "token": "nuevo_token...",
    "token_type": "Bearer",
    "expires_in": 2592000
  }
}
```

---

### ⏰ Fichajes (Time Entries)

#### GET /time-entries/status
Obtener estado actual del fichaje del usuario.

**Headers:** `Authorization: Bearer {token}`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "clock_status": {
      "is_clocked_in": true,
      "last_action": {
        "type": "clock_in",
        "time": "2024-01-15 08:02:00",
        "method": "qr"
      },
      "worked_today": {
        "minutes": 245,
        "formatted": "04:05"
      },
      "current_session_start": "2024-01-15 08:02:00"
    },
    "current_shift": {
      "id": 1,
      "name": "Turno Mañana",
      "start_time": "08:00:00",
      "end_time": "16:00:00",
      "working_days": [1, 2, 3, 4, 5],
      "is_work_day": true
    },
    "today_entries": [
      {
        "id": 123,
        "type": "clock_in",
        "time": "08:02:00",
        "method": "qr",
        "status": "on_time"
      }
    ],
    "today_summary": {
      "date": "2024-01-15",
      "worked_minutes": 245,
      "worked_formatted": "04:05",
      "expected_minutes": 480,
      "expected_formatted": "08:00",
      "difference_minutes": -235,
      "difference_formatted": "-03:55",
      "entries_count": 1
    }
  }
}
```

---

#### POST /time-entries/clock-in
Registrar entrada (fichaje).

**Headers:** `Authorization: Bearer {token}`

**Request (Manual):**
```json
{
  "method": "manual"
}
```

**Request (QR):**
```json
{
  "method": "qr",
  "qr_token": "wc_abc123..."
}
```

**Request (Geolocalización):**
```json
{
  "method": "geolocation",
  "latitude": 40.4168,
  "longitude": -3.7038
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Entrada registrada correctamente",
  "data": {
    "entry": {
      "id": 124,
      "type": "clock_in",
      "recorded_at": "2024-01-15 08:02:00",
      "method": "qr",
      "work_center": "Oficina Central"
    },
    "clock_status": { ... },
    "shift_validation": {
      "status": "on_time",
      "message": "Entrada registrada a tiempo",
      "shift_id": 1,
      "expected_time": "08:00:00",
      "actual_time": "08:02:00",
      "late_minutes": 2
    }
  }
}
```

**Response 400 (Ya fichó):**
```json
{
  "success": false,
  "message": "Ya has fichado entrada hoy. Debes fichar salida primero."
}
```

---

#### POST /time-entries/clock-out
Registrar salida (fichaje).

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "method": "geolocation",
  "latitude": 40.4168,
  "longitude": -3.7038
}
```

**Response 200:**
```json
{
  "success": true,
  "message": "Salida registrada correctamente",
  "data": {
    "entry": {
      "id": 125,
      "type": "clock_out",
      "recorded_at": "2024-01-15 16:05:00",
      "method": "geolocation"
    },
    "clock_status": {
      "is_clocked_in": false,
      ...
    },
    "shift_validation": {
      "status": "overtime",
      "message": "Salida con horas extra",
      "overtime_minutes": 5
    },
    "today_summary": {
      "worked_minutes": 483,
      "worked_formatted": "08:03",
      "expected_minutes": 480,
      "expected_formatted": "08:00",
      "difference_minutes": 3,
      "difference_formatted": "+00:03"
    }
  }
}
```

---

#### POST /time-entries/qr
Fichar con QR (auto-detecta entrada/salida).

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "qr_token": "wc_abc123...",
  "action": "auto"  // "auto" | "clock_in" | "clock_out"
}
```

**Response 200:**
Similar a clock-in o clock-out según corresponda.

---

#### GET /time-entries/history
Historial de fichajes.

**Headers:** `Authorization: Bearer {token}`

**Query Params:**
- `from`: Fecha inicio (YYYY-MM-DD) - Default: primer día del mes
- `to`: Fecha fin (YYYY-MM-DD) - Default: hoy
- `page`: Página - Default: 1
- `per_page`: Registros por página - Default: 20 (Max: 100)

**Ejemplo:** `/time-entries/history?from=2024-01-01&to=2024-01-31&page=1`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "entries": [
      {
        "id": 125,
        "type": "clock_out",
        "recorded_at": "2024-01-15 16:05:00",
        "date": "2024-01-15",
        "time": "16:05:00",
        "method": "geolocation",
        "work_center": "Oficina Central",
        "validation_status": "on_time",
        "validation_message": null,
        "late_minutes": null,
        "early_minutes": null,
        "overtime_minutes": 5
      },
      ...
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 45,
      "total_pages": 3,
      "from": "2024-01-01",
      "to": "2024-01-31"
    }
  }
}
```

---

#### GET /time-entries/summary
Resumen de horas trabajadas.

**Headers:** `Authorization: Bearer {token}`

**Query Params:**
- `period`: "week" | "month" | "custom" - Default: "week"
- `from`: Fecha inicio (solo para "custom")
- `to`: Fecha fin (solo para "custom")

**Ejemplo:** `/time-entries/summary?period=month`

**Response 200:**
```json
{
  "success": true,
  "data": {
    "period": {
      "type": "month",
      "from": "2024-01-01",
      "to": "2024-01-31"
    },
    "summary": {
      "total_minutes": 9600,
      "total_formatted": "160:00",
      "days_worked": 20,
      "average_minutes_per_day": 480,
      "average_formatted": "08:00",
      "daily": [
        {
          "date": "2024-01-15",
          "minutes": 483,
          "formatted": "08:03",
          "entries": 2
        },
        ...
      ]
    }
  }
}
```

---

## Códigos de Respuesta

| Código | Significado |
|--------|-------------|
| 200 | Éxito |
| 400 | Error de validación o lógica |
| 401 | No autenticado |
| 403 | Sin permisos |
| 404 | No encontrado |
| 422 | Datos inválidos |
| 500 | Error del servidor |

---

## Estados de Validación de Turno

| Estado | Descripción |
|--------|-------------|
| `on_time` | A tiempo |
| `early` | Entrada antes de hora |
| `late` | Entrada tarde |
| `overtime` | Horas extra |
| `unassigned` | Sin turno asignado |
| `no_shift_today` | No trabaja hoy según turno |

---

## Ejemplo Flutter - Servicio de API

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String baseUrl = 'https://tu-servidor.com/public/index.php?route=/api/v1';
  String? _token;

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
        'device_name': 'Flutter App',
        'platform': Platform.isAndroid ? 'android' : 'ios',
      }),
    );
    
    final data = jsonDecode(response.body);
    if (data['success']) {
      _token = data['data']['token'];
    }
    return data;
  }

  Future<Map<String, dynamic>> getClockStatus() async {
    final response = await http.get(
      Uri.parse('$baseUrl/time-entries/status'),
      headers: _authHeaders(),
    );
    return jsonDecode(response.body);
  }

  Future<Map<String, dynamic>> clockIn({
    required String method,
    String? qrToken,
    double? latitude,
    double? longitude,
  }) async {
    final response = await http.post(
      Uri.parse('$baseUrl/time-entries/clock-in'),
      headers: _authHeaders(),
      body: jsonEncode({
        'method': method,
        if (qrToken != null) 'qr_token': qrToken,
        if (latitude != null) 'latitude': latitude,
        if (longitude != null) 'longitude': longitude,
      }),
    );
    return jsonDecode(response.body);
  }

  Future<Map<String, dynamic>> clockOut({double? latitude, double? longitude}) async {
    final response = await http.post(
      Uri.parse('$baseUrl/time-entries/clock-out'),
      headers: _authHeaders(),
      body: jsonEncode({
        'method': latitude != null ? 'geolocation' : 'manual',
        if (latitude != null) 'latitude': latitude,
        if (longitude != null) 'longitude': longitude,
      }),
    );
    return jsonDecode(response.body);
  }

  Future<Map<String, dynamic>> clockWithQr(String qrToken) async {
    final response = await http.post(
      Uri.parse('$baseUrl/time-entries/qr'),
      headers: _authHeaders(),
      body: jsonEncode({
        'qr_token': qrToken,
        'action': 'auto',
      }),
    );
    return jsonDecode(response.body);
  }

  Map<String, String> _authHeaders() {
    return {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $_token',
    };
  }
}
```

---

## Notas Importantes

1. **Tokens expiran en 30 días**. Usar `/auth/refresh` antes de que expire.

2. **QR Token**: Cada centro de trabajo tiene un código QR único. El token se extrae del código QR escaneado.

3. **Geolocalización**: El servidor valida que el usuario esté dentro del radio permitido del centro de trabajo (geofence_radius en metros).

4. **Zona Horaria**: El servidor usa la zona horaria configurada (America/Argentina/Buenos_Aires). Las fechas se devuelven en formato ISO.

5. **Validación de Turno**: Si el empleado tiene un turno asignado, el sistema valida automáticamente si llegó a tiempo, tarde, o con horas extra.
