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

print 'init GPIO';

GPIO.setmode(GPIO.BCM)

print 'init sys';
reload(sys)
sys.setdefaultencoding('utf8')

print 'init w1';
#os.system('modprobe w1-gpio')
#os.system('modprobe w1-therm')

g27 = '28-03099779fe76'
g25 = '28-0317248346ff'
g27Path = '/sys/bus/w1/devices/' + g27 + '/w1_slave'
g25Path = '/sys/bus/w1/devices/' + g25 + '/w1_slave'
g25Exists = True
g27Exists = True

# server url ip address
serverURL = '***.***.***.***'
apiService = "http://" + serverURL + "/index.php"
apiTimeout = 5

if (not path.exists(g27Path)):
    g27Exists = False

if (not path.exists(g25Path)):
    g25Exists = False

g27Value = '0.0'
g25Value = '0.0'
cnt = 0

# konstansok
V2_ZARVA = 22;
V2_NYITVA = 21;
V4_ZARVA = 20;
V4_NYITVA = 19;
V2_V4_VEZERLES = 18;
V1_V3_VEZERLES = 17;
V1_ZARVA = 16;
V1_NYITVA = 13;
V3_ZARVA = 12;
V3_NYITVA = 6;
V1_V3_VEZERLES_NYITAS = 0;
V1_V3_VEZERLES_ZARAS = 1;
V2_V4_VEZERLES_NYITAS = 1;
V2_V4_VEZERLES_ZARAS = 0;

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
        return 0.0

    if (lines[0].strip()[-3:] != 'YES'):
        return 0.0

    equals_pos = lines[1].find('t=')
    if equals_pos != -1:
        temp_string = lines[1][equals_pos+2:]
        temp_c = float(temp_string) / 1000.0
        return temp_c

def pillangoSzelepAllapot():
    if GPIO.input(V2_ZARVA) == 0 and GPIO.input(V4_ZARVA) == 0:
        return 0
    elif GPIO.input(V2_NYITVA) == 0 and GPIO.input(V4_NYITVA) == 0:
        return 1
    else:
        return 2

def pillangoSzelepZaras():
    if GPIO.input(V2_ZARVA) != 0 or GPIO.input(V4_ZARVA) != 0:
        GPIO.output(V2_V4_VEZERLES, 0);
	time.sleep(1)
    t = 0
    while GPIO.input(V2_ZARVA) != 0 or GPIO.input(V4_ZARVA) != 0:
        time.sleep(1)
        if (t % 8) == 0:
            allapotTarolas(allapotUzenetLetrehozasa())
        t = t + 1
    return 1

def pillangoSzelepNyitas():
    if (GPIO.input(V1_ZARVA) != 0 or GPIO.input(V3_ZARVA) != 0):
        keres = requests.post(apiService, data={'action': 5})
        return 0
    if GPIO.input(V2_NYITVA) != 0 or GPIO.input(V4_NYITVA) != 0:
        GPIO.output(V2_V4_VEZERLES, 1);
	time.sleep(1)
    t = 0
    while GPIO.input(V2_NYITVA) != 0 or GPIO.input(V4_NYITVA) != 0:
        time.sleep(1)
        if (t % 8) == 0:
            allapotTarolas(allapotUzenetLetrehozasa())
        t = t + 1
    return 1

def golyosSzelepAllapot():
    if GPIO.input(V1_ZARVA) == 0 and GPIO.input(V3_ZARVA) == 0:
        return 0
    elif GPIO.input(V1_NYITVA) == 0 and GPIO.input(V3_NYITVA) == 0:
        return 1
    else:
        return 2

def golyosSzelepZaras():
    if GPIO.input(V1_ZARVA) != 0 or GPIO.input(V3_ZARVA) != 0:
        GPIO.output(V1_V3_VEZERLES, 1);
	time.sleep(1)
    t = 0
    while GPIO.input(V1_ZARVA) != 0 or GPIO.input(V3_ZARVA) != 0:
        time.sleep(1)
        if (t % 8) == 0:
            allapotTarolas(allapotUzenetLetrehozasa())
        t = t + 1
    return 1

def golyosSzelepNyitas():
    if (GPIO.input(V2_ZARVA) != 0 or GPIO.input(V4_ZARVA) != 0):
        keres = requests.post(apiService, data={'action': 4})
        return 0
    if GPIO.input(V1_NYITVA) != 0 or GPIO.input(V3_NYITVA) != 0:
        GPIO.output(V1_V3_VEZERLES, 0);
	time.sleep(1)
    t = 0
    while GPIO.input(V1_NYITVA) != 0 or GPIO.input(V3_NYITVA) != 0:
        time.sleep(1)
        if (t % 8) == 0:
            allapotTarolas(allapotUzenetLetrehozasa())
        t = t + 1
    return 1

