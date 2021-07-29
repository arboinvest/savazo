# Savazó rendszer vezérlő

## 1.A savazó rendszer architektúra
![Felépítés](https://raw.githubusercontent.com/arboinvest/savazo/master/felepites.png)
A savazó rendszer működését Raspberry Pi típusú mikroszámítógépek vezérlik, ezekből 4 ilyen található a rendszerben:
1. P3 : A rendszer központi számítógépe. Vezérli a P1,és P2 egységeket, valamint a kezelő felület web kiszolgálása is itt történik.
2. P1,P2 : Az egyes hőcserélő körök szelepeit vezérli, és a hozzá tartozó hőmérők értékeinek kiolvasása is itt történik.
3. P4 : A szivattyúházban található nagyteljesítményű nyomószivattyú mérő műszereinek a kiolvasása itt történik. A kiolvasott eredmények továbbításra történnek a P3 felé. Opcionális lehetőségként a P4 képes riasztások küldésére is, amely SMS-ben történik.
### 1.1 A P3 számítógép
arbo/bin/arduino/base/base.ino
A P3 számítógépre épített Arduino shield programja, amelyet Arduino IDE-vel lehet feltölteni az eszközre.

arbo/bin/vezerlok/p3.py:
A P3 számítógépen futó vezérlő program, amely Python 2 -ben íródott. Az indítás rögtön a boot-olás után megtörténik, ezt az /etc/rc.local-ba kell belerakni.
```
python2.7 /home/pi/arbo/p3.py &>/dev/null &
```

arbo/html/\
A webkiszolgáló fáljai ebben a könyvtárban vannak, amelyeknek a szerveroldali része php-vel megvalósított. \
\
arbo/html/data.php\
Az adatbázisban tárolt legutóbbi adatértékek kérdezhetőek le az API-val json formátumban.\
\
arbo/html/index.php\
Az vezérlések indítását és végrehajtását végi el ez az API. A meghívott parancs praméterei POST változók, amelyek feldolgozásra kerülnek.\
\
arbo/html/vezerlo.php\
A vezérlő dashboard felületét tartalmazza ez a php fájl. Az adatok változásának megjelenítését, és a funkciók indítását az "arbo/html/js/vezerlok.js" végzi, amely egy javascript osztály JQuery elemekkel.\
\
arbo/html/pid.php\
A kapcsolási rajz nézetet tartalmazza. Hozzá tartozik a "arbo/html/js/pid.js" javascript osztály JQuery elemekkel.\
\
arbo/html/vezerlo/\
A könytárban megtalálhatók a vezérlések szerveroldali része php fájlokban (pl: mérés,savazás,kézi üzemmód), valamint a szelep vezérlő elemek státuszának lekérdezése (nyitott,zárt,köztes állapotú).\
\
arbo/html/js/\
A könytárban megtalálhatók a JQuery/javascript osztályok és lib fájlok (jquery, charts, bootstrap).\
\
arbo/html/img/\
A könyvtárban megtalálhatók a kapcsolási rajz nézet ikonjai, és háttérképe.\
\
További fájlok az arbo/html/ könyvtárban:\
login.php - A bejelentkezés felülete\
nyofdata.php - A P4 értékeit lekérdező API, amely helyből, ajax hívással is hívható\
dummy.php - a dashboard-on található egyéb karbantartó funkciók (pl: számítógép pingelése, hard reset)\
passreplace.php - a jelszó csere felülete\
usrmngmt.php - a felhasználói adminisztráció felülete\
\
arbo/db/arbo.sql\
A P3 számítógépen található mysql adatbázis létrehozására szolgál. Az adatbázis hozzáférési paramétereit az arbo/html/config/Config.php -ben is át kell írni.\
```
...
	const MYSQL_HOST = 'localhost';
	const MYSQL_USER = 'root';
	const MYSQL_PASSWORD = 'mypass';
	const MYSQL_DB = 'arbo';
...
```
Szintén itt kell beállítani a P4 számítógép végpont elérését.
```
const NYOF_URL = 'http://192.168.2.172:80';
```

A P3 számítógép port kiosztása:

|Megnevezés|Port| 0 | 1 |
|------------------------------|-----|-------------------|----------------|
|Sav keringtető szivattyú|G22|Szivattyú jár|Szivattyú áll|
|P1 reset|G20|P1 off|P1 on|
|P2 reset|G19|P2 off|P2 on|
|Átfolyásmérő LOW riasztás|G17|túl kevés átfolyás|normál átfolyás|
|Átfolyásmérő HIGH riasztás|G16|túl sok átfolyás|normál átfolyás|
|Keverőtartály szint|G5|túlcsordulás|normális szint|
|Keverőtartály szint|G12|felső határ fölött|normális szint|
|Keverőtartály szint|G6|alsó határ alatt|normális szint|
|pH mérő 1.|A1| | |
|pH mérő 2.|A2| | |
|Indukciós áramlásmérő|A3| | |
|Nyomásmérő 1.|A4| | |
|Nyomásmérő 2.|A5| | |
\
ahol:\
Axx: Analóg portok\
Gxx: GPIO digitális portok\
\
A "/boot/config.txt" tartalma A P3-an:
```
...
hdmi_force_hotplug=1
enable_uart=1
dtoverlay=disable-wifi
dtoverlay=pi3-disable-bt
gpio=22=op,dh
```

### 1.2 A P2 számítógép
arbo/bin/vezerlok/p2.py \
A P2 számítógép vezérlő programja, amely Python 2 -ben íródott. Az indítás rögtön a boot-olás után megtörténik, ezt az /etc/rc.local-ba kell belerakni.
```
python2.7 /home/pi/arbo/p2.py &>/dev/null &
```
A vezérlő programban szükséges megadni a P3 helyi ip címét, valamint a hőmérséklet érzékelők elérését a /sys/bus/w1 könyvtárból.
```
...
serverURL = '192.168.5.153'
...
g27 = '..-............'
g25 = '..-............'
g27Path = '/sys/bus/w1/devices/' + g27 + '/w1_slave'
g25Path = '/sys/bus/w1/devices/' + g25 + '/w1_slave'...
...
```
A hőmérséklet érzékelők listázása:
```
ls /sys/bus/w1/devices/ | grep -
```

A "boot/config.txt" tartalma A P1-en és P2-n:
```
...
hdmi_force_hotplug=1
enable_uart=1
dtoverlay=w1-gpio,pullup="on",gpiopin=27
dtoverlay=disable-wifi
dtoverlay=pi3-disable-bt
gpio=18=op,dl
gpio=17=op,dh
```

|Megnevezés|Port| 0 | 1 |
|------------------------------|-----|-------------------|----------------|
|Hőmérséklet szenzor, bemenő|G27| | |
|Hőmérséklet szenzor, elmenő|G25| | |
|V6 pillangószelep végállás|G22|V6 zárva|köztes állapot|
|V6 pillangószelep végállás|G21|V6 nyitva|köztes állapot|
|V8 pillangószelep végállás|G20|V8 zárva|köztes állapot|
|V8 pillangószelep végállás|G19|V8 nyitva|köztes állapot|
|V6 és V8 nyit/zár|G18|V6,V8 zárás|V6,V8 nyitás|
|V5 és V7 nyit/zár|G17|V5,V7 nyitás|V5,V7 zárás|
|V5 golyósszelep végállás|G16|V5 zárva|köztes állapot|
|V5 golyósszelep végállás|G13|V5 nyitva|köztes állapot|
|V7 golyósszelep végállás|G12|V7 zárva|köztes állapot|
|V7 golyósszelep végállás|G6|V7 nyitva|köztes állapot|


### 1.3 A P1 számítógép
arbo/bin/vezerlok/p1.py \
A P1 számítógép vezérlő programja, amely Python 2 -ben íródott. Az indítás rögtön a boot-olás után megtörténik, ezt az /etc/rc.local-ba kell belerakni.
```
python2.7 /home/pi/arbo/p1.py &>/dev/null &
```
A vezérlő programban szükséges megadni a P3 helyi ip címét, valamint a hőmérséklet érzékelők elérését a /sys/bus/w1 könyvtárból.
```
...
serverURL = '192.168.5.153'
...
g27 = '..-............'
g25 = '..-............'
g27Path = '/sys/bus/w1/devices/' + g27 + '/w1_slave'
g25Path = '/sys/bus/w1/devices/' + g25 + '/w1_slave'...
```

A P1 számítógép port kiosztása:

|Megnevezés|Port| 0 | 1 |
|------------------------------|-----|-------------------|----------------|
|Hőmérséklet szenzor, bemenő|G27| | |
|Hőmérséklet szenzor, elmenő|G25| | |
|V2 pillangószelep végállás|G22|V6 zárva|köztes állapot|
|V2 pillangószelep végállás|G21|V6 nyitva|köztes állapot|
|V4 pillangószelep végállás|G20|V8 zárva|köztes állapot|
|V4 pillangószelep végállás|G19|V8 nyitva|köztes állapot|
|V2 és V4 nyit/zár|G18|V2,V4 zárás|V2,V4 nyitás|
|V1 és V3 nyit/zár|G17|V1,V3 nyitás|V1,V3 zárás|
|V1 golyósszelep végállás|G16|V1 zárva|köztes állapot|
|V1 golyósszelep végállás|G13|V1 nyitva|köztes állapot|
|V3 golyósszelep végállás|G12|V3 zárva|köztes állapot|
|V3 golyósszelep végállás|G6|V3 nyitva|köztes állapot|

### 1.4 A P4 számítógép

nyof/P4:\
A P4 vezérlő programja. Feladata, hogy a kiolvasott értékeket biztosítsa a P3 felé.\
\
A programban szükséges beállítani az egyik hőmérséklet érzékelő elérését, valamint az sms küldéshez használt számítógép végpont elérését.

```
...
g17 = '28-030997796012'
g17Path = '/sys/bus/w1/devices/' + g17 + '/w1_slave'
...

requests.post("http://............./nyof_alarm.php", data={'pw': smsPass, 'msg': '1'})
...
requests.post("http://............./nyof_alarm.php", data={'pw': smsPass, 'msg': '4'})
...
requests.post("http://............./nyof_alarm.php", data={'pw': smsPass, 'msg': '2'})
...
requests.post("http://............./nyof_alarm.php", data={'pw': smsPass, 'msg': '3'})
```

A /boot/config.txt beállításai:\
```
hdmi_force_hotplug=1
dtoverlay=disable-wifi
dtoverlay=pi3-disable-bt
dtoverlay=w1-gpio,gpiopin=16
dtoverlay=w1-gpio,gpiopin=17
enable_uart=1
```

nyof/php\
A P4 webkiszolgálón elhelyezendő php fájlok. \
\
nyof/php/index.php \
Ez az api visszaadja a kiolvasott adatokat json formában.\
\
\
nyof/sms/\
A könytárban található fájlok bármelyik webkiszolgálóra elhelyezhetők, a fenti végpontot ennek megfelelően kell beállítani.\
A "send.js" - NodeJs-ben íródott - program végzi el végül az sms küldést, ezért erre a számítógépre NodeJs-t kell telepíteni, és a fájlban be kell állítani milyen telefonszámról és melyik telefonszámra küldje az sms-t.

```
var postData = {
  from: "...CELL PHONE NUMBER...",
  to: ["...CELL PHONE NUMBER...  ."],
  body: "",
};
```

Az sms api regisztrációt a következő helyen lehet elvégezni:\
\
[https://developers.sinch.com/docs/sms/getting-started/node/nodesend/](https://developers.sinch.com/docs/sms/getting-started/node/nodesend/)
\
A regisztrációt követően a "send.js" fájlban az sms api hívást a megfelelő Id azonosítóra kell átírni.








