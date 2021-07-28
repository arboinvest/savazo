String parancs;
char vezerlo[3][100] = {0};
int i = 0;
int j = 0;
int k = 0;

void setup() {
  Serial.begin(256000);
}

void loop() {
  while (1) {
    vezerlo[0][0] = '\0';
    vezerlo[1][0] = '\0';
    j = 0;
    k = 0;
    i = 0;
    parancs = Serial.readString();
    while (parancs[i] != '\0') {
      if (parancs[i] == ':') {
        j++;
        k=0;
      } else {
        vezerlo[j][k] = parancs[i];
        k++;
      }
      i++;
    }
    if (strcmp(vezerlo[0],"analogMeres") == 0) {
      Serial.println(analogMeres(atoi(vezerlo[1])));
    } else if (strcmp(vezerlo[0],"digitalisMeres") == 0) {
      Serial.println(digitalisMeres(atoi(vezerlo[1])));
    } else if (strcmp(vezerlo[0],"analogIras") == 0) {
      Serial.println(analogIras(atoi(vezerlo[1]),atoi(vezerlo[2])));
    } else if (strcmp(vezerlo[0],"digitalisIras") == 0) {
      Serial.println(digitalisIras(atoi(vezerlo[1]),atoi(vezerlo[2])));
    } else {
//      Serial.println(parancs);
    }
  }
}

int analogMeres(int portszam) {
  int ertek = analogRead(portszam);
  return ertek;
}

int analogIras(int portszam, int ertek) {
  analogWrite(portszam, ertek);
  return 1;
}

int digitalisMeres(int portszam) {
  pinMode(portszam, INPUT);
  int ertek = digitalRead(portszam);
  return ertek;
}

int digitalisIras(int portszam, int ertek) {
  pinMode(portszam, OUTPUT);
  digitalWrite(portszam, ertek);
  return 1;
}
