#include <WiFi.h>
#include <SPI.h>
#include <MFRC522.h>
#include <WebServer.h>

#define RST_PIN 22   // Cambia si usas otros pines
#define SS_PIN 5
MFRC522 rfid(SS_PIN, RST_PIN);

const char* ssid = "UbeeD18F-2.4G";
const char* password = "MaFer0808";

String lastUUID = "";
bool isUpdated = false; // Bandera que indica si el UUID fue actualizado
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

  // Habilitar CORS
  server.on("/uuid", HTTP_GET, []() {
    if (isUpdated) {
      server.sendHeader("Access-Control-Allow-Origin", "*");
      server.sendHeader("Access-Control-Allow-Methods", "GET");
      server.send(200, "text/plain", lastUUID);
      isUpdated = false; // Después de enviar el UUID, reseteamos la bandera
    } else {
      server.send(200, "text/plain", ""); // Si no hay actualización, enviamos vacío
    }
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

  // Solo actualizamos si el UUID es diferente al último guardado
  if (uuid != lastUUID) {
    lastUUID = uuid;
    isUpdated = true;  // Se indica que el UUID fue actualizado
    Serial.println("Nuevo UUID detectado: " + uuid);
  }

  delay(2000);  // Evita lecturas duplicadas rápidas
}
