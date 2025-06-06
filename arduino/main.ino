#include <WiFi.h>
#include <SPI.h>
#include <MFRC522.h>
#include <WebServer.h>
#include <HTTPClient.h>

#define RST_PIN 22
#define SS_PIN 5
MFRC522 rfid(SS_PIN, RST_PIN);

// Configuración WiFi
const char* ssid = "UbeeD18F-2.4G";
const char* password = "MaFer0808";

// Dirección del servidor
const char* serverUrl = "http://TU_IP/api/log_access.php";  // ⚠️ Reemplaza TU_IP

String lastUUID = "";
WebServer server(80);

void setup() {
  Serial.begin(115200);
  SPI.begin();
  rfid.PCD_Init();

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Conectando a WiFi...");
  }
  Serial.println("Conectado a WiFi. IP: " + WiFi.localIP().toString());

  // Endpoint para pruebas opcionales
  server.on("/uuid", HTTP_GET, []() {
    server.sendHeader("Access-Control-Allow-Origin", "*");
    server.sendHeader("Access-Control-Allow-Methods", "GET");
    server.send(200, "text/plain", lastUUID);
  });

  server.begin();
  Serial.println("Servidor web iniciado");
}

void loop() {
  server.handleClient();

  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) return;

  String uuid = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    uuid += String(rfid.uid.uidByte[i], HEX);
  }

  uuid.toUpperCase(); // Convierte el UUID a mayúsculas para consistencia

  // Solo registrar si es distinto al anterior
  if (uuid != lastUUID) {
    lastUUID = uuid;
    Serial.println("UUID detectado: " + uuid);

    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(serverUrl);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      String postData = "uuid=" + uuid;

      int httpResponseCode = http.POST(postData);
      String response = http.getString();

      Serial.println("Código HTTP: " + String(httpResponseCode));
      Serial.println("Respuesta: " + response);

      http.end();
    } else {
      Serial.println("WiFi desconectado");
    }
  }

  delay(2000); // Evita lecturas duplicadas rápidas
}
