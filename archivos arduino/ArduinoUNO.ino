#include <SoftwareSerial.h>
#include <stdio.h>
#include <string.h>

/******************************************************DEFINICIÓN DE VARIABLES************************************************************/
//Sonido
const int sensitivity = -58;     // Sensibilidad del micrófono en dB
const int gain = 20;             // op-amp gain dB
const int MAX9812_PIN = 2;
const int sampleWindow = 1000;  //sample for 1000 milliseconds
float S_MAX;
//Temperatura
#define SAMPLES_FAST_AVERAGE   64
#define SAMPLES_TO_AVERAGE    100
int lectura;
float RawTemp;
float temperatura_deseada;
float tempC;
const int LM35_PIN = 1;
float T_MIN;
float T_MAX;
//Gas
const int calibrationLed = 13;                      //when the calibration start , LED pin 13 will light up , off when finish calibrating
const int MQ_PIN = A0;                                //define which analog input channel you are going to use
int RL_VALUE = 1;                                     //define the load resistance on the board, in kilo ohms
float RO_CLEAN_AIR_FACTOR =11.97;                     //RO_CLEAR_AIR_FACTOR=(Sensor resistance in clean air)/RO,
                                                    //which is derived from the chart in datasheet
int CALIBARAION_SAMPLE_TIMES=50;                    //define how many samples you are going to take in the calibration phase
int CALIBRATION_SAMPLE_INTERVAL=500;                //define the time interal(in milisecond) between each samples in the
                                                    //cablibration phase
int READ_SAMPLE_INTERVAL=50;                        //define how many samples you are going to take in normal operation
int READ_SAMPLE_TIMES=5;                            //define the time interal(in milisecond) between each samples in 
                                                    //normal operation
float           CO2Curve[3]  =  {2,0.04,-0.46};   
float           COCurve[3]  =  {2,0.20,-0.19};   
float           ALCurve[3] ={2,-0.036,-0.35};                                                        
float           NH4Curve[3] ={2,-0.046,-0.206}; 
float           TOCurve[3] ={2,-0.097,-0.299}; 
float           ACCurve[3] ={2,-0.125,-0.371}; 
float           Ro           =  10;                 //Ro está inicializado en 10kohm
float CO2_MAX;
float CO_MAX;
float NH4_MAX;
float TO_MAX;

#define         GAS_CO2             0   
#define         GAS_CO              1   
#define         GAS_AL              2   
#define         GAS_NH4             3   
#define         GAS_TO              4   
#define         GAS_AC              5 

const byte espRx = 6;
const byte espTx = 5;
SoftwareSerial SerialEsp(espTx, espRx);
//*******************************************************CALIBRACION MQ135***************************************************************
long  MQGetPercentage(float rs_ro_ratio, float *pcurve)
{
  return (pow(10,( ((log(rs_ro_ratio)-pcurve[1])/pcurve[2]) + pcurve[0])));
}

long MQGetGasPercentage(float rs_ro_ratio, int gas_id)
{
  if ( gas_id == GAS_CO2 ) {
     return MQGetPercentage(rs_ro_ratio,CO2Curve);
  } else if ( gas_id == GAS_CO ) {
     return MQGetPercentage(rs_ro_ratio,COCurve);
  } else if ( gas_id == GAS_AL ) {
     return MQGetPercentage(rs_ro_ratio,ALCurve);
  } else if ( gas_id == GAS_NH4 ) {
     return MQGetPercentage(rs_ro_ratio,NH4Curve);
  } else if ( gas_id == GAS_TO ) {
     return MQGetPercentage(rs_ro_ratio,TOCurve);
  } else if ( gas_id == GAS_AC ) {
     return MQGetPercentage(rs_ro_ratio,ACCurve);
  } 
 
  return 0;
}

float MQRead(int mq_pin)
{
  int i;
  float rs=0;
 
  for (i=0;i<READ_SAMPLE_TIMES;i++) {
    rs += MQResistanceCalculation(analogRead(mq_pin));
    delay(READ_SAMPLE_INTERVAL);
  }
 
  rs = rs/READ_SAMPLE_TIMES;
 
  return rs;  
}

