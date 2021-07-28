import requests
import syslog
import sys
import time
import os
from time import mktime
import datetime
import requests
import RPi.GPIO as GPIO
from os import path
import serial
GPIO.setmode(GPIO.BCM)

print('init serial')
ser = serial.Serial('/dev/ttyACM0', 256000)

print('init w1')

g16 = 'nincs-bekotve'
g17 = '28-030997796012'
g16Path = '/sys/bus/w1/devices/' + g16 + '/w1_slave'
g17Path = '/sys/bus/w1/devices/' + g17 + '/w1_slave'
outDevPath = '/home/pi/nyof/dev/dev';
now = ''
g16PathExists = path.exists(g16Path);
g17PathExists = path.exists(g17Path);
smsPass = "T2z76BqSb5fukPpRtzHt7Up1h6aYgpq6uQNvPAGiR0PRc51UJHCG5W3mjJpF3SNZ"
smsSent1 = False
smsSent2 = False
smsSent3 = False
smsSent4 = False
smsPar1 = "0"
smsPar2 = "0"
smsPar3 = "0"
smsPar4 = "0"
resetPath = "/home/pi/nyof/reset/reset"
g16Value = 0.0
g17Value = 0.0
a4Value = '0'
a5Value = '0'
riaszt = '0'

# todo: majd a kiolvasott értéket kapja meg ne ezt a konstans értéket
inverter = '1';
inverterPre = inverter;

if (not g16PathExists):
    print('Hiba: G16 nem letezik!')
if (not g17PathExists):
    print('Hiba: G17 nem letezik!')


def read_temp_raw(file):
    f = open(file, 'r')
    lines = f.readlines()
    f.close()
    return lines

def read_temp(file):
    lines = ''

    try:
        lines = read_temp_raw(file)
    except:
        print('Hiba: Nem olvashato a file: ' + file)
        return 0
    
    while lines[0].strip()[-3:] != 'YES':
        time.sleep(0.2)
        lines = read_temp_raw()
    equals_pos = lines[1].find('t=')
    if equals_pos != -1:
        temp_string = lines[1][equals_pos+2:]
        temp_c = float(temp_string) / 1000.0
        return temp_c

def commandRead(cmd):
    parancs = cmd + "\0";
    ser.write(parancs.encode('utf-8'))
    return ser.readline().decode('utf-8').replace("\r", "").replace("\n", "")

def saveOutDev(values):
    file2write=open(outDevPath,'w')
    file2write.write(values)
    file2write.close()    


print("A program elindult")

GPIO.setup(20, GPIO.IN)

#a5Value = commandRead("aR:5")
#a5Value = float(a5Value) * 0.0095238095238
#print("A5: " + str(a5Value) )
#sys.exit()

while True:
    if (g16PathExists):
        try:
            g16Value = read_temp(g16Path)
        except:
            pass
            # print('Hiba: Nem olvashato a file: ' + g16Path)
    if (g17PathExists):
        try:
            g17Value = read_temp(g17Path)
        except:
            pass
            # print('Hiba: Nem olvashato a file: ' + g17Path)

    inverter = str(GPIO.input(20))

    a5Value = round( float( commandRead("aR:5") ) * 0.0095238095238, 1)

    now = datetime.datetime.now()

    if a5Value < 0.0:
        a5Value = 0.0

    if a5Value < 10.0:
        riaszt = '0'
        if (a5Value > 8.0) and (not smsSent1):
            requests.post("http://192.168.2.172/nyof_alarm.php", data={'pw': smsPass, 'msg': '1'})
            smsSent1 = True
            smsPar1 = "1"
        elif (a5Value <= 0.05) and (not smsSent4):
            requests.post("http://192.168.2.172/nyof_alarm.php", data={'pw': smsPass, 'msg': '4'})
            smsSent4 = True
            smsPar4 = "1"
    else:
        riaszt = '1'
        if not smsSent2:
            requests.post("http://192.168.2.172/nyof_alarm.php", data={'pw': smsPass, 'msg': '2'})
            smsSent2 = True
            smsPar2 = "1"
    
    if (inverter == '0') and (inverterPre == '1') and (a5Value < 1.0) and (not smsSent3):
        requests.post("http://192.168.2.172/nyof_alarm.php", data={'pw': smsPass, 'msg': '3'})
        smsSent3 = True
        smsPar3 = "1"
    
    if path.exists(resetPath):
        os.remove(resetPath)
        os.system("reboot now")
        os.exit(1)

    #print("G16 erteke: " + str(g16Value))
    #print("G17 erteke: " + str(g17Value))
    #print("A4 erteke: " + a4Value)
    #print("A5 erteke: " + a5Value)
    print("")

    # todo: az a4 és G16 portokat jelenleg nem olvassuk, ezért 0 van fixen beírva. olvasásuk esetén át kell írni a változóra a fix értéket (az 0. és 2. paramétert)
    saveOutDev("0;" + str(g17Value) + ";0;" + str(a5Value) + ";" + now.strftime('%m.%d %H:%M:%S') + ";" + riaszt + ";" + inverter + ";" + smsPar1 + ";" + smsPar2 + ";" + smsPar3 + ";" + smsPar4)

    time.sleep(10)
