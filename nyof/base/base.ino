String parancs;
char vezerlo[2][10] = {0};
short i = 0;
short j = 0;
short k = 0;

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
    while (parancs[i] != '\0' && i < 10) {
      if (parancs[i] == ':') {
        j++;
    		if (j > 1) {
    			j = 1;
    		}
        k = 0;
      } else {
        vezerlo[j][k] = parancs[i];
        k++;
      }
      i++;
    }
    if (strcmp(vezerlo[0],"aR") == 0) {
      Serial.println(analogRead(atoi(vezerlo[1])));
    }

  }
}
