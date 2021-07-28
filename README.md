# Savazó rendszer vezérlő

## 1.A savazó rendszer architektúra
![Felépítés](https://raw.githubusercontent.com/arboinvest/savazo/master/felepites.png)
A savazó rendszer működését Raspberry Pi típusú mikroszámítógépek vezérlik, ezekből 4 ilyen található a rendszerben:
1. P3 : A rendszer központi számítógépe. Vezérli a P1,és P2 egységeket, valamint a kezelő felület web kiszolgálása is itt történik.
2. P1,P2 : Az egyes hőcserélő körök szelepeit vezérli, és a hozzá tartozó hőmérők értékeinek kiolvasása is itt történik.
3. P4 : A szivattyúházban található nagyteljesítményű nyomószivattyú mérő műszereinek a kiolvasása itt történik. A kiolvasott eredmények továbbításra történnek a P3 felé. Opcionális lehetőségként a P4 képes riasztások küldésére is, amely SMS-ben történik.
### 1.1 A P3 tartalma
arbo/bin/arduino/base/base.ino
A P3 számítógépre épített Arduino shield programja, amelyet Arduino IDE-vel lehet feltölteni az eszközre.

arbo/bin/vezerlok/p3.py:
A P3 számítógépen futó vezérlő program, amely Python 2 -ben íródott. Az indítás rögtön a boot-olás után megtörténik, ezt az /etc/rc.local-ba kell belerakni.
```
python2.7 /home/pi/arbo/p3.py &>/dev/null &
```

arbo/html
A webkiszolgáló fáljai ebben a könyvtárban vannak, amelyeknek a szerveroldali része php-vel megvalósított.

arbo/db/arbo.sql
A P3 számítógépen található mysql adatbázis létrehozására szolgál. Az adatbázis hozzáférési paramétereit az arbo/html/config/Config.php -ben is át kell írni.
```
...
	const MYSQL_HOST = 'localhost';
	const MYSQL_USER = 'root';
	const MYSQL_PASSWORD = 'mypass';
	const MYSQL_DB = 'arbo';
...
```
