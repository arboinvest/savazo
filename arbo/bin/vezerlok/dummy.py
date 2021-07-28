#!/usr/bin/python

import sys
import serial
import RPi.GPIO as GPIO

ser = serial.Serial('/dev/ttyACM0', 256000)
GPIO.setmode(GPIO.BCM)
reload(sys)
sys.setdefaultencoding('utf8')

def analogRead(port):
    ser.write("analogMeres:" + str(port) + "\0")
    return ser.readline()

def parsUse():
    print("Usage:")
    print("  dummy.py a|d r|w <port> [1|0]")
    print("     a - analog")
    print("     d - digital")
    print("     r - read")
    print("     w - write")
    print("     <port> - GPIO port number")
    print("     1|0 - value to write")
    print("")
    print("     Important: The analog reading cannot be simultaneous. If a process is reading analog port stop it before measuring!")
    return 1;

if (len(sys.argv) < 4) :
    parsUse();
    sys.exit()

if (sys.argv[1] == 'a') :
    if (sys.argv[2] == 'r') :
        print(analogRead(sys.argv[3]))
        sys.exit()
    if (sys.argv[2] == 'w') :
        print("only read supports in analog mode!")
        sys.exit()
elif (sys.argv[1] == 'd') :
    if (sys.argv[2] == 'r') :
        GPIO.setup( int(sys.argv[3]) , GPIO.IN)
        print(GPIO.input( int(sys.argv[3]) ))
        sys.exit()
    if (sys.argv[2] == 'w' and len(sys.argv) == 5) :
        GPIO.setup( int(sys.argv[3]) , GPIO.OUT)
        GPIO.output( int(sys.argv[3]) , int(sys.argv[4]))
        sys.exit()

print("Bad parameters!")
parsUse();
sys.exit()