def init():
    GPIO.setup(27, GPIO.IN)
    GPIO.setup(25, GPIO.IN)
    GPIO.setup(V2_ZARVA, GPIO.IN)
    GPIO.setup(V2_NYITVA, GPIO.IN)
    GPIO.setup(20, GPIO.IN)
    GPIO.setup(19, GPIO.IN)
    GPIO.setup(18, GPIO.OUT)
    GPIO.setup(17, GPIO.OUT)
    GPIO.setup(16, GPIO.IN)
    GPIO.setup(13, GPIO.IN)
    GPIO.setup(12, GPIO.IN)
    GPIO.setup(6, GPIO.IN)


def bejelentkezes():
    keres = requests.post(apiService, data={'action': 0})
    if keres.status_code == 200:
        if keres.text == '-11109':
            print "Adatbázis kapcsolat hiba!"
        elif keres.text == '-11111':
            print "Nem azonosított eszköz, ellenörizze a beállításokat!"
        elif keres.text == '-11110':
            print "Ismeretlen parancs!"
        elif keres.text != '0':
            print "Üzemmód hiba!"
        else:
            return keres.text
    else:
        print "Kapcsolódási hiba!"
    return -1

def allapotTarolas(ertekek):

    try:
        keres = requests.post(apiService, data={'action': 1, 'ertekek':ertekek}, timeout=apiTimeout)
        if keres.status_code == 200:
            if keres.text == '-11109':
                print "Adatbázis kapcsolat hiba!"
            elif keres.text == '-11111':
                print "Nem azonosított eszköz, ellenörizze a beállításokat!"
            elif keres.text == '-11110':
                print "Ismeretlen parancs!"
            elif keres.text != '1':
                print "Általános hiba!"
            else:
                return keres.text
        else:
            print "Kapcsolódási hiba!"

    except requests.Timeout:
        pass
    except requests.ConnectionError:
        pass    
    
    return -1

def utasitasLekerdezes():
    try:

        keres = requests.post(apiService, data={'action': 2}, timeout=apiTimeout)
        if keres.status_code == 200:
            if keres.text == '-11109':
                print "Adatbázis kapcsolat hiba!"
            elif keres.text == '-11111':
                print "Nem azonosított eszköz, ellenörizze a beállításokat!"
            elif keres.text == '-11110':
                print "Ismeretlen parancs!"
            else:
                return keres.text
        else:
            print "Kapcsolódási hiba!"
    
    except requests.Timeout:
        pass
    except requests.ConnectionError:
        pass    
    
    return -1

def utasitasVisszajelzes(allapot):
    keres = requests.post(apiService, data={'action': 3, 'allapot':allapot})
    if keres.status_code == 200:
        if keres.text == '-11109':
            print "Adatbázis kapcsolat hiba!"
        elif keres.text == '-11111':
            print "Nem azonosított eszköz, ellenörizze a beállításokat!"
        elif keres.text == '-11110':
            print "Ismeretlen parancs!"
        else:
            return keres.text
    else:
        print "Kapcsolódási hiba!"
    return -1

def uzemAllapotBeallitas():
    global boot
    # if boot == 1:
    # asyncSleep(5)

    #zar pillangot
    #ha pillango zart -> nyit 2 percre a golyost, aztan zar
    #ha a golyos zárva -> nyitas pillango
    # uzemallapot ha golyosok zarva és pillango nyitva

    keres = requests.post(apiService, data={'action': 20})

    print "pillangoSzelepZaras()"
    pillangoSzelepZaras()
    print "pillangoSzelepZaras()..kesz"
    allapotTarolas(allapotUzenetLetrehozasa())
    pillangoszelep = pillangoSzelepAllapot()
    if (pillangoszelep == 0):
        print "golyosSzelepNyitas()"
        golyosSzelepNyitas()
        print "golyosSzelepNyitas()..kesz"
        allapotTarolas(allapotUzenetLetrehozasa())
        print "asyncSleep(30)"
        asyncSleep(30)
        print "asyncSleep(30)..kesz"
        print "golyosSzelepZaras()"
        golyosSzelepZaras()
        print "golyosSzelepZaras()..kesz"
        allapotTarolas(allapotUzenetLetrehozasa())

        golyosszelep = golyosSzelepAllapot()
        #print "golyosSzelepAllapot = " + str(golyosszelep)
        if (golyosszelep == 0):
            print "pillangoSzelepNyitas()"
            pillangoSzelepNyitas()
            print "pillangoSzelepNyitas()..kesz"
            allapotTarolas(allapotUzenetLetrehozasa())

    pillangoszelep = pillangoSzelepAllapot()
    golyosszelep = golyosSzelepAllapot()

    if (golyosszelep == 0 and pillangoszelep == 1):
        utasitasVisszajelzes(1)
        requests.post(apiService, data={'action': 22})

    if boot == 1:
        boot = 0
    return 1

