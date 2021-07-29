import requests
import serial
import syslog
import sys
import time
from time import mktime
import datetime
import requests
import RPi.GPIO as GPIO
GPIO.setmode(GPIO.BCM)
reload(sys)
sys.setdefaultencoding('utf8')

# loopback address
serverURL = '127.0.0.1'
apiService = "http://" + serverURL + "/index.php"

a3Value = '0.0'
cnt = 0

def analogMeres(port):
    # return '0'
    # ha működik, ezt visszarakni!!!!
    ser.write("analogMeres:" + str(port) + "\0")
    return ser.readline()


def init():
    GPIO.setup(22, GPIO.OUT)
    GPIO.setup(12, GPIO.IN)
    GPIO.setup(5, GPIO.IN)
    GPIO.setup(17, GPIO.IN)
    GPIO.setup(16, GPIO.IN)
    GPIO.setup(6, GPIO.IN)
    #GPIO.setup(19, GPIO.OUT)
    #GPIO.setup(20, GPIO.OUT)
    #g19 = GPIO.input(19)
    #if g19 != 1:
    #    print "Indító jel küldése P2-nek"
    #    GPIO.output(19, 1);
    #g20 = GPIO.input(20)
    #if g20 != 1:
    #    print "Indító jel küldése P1-nek"
    #    GPIO.output(20, 1);


def bejelentkezes():
    keres = requests.post(apiService, data={'action': 0})
    if keres.status_code == 200:
        if keres.text == '-11109':
            print "Adatbázis kapcsolat hiba!"
        elif keres.text == '-11111':
            print "Nem azonosított eszköz, ellenőrizze a beállításokat!"
        elif keres.text == '-11110':
            print "Ismeretlen parancs!"
        elif keres.text != '1':
            print "Üzemmód hiba!"
        else:
            return keres.text
    else:
        print "Kapcsolódási hiba!"
    return -1

def allapotTarolas(ertekek):
    keres = requests.post(apiService, data={'action': 1, 'ertekek':ertekek})
    if keres.status_code == 200:
        if keres.text == '-11109':
            print "Adatbázis kapcsolat hiba!"
        elif keres.text == '-11111':
            print "Nem azonosított eszköz, ellenőrizze a beállításokat!"
        elif keres.text == '-11110':
            print "Ismeretlen parancs!"
        elif keres.text != '1':
            print "Általános hiba!"
        else:
            return keres.text
    else:
        print "Kapcsolódási hiba!"
    return -1

def utasitasVisszajelzes(allapot):
    keres = requests.post(apiService, data={'action': 3, 'allapot':allapot})
    if keres.status_code == 200:
        if keres.text == '-11109':
            print "Adatbázis kapcsolat hiba!"
        elif keres.text == '-11111':
            print "Nem azonosított eszköz, ellenőrizze a beállításokat!"
        elif keres.text == '-11110':
            print "Ismeretlen parancs!"
        else:
            return keres.text
    else:
        print "Kapcsolódási hiba!"
    return -1

def uzemAllapotBeallitas():
    global boot
    global cnt   
    g22 = GPIO.input(22)
    if g22 != 1:
        GPIO.output(22, 1);
    if boot == 1:
        boot = 0
    cnt = 0
    utasitasVisszajelzes(1)
    return 1

def savazoAllapotBeallitas():
    global cnt
    g22 = GPIO.input(22)
    if g22 != 0:
        GPIO.output(22, 0);
    cnt = 0
    utasitasVisszajelzes(4)
    return 1

def meres():
    global cnt
    cnt = 0
    time.sleep(10)
    uzenet = allapotUzenetLetrehozasa()
    allapotTarolas(uzenet)
    utasitasVisszajelzes(3)

def allapotUzenetLetrehozasa():
    global cnt
    global a1Value
    global a2Value
    global a3Value
    #if cnt == 0:
    #    cnt = 1

    aramd = int(analogMeres(3)) - 138
    if aramd >= 0:
        a3Value = str(aramd * 0.085897436)
    else:
        a3Value = 0

    #else:
    #    cnt = cnt + 1
    #    if (cnt >= 2):
    #        cnt = 0

    a1Value = str( (float(analogMeres(1)) - 246.0) * 0.063366 )
    a2Value = str( (float(analogMeres(2)) - 246.0) * 0.063366 )

    return ( "G22:" + str(GPIO.input(22))
     + ",G20:1,G19:1"
     + ",G17:" + str(GPIO.input(17))
     + ",G16:" + str(GPIO.input(16))
     + ",G5:" + str(GPIO.input(5))
     + ",G12:" + str(GPIO.input(12))
     + ",G6:" + str(GPIO.input(6))
     + ",A4:" + str(analogMeres(4))
     + ",A1:" + a1Value
     + ",A2:" + a2Value
     + ",A3:" + a3Value
     + ",A5:" + str(analogMeres(5)) )

def utasitasLekerdezes():
    keres = requests.post(apiService, data={'action': 2})
    if keres.status_code == 200:
        if keres.text == '-11109':
            print "Adatbázis kapcsolat hiba!"
        elif keres.text == '-11111':
            print "Nem azonosított eszköz, ellenőrizze a beállításokat!"
        elif keres.text == '-11110':
            print "Ismeretlen parancs!"
        else:
            return keres.text
    else:
        print "Kapcsolódási hiba!"
    return -1

def p2Reset():
    time.sleep(15)
    utasitasVisszajelzes(5)

def p1Reset():
    time.sleep(15)
    utasitasVisszajelzes(6)

print "A program elindult"
ser = serial.Serial('/dev/ttyACM0', 256000)
#ser = serial.Serial('/dev/ttyACM1', 256000)
init()
boot = 1
master = '-1'

for arg in sys.argv:
    if arg == '-w':
        boot = 0;
        master = '1'

while True:
    if boot == 1:
        requests.post(apiService, data={'action': 19})
        uzemAllapotBeallitas()
    if master == '-1':
        master = bejelentkezes()
    else:
        try:
            uzenet = allapotUzenetLetrehozasa()
            allapotTarolas(uzenet)
        except Exception as ex:
            requests.post(apiService, data={'action': 24, 'error': ex})
            uzenet = ''
        #print uzenet;
        utasitas = utasitasLekerdezes()
        print "Utasítás:"+str(utasitas)
        if utasitas == '1':
            uzemAllapotBeallitas()
        elif utasitas == '3':
            meres()
        elif utasitas == '4':
            savazoAllapotBeallitas()
        elif utasitas == '5':
            p1Reset()
        elif utasitas == '6':
            p2Reset()
    print ""
    time.sleep(10)
