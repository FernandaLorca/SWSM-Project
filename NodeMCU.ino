#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>
#include <SoftwareSerial.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <bits/stdc++.h>

/*******************DEFINICIÓN DE VARIABLES*********************/
const char* ssid     = "Fernanda";
const char* password = "jejejaja";
const char* host = "192.168.136.193";
const uint16_t port = 80;

int led_builtin = 2;
bool conectado = false;

boolean conectarWiFi(const char* ssid, const char* password, const char* host, const uint16_t port, int led_builtin){
  bool conectado=false;
  Serial.begin(9600);
  pinMode(led_builtin, OUTPUT);
  digitalWrite(led_builtin, LOW);
  
  ESP8266WiFiMulti MyWifi_module;
  WiFi.mode(WIFI_STA);
  MyWifi_module.addAP(ssid, password);
  Serial.println("Esperando Red WiFi... ");
  Serial.println(WiFi.localIP());

  while (MyWifi_module.run() != WL_CONNECTED){   
    delay(500);
  }
  conectado = true;
  delay(500);
  return conectado;
}
float * getMediciones(String mediciones){
  static float r[6];
  char envio[mediciones.length()];
  strcpy(envio, mediciones.c_str());
  String medicionesDos[7];
  char delimitador[] = "|";
  char *token = strtok(envio, delimitador);
  int j = 0;
  if(token != NULL){
    while(token != NULL){
      if(j>0){
        medicionesDos[j]=token;
      }
      token = strtok(NULL, delimitador);
      j++;
    }
  }
  for (int i = 1; i < 7; i++){
    r[i-1] = strtod(medicionesDos[i].c_str(), NULL);
  }
  return r;
}
void setup() {
//SE CONECTA AL WIFI
  conectado = conectarWiFi(ssid, password, host, port, led_builtin);
  Serial.begin(9600);
}
void loop() {
//SE ENCIENDE EL LED BUILTIN SI ES QUE SE ESTÁ CONECTADO A WIFI
  if(conectado){
    digitalWrite(led_builtin, HIGH);
    delay(250);
    digitalWrite(led_builtin, LOW);
    delay(250);
  }
//SE ESTABLECE LA CONEXIÓN CON LOCALHOST EN EL PUERTO 80
  WiFiClient client;
  if (!client.connect(host, port)) {
    Serial.println("Conexion fallida");
    Serial.println("Reintentando en 5 segundos...");
    delay(5000);
    return;
  }
//SE SOLICITAN LOS PARÁMETROS AL SERVIDOR
  client.println("GET /enviar.php?msg=1 HTTP/1.0");
  client.println("Host: 192.168.136.193"); //ip localhost
  client.println("Connection: close");
  client.println();
//SE VERIFICA QUE LA CONEXIÓN AL SERVIDOR ESTÉ HABILITADA
  int timeout_flag = 0;
  while (client.available() == 0){
    static int count = 0;
    //Serial.println("Esperando a recibir una respuesta del servidor");
    delay(250);
    if (count > 12)
      timeout_flag = 1;
      break;
  }
//SE EXTRAEN LOS PARÁMETROS DESDE RESPUESTA DEL SERVIDOR Y SE IMPRIMEN EN EL SERIAL
  String lineas[10];
  int i=0;
  if (timeout_flag == 0) {
    //lee todas las líneas enviadas por el servidor
    do{
      String line = client.readStringUntil('\r'); //lee línea por línea
      lineas[i]=line; //ingresa line al arreglo lineas
      i++;
    }while(client.available() > 0);
    size_t n = sizeof(lineas)/sizeof(lineas[0]); //largo del arreglo
    //Serial.print("Parámetros recibidos desde el servidor local: ");
    Serial.println(lineas[7]);
  }
//SI NO SE ESTÁ RECIBIENDO DATOS DESDE ARDUINO, SE ESPERA 5 SEG Y SE REINICIA EL LOOP
  if(Serial.available()<1){
    //Serial.print("La dirección IP local del módulo es: ");
    //Serial.println(WiFi.localIP());
    //Serial.println("No hay datos que recibir por comunicación serial.");
    client.stop();
    delay(5000);
    return;
  }
//SE ENVÍAN LOS DATOS A LOS SERVIDORES CUANDO ESTOS SON RECIBIDOS
  if(Serial.available()>0){
    bool isValid = false;
    String msg = Serial.readStringUntil('\r');
    float *Resultado;
    Resultado = getMediciones(msg);
    //Serial.print("Mediciones recibidas: ");
    for (int i = 0; i < 6; i++){
      if(Resultado[i] != 0.0){
        isValid = true;
      }
    }
    if(isValid == false){
      return;
    }
    for (int i = 0; i < 6; i++){
      if(i != 5){
        //Serial.print(Resultado[i]);
        //Serial.print(", ");
      }else{
        //Serial.println(Resultado[i]);  
      }
    }
    
    HTTPClient clienteRemoto;
    clienteRemoto.begin(client, "http://44.203.251.57/arduino1/postdata.php");
    clienteRemoto.addHeader("Content-Type", "application/x-www-form-urlencoded", false, true);
    
    HTTPClient clienteLocal;
    clienteLocal.begin(client, "http://192.168.136.193/postdata.php");
    clienteLocal.addHeader("Content-Type", "application/x-www-form-urlencoded", false, true);
    
    int clienteRemotoCode = clienteRemoto.POST("Noise_value="+String(Resultado[0])+"&Temp_value="+String(Resultado[1])+"&Monox_value="+String(Resultado[2])+"&Diox_value="+String(Resultado[3])+"&Amon_value="+String(Resultado[4])+"&Tolu_value="+String(Resultado[5]));
    if(clienteRemotoCode > 0){
      Serial.printf("[Remote][HTTP] POST... code: %d\n", clienteRemotoCode);
      if(clienteRemotoCode == HTTP_CODE_OK) {
        String payload = clienteRemoto.getString();
        Serial.println(payload);
      }
    }else{
      Serial.printf("[Remote][HTTP] POST... failed, error: %s\n", clienteRemoto.errorToString(clienteRemotoCode).c_str());
    }
    int clienteLocalCode = clienteLocal.POST("Noise_value="+String(Resultado[0])+"&Temp_value="+String(Resultado[1])+"&Monox_value="+String(Resultado[2])+"&Diox_value="+String(Resultado[3])+"&Amon_value="+String(Resultado[4])+"&Tolu_value="+String(Resultado[5]));
    if(clienteLocalCode > 0){
      Serial.printf("[Local][HTTP] POST... code: %d\n", clienteLocalCode);
      if(clienteLocalCode == HTTP_CODE_OK) {
        String payload = clienteLocal.getString();
        Serial.println(payload);
      }
    }else{
      Serial.printf("[Local][HTTP] POST... failed, error: %s\n", clienteLocal.errorToString(clienteLocalCode).c_str());
    } 
    clienteRemoto.end();
    clienteLocal.end();
  }
  client.stop();
  delay(2000);
}