float MQCalibration(int mq_pin)
{
  int i;
  float val=0;

  for (i=0;i<CALIBARAION_SAMPLE_TIMES;i++) {            //take multiple samples
    val += MQResistanceCalculation(analogRead(mq_pin));
    delay(CALIBRATION_SAMPLE_INTERVAL);
  }
  val = val/CALIBARAION_SAMPLE_TIMES;                   //calculate the average value
  val = val/RO_CLEAN_AIR_FACTOR;                        //divided by RO_CLEAN_AIR_FACTOR yields the Ro                                        
  return val;                                           //according to the chart in the datasheet 

}

float MQResistanceCalculation(int raw_adc)
{
  return ( ((float)RL_VALUE*(1023-raw_adc)/raw_adc));
}

//***********************************************************FUNCIONES DE MEDICIÓN***************************************************************
double medirSonido(const int sensitivity, const int gain, const int MAX9812_PIN, const int sampleWindow){
  unsigned int sample;
  unsigned long startMillis= millis();  // Start of sample window
  unsigned int peakToPeak = 0;         // peak-to-peak level
  unsigned int signalMax = 0;
  unsigned int signalMin = 1024;       // 10 bit ADC = 2^10

  // collect data for Sample window width in mS 
  while (millis() - startMillis < sampleWindow){
    sample = analogRead(MAX9812_PIN);

    if (sample < 1024){  // toss out spurious readings
      // see if you have a new maxValue
      if (sample > signalMax){
        signalMax = sample;  // save just the max levels
      }else if (sample < signalMin){
        signalMin = sample;  // save just the min levels
      }
    }
  }
  peakToPeak = signalMax - signalMin;               // max - min = peak-peak amplitude
  double volts = ((peakToPeak * 5.0) / 1024)*10;    // convert to volts,gain 20db  = 10V
                                                    // Uno/Mega analogRead() voltages between 0 and 5 volts into integer values between 0 and 1023
                                                    // change if have different board

  double volts_db = 20*log10(volts/0.001259);                             
  double spl_db = volts_db + 94 + sensitivity - gain;            
  return volts_db;
}

float medirTemperatura(){  
  int lectura;
          
  lectura = analogRead(A1);  //asignacion de lo que se mida en la variable sensor, a la variable lectura
  RawTemp = lectura / 9.31;        //ATENCION, al cambiar la ref analogica del arduino de 5v a 1.1v se tiene que reformular la ec. para obtener la temperatura de tal forma que
                               // se divide 1./1024 = 0.001074V = 1.0742 mV. Si 10mV es igual a 1 grado Celsius, 10 / 1.0742 = ~ 9,31.
                              // Así, por cada cambio de 9,31 en la lectura analógica, hay un grado de cambio de temperatura.

  tempC = tempC  + (RawTemp - tempC) / SAMPLES_TO_AVERAGE;   // aca ya trae 100 muestras previas promediadas.
  return (tempC-16);
}