def zartAllapotBeallitas():
    pillangoszelep = pillangoSzelepAllapot()
    golyosszelep = golyosSzelepAllapot()
    if golyosszelep == 1 or golyosszelep == 2:
        golyosSzelepZaras()
    if pillangoszelep == 1 or pillangoszelep == 2:
        pillangoSzelepZaras()
    utasitasVisszajelzes(2)
    return 1

def savazoAllapotBeallitas():
    pillangoszelep = pillangoSzelepAllapot()
    golyosszelep = golyosSzelepAllapot()
    if pillangoszelep == 1 or pillangoszelep == 2:
        pillangoSzelepZaras()
    if golyosszelep == 0 or golyosszelep == 2:
        golyosSzelepNyitas()
        time.sleep(60)
    utasitasVisszajelzes(4)
    return 1

def szelepVezerles(utasitas):

    # v1,v3 nyitás
    if utasitas == 30 or utasitas == 34 :
        # ha v2,v4 zárva van
        if GPIO.input(V2_ZARVA) == 0 and GPIO.input(V4_ZARVA) == 0 :
            #nyitás
            GPIO.setup(V1_V3_VEZERLES, GPIO.OUT)
            time.sleep(1)
            GPIO.output(V1_V3_VEZERLES, V1_V3_VEZERLES_NYITAS)

            # várakozás, és köztes állapot tárolása
            while GPIO.input(V1_ZARVA) == 0 or GPIO.input(V3_ZARVA) == 0 :
                time.sleep(2)

            uzenet = allapotUzenetLetrehozasa()
            allapotTarolas(uzenet)
            
            while GPIO.input(V1_NYITVA) != 0 or GPIO.input(V3_NYITVA) != 0 :
                time.sleep(2)
            utasitasVisszajelzes(utasitas)

            uzenet = allapotUzenetLetrehozasa()
            allapotTarolas(uzenet)
            
            return 1
        else :
            #nem nyitható
            requests.post(apiService, data={'action': 4})
            return 0

    # v1,v3 zárás
    elif utasitas == 31 or utasitas == 35 :
        # akkor zárható ha a szivattyú áll, vagy ha a bemenö,elmenö oldalon tud keringtetni , ezt szerver oldalon kell majd ellenörizni
        if int(eszkozLekerdezes('p3', 'G22')) == 0 and (int(eszkozLekerdezes('p2', 'G13')) != 0 or int(eszkozLekerdezes('p2', 'G6')) != 0) :
            requests.post(apiService, data={'action': 11})
            return 0

        GPIO.setup(V1_V3_VEZERLES, GPIO.OUT)
        time.sleep(1)
        GPIO.output(V1_V3_VEZERLES, V1_V3_VEZERLES_ZARAS)

        # várakozás, és köztes állapot tárolása
        while GPIO.input(V1_NYITVA) == 0 or GPIO.input(V3_NYITVA) == 0 :
            time.sleep(2)

        uzenet = allapotUzenetLetrehozasa()
        allapotTarolas(uzenet)
        
        while GPIO.input(V1_ZARVA) != 0 or GPIO.input(V3_ZARVA) != 0 :
            time.sleep(2)
        utasitasVisszajelzes(utasitas)

        uzenet = allapotUzenetLetrehozasa()
        allapotTarolas(uzenet)
        
        return 1

    # v2,v4 nyitás
    elif utasitas == 32 or utasitas == 36 :
        if GPIO.input(V1_ZARVA) == 0 and GPIO.input(V3_ZARVA) == 0 :
            #nyitás
            GPIO.setup(V2_V4_VEZERLES, GPIO.OUT)
            time.sleep(1)
            GPIO.output(V2_V4_VEZERLES, V2_V4_VEZERLES_NYITAS)

            # várakozás, és köztes állapot tárolása
            while GPIO.input(V2_ZARVA) == 0 or GPIO.input(V4_ZARVA) == 0 :
                time.sleep(2)

            uzenet = allapotUzenetLetrehozasa()
            allapotTarolas(uzenet)
            
            while GPIO.input(V2_NYITVA) != 0 or GPIO.input(V4_NYITVA) != 0 :
                time.sleep(2)
            utasitasVisszajelzes(utasitas)

            uzenet = allapotUzenetLetrehozasa()
            allapotTarolas(uzenet)

            return 1
        else :
            #nem nyitható
            requests.post(apiService, data={'action': 5})
            return 0            

    # v2,v4 zárás
    elif utasitas == 33 or utasitas == 37 :
        # akkor zárható ha a bemenö,elmenö oldalon tud keringtetni , ezt szerver oldalon kell majd ellenörizni
        if int(eszkozLekerdezes('p2', 'G21')) != 0 or int(eszkozLekerdezes('p2', 'G19')) != 0 :
            requests.post(apiService, data={'action': 12})
            return 0

        print "v2,v4 zárás"
        GPIO.setup(V2_V4_VEZERLES, GPIO.OUT)
        time.sleep(1)
        GPIO.output(V2_V4_VEZERLES, V2_V4_VEZERLES_ZARAS)

        print "várakozás"
        # várakozás, és köztes állapot tárolása
        while GPIO.input(V2_NYITVA) == 0 or GPIO.input(V4_NYITVA) == 0 :
            time.sleep(2)

        uzenet = allapotUzenetLetrehozasa()
        allapotTarolas(uzenet)

        
        while GPIO.input(V2_ZARVA) != 0 or GPIO.input(V4_ZARVA) != 0 :
            time.sleep(2)
        utasitasVisszajelzes(utasitas)

        uzenet = allapotUzenetLetrehozasa()
        allapotTarolas(uzenet)
        
        return 1

    return 0

