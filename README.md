Cara Jalaninnya

Bahan:
Dowload Xampp
https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe/download

Microsoft Access Database Engine 2016 Redistributable
https://www.microsoft.com/en-us/download/details.aspx?id=54920

Install
- Xampp
- Microsoft Access Database Engine 2016 Redistributable
- File yang ada di github ini

Cara:
Jalan kan Xampp
Masuk ke contorl panel XAMPP > Config Di Apache > php.ini 
cari : ;extension=pdo_odbc hapus ; (titik komanya) lalu save

Copy Seluruh file di github ini ke
Drive C > xampp > htdocs > paste disini

Setting ODBC Data Source (64-bit)
Klik Start > Ketik: ODBC Data Source (64-bit) > Masuk ke tab System DSN > Add.. > Pilih : Microsoft Access Driver (*.mbd, *accdb) > Finish
Data Source Name Isikan : db_darwoto
Description : Bebas
Klik Slect Cari File db_darwoto.accdb yang tadi sudah di pindahkan ke htdocs > klik databasenya > OK
lalu Ok lagi maka akan terbentuk Syetem DSN nya

Lanjut...
Buka Xampp kembali lalu jalankan Apache dan MYSQLnya
dan masuk ke broser Chrome
ketikan :
http://localhost/warung_darwoto/index.php