float * medirGas(const int calibrationLed, const int MQ_PIN, int RL_VALUE, float RO_CLEAN_AIR_FACTOR, float Ro){  
  long iPPM_CO2 = 0;
  long iPPM_CO = 0;
  long iPPM_AL = 0;
  long iPPM_NH4 = 0;
  long iPPM_TO = 0;
  long iPPM_AC = 0;
  static float f[4];
  iPPM_CO2 = MQGetGasPercentage(MQRead(MQ_PIN)/Ro,GAS_CO2);
  iPPM_CO = MQGetGasPercentage(MQRead(MQ_PIN)/Ro,GAS_CO);
  iPPM_AL = MQGetGasPercentage(MQRead(MQ_PIN)/Ro,GAS_AL);
  iPPM_NH4 = MQGetGasPercentage(MQRead(MQ_PIN)/Ro,GAS_NH4);
  iPPM_TO = MQGetGasPercentage(MQRead(MQ_PIN)/Ro,GAS_TO);
  iPPM_AC = MQGetGasPercentage(MQRead(MQ_PIN)/Ro,GAS_AC);
  f[0] = float (iPPM_CO);
  f[1] = float (iPPM_CO2);
  f[2] = float (iPPM_NH4);
  f[3] = float (iPPM_TO);
  return f;
}
float * getParametros(String parametros){
  // La función entrega un puntero a arreglo. Para crear el arreglo se debe hacer del modo
  // string *Resultado;
  // Resultado = getMediciones(mediciones);
  // Luego, Resultado[0] -> Sonido, Resultado[1] -> Temperatura, Resultado[2] -> CO, Resultado[3] -> CO2, Resultado[4] -> NH4, Resultado[5] -> Tolueno
  static float r[7];
  char envio[parametros.length()];
  strcpy(envio, parametros.c_str());

  String parametrosDos[7];
  char delimitador[] = "|";
  char *token = strtok(envio, delimitador);
  int j = 0;
  if(token != NULL){
    while(token != NULL){
      if(j>0){
        parametrosDos[j]=token;
      }
      token = strtok(NULL, delimitador);
      j++;
    }
  }
  for (int i = 1; i < 8; i++){
    r[i-1] = strtod(parametrosDos[i].c_str(), NULL);
  }
  return r;
}
void setup() {
  Serial.begin(9600);

  //Temperatura
  //pinMode(LM35_PIN, INPUT);
  for (int i= 0; i<SAMPLES_TO_AVERAGE; i++) {
       lectura = analogRead(LM35_PIN);  //asignacion de lo que se mida en la variable sensor, a la variable lectura
    RawTemp = lectura / 9.31;        //ATENCION, al cambiar la ref analogica del arduino de 5v a 1.1v se tiene que reformular la ec. para obtener la temperatura de tal forma que
                               // se divide 1./1024 = 0.001074V = 1.0742 mV. Si 10mV es igual a 1 grado Celsius, 10 / 1.0742 = ~ 9,31.
                              // Así, por cada cambio de 9,31 en la lectura analógica, hay un grado de cambio de temperatura.

      tempC = tempC  + (RawTemp - tempC) / SAMPLES_TO_AVERAGE;   // para la primer iteraccion veran que RawTemp vale 0 y que 0 - 0 + tempC toma el primer valor 
    delay(50);
   }
  
  pinMode(7, OUTPUT);
  pinMode(8, OUTPUT);
  //Gas
  pinMode(calibrationLed,OUTPUT);
  digitalWrite(calibrationLed,HIGH);
  Serial.print("Calibrating...");                        
  
  Ro = MQCalibration(MQ_PIN);
  digitalWrite(calibrationLed,LOW);              
  
  Serial.println("done!");                                 
  Serial.print("Ro= ");
  Serial.print(Ro);
  Serial.println("kohm\n");

  const byte espRx = 6;
  const byte espTx = 5;
  SerialEsp.begin(9600);
}

void loop() {
  if (SerialEsp.available() > 0) {
    String mensajeRecibido = SerialEsp.readStringUntil('\r');
    float *Resultado;
    Resultado = getParametros(mensajeRecibido);
    bool isValid = false;
    T_MIN = Resultado[0];
    T_MAX = Resultado[1];
    S_MAX = Resultado[2];
    CO2_MAX = Resultado[3];
    CO_MAX = Resultado[4];
    TO_MAX = Resultado[5];
    NH4_MAX = Resultado[6];
    for (int i = 0; i < 7; i++){
      if(Resultado[i] != 0.0){
        isValid = true;
      }
    }
    if (isValid == false){
      return;
    }
    Serial.print("Parametros recibidos: ");
    for (int i = 0; i < 7; i++){
      if(i != 6){
        Serial.print(Resultado[i]);
        Serial.print(", ");
      }else{
        Serial.println(Resultado[i]);
      }
    }
  }
  double sonido = medirSonido(sensitivity, gain, MAX9812_PIN, sampleWindow);
  Serial.println(sonido);

  float temperatura = medirTemperatura();
  Serial.println(temperatura);
  
  float *p;
  p = medirGas(calibrationLed, MQ_PIN, RL_VALUE, RO_CLEAN_AIR_FACTOR, Ro);
  if(sonido >= S_MAX || temperatura <= T_MIN || temperatura >= T_MAX || p[0] >= CO_MAX || p[1] >= CO2_MAX || p[2] >= NH4_MAX || p[3] >= TO_MAX){
    digitalWrite(7, LOW); 
    digitalWrite(8, HIGH);
    delay(500);
  }else{
    digitalWrite(8, LOW);
    digitalWrite(7, HIGH);
    delay(500);
  }
  String mensajeParaEnviar = "0|" + (String)sonido + "|" + (String)temperatura + "|" + (String)p[0] + "|" + (String)p[1] + "|" + (String)p[2] + "|" + (String)p[3];
  Serial.println(mensajeParaEnviar);
  SerialEsp.println(mensajeParaEnviar);
  
  delay(2000);
}