def eszkozLekerdezes(device, name):
    keres = requests.post(apiService, data={'action': 10, 'device': device, 'name': name })
    return keres.text

def allapotUzenetLetrehozasa():
    global cnt
    global g27Value
    global g25Value

    if cnt == 0:
        cnt = 1
        if g27Exists:
            g27Value = str(read_temp(g27Path))
        else:
            g27Value = '0.0'

        if g25Exists:
            g25Value = str(read_temp(g25Path))
        else:
            g25Value = '0.0'
    else:
        cnt = cnt + 1
        if (cnt >= 6):
            cnt = 0

    return ( "G22:" + str(GPIO.input(V2_ZARVA))
     + ",G21:" + str(GPIO.input(V2_NYITVA))
     + ",G20:" + str(GPIO.input(V4_ZARVA))
     + ",G19:" + str(GPIO.input(V4_NYITVA))
     + ",G18:" + str(GPIO.input(18))
     + ",G17:" + str(GPIO.input(17))
     + ",G16:" + str(GPIO.input(V1_ZARVA))
     + ",G13:" + str(GPIO.input(V1_NYITVA))
     + ",G12:" + str(GPIO.input(V3_ZARVA))
     + ",G6:" + str(GPIO.input(V3_NYITVA))
     + ",G27:" + g27Value
     + ",G25:" + g25Value )

def resetSignalTorlese():
    requests.post(apiService, data={'action': 8})
    return 1

def asyncSleep(ido):
    t = ido
    while t > 0:
        time.sleep(1)
        if (t % 8) == 0:
            allapotTarolas(allapotUzenetLetrehozasa())
        t = t - 1
    return 1

print "A program elindult"
init()
boot = 1
master = '-1'

for arg in sys.argv:
    if arg == '-w':
        boot = 0
        master = '0'

while True:
    if master == '-1':
        master = bejelentkezes()

    if boot == 1 and master == '0' :
        requests.post(apiService, data={'action': 17})
        uzemAllapotBeallitas()
        resetSignalTorlese()

    if boot != 1 and master == '0' :
        #print 'allapotTarolas';
        allapotTarolas(allapotUzenetLetrehozasa())
        #print 'utasitasLekerdezes';
        utasitas = int(utasitasLekerdezes())
        if utasitas == 1 :
            uzemAllapotBeallitas()
        elif utasitas == 2 :
            zartAllapotBeallitas()
        elif utasitas == 4 :
            savazoAllapotBeallitas()
        elif utasitas >= 30 and utasitas <= 37 :
            szelepVezerles(utasitas)
            
    print ""
    time.sleep(10)
