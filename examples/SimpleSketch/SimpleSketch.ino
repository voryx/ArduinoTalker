void setup() {
  Serial.begin(9600);
}

void loop() {
  if (Serial.available()) {
    char in = Serial.read();
    if (in == 'X') Serial.write("Y");
  }
}
