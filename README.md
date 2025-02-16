選舉黃頁 / Election Directory
=========

[中文說明]
選舉黃頁試著要製作一個介面去呈現在台灣出現過的所有選舉以及候選人資訊，主要的資料來自中央選舉委員會，網路志工協助更新資料，更新過程大多經由網路查證，如果有錯誤之處請以中選會公告資料為主。

官方網站：[https://elections.olc.tw/](https://elections.olc.tw/)

[English]
Election Directory aims to create an interface to present information about all elections and candidates that have appeared in Taiwan, currently focusing on the 2014 9-in-1 local elections.

Official Website: [https://elections.olc.tw/](https://elections.olc.tw/)

技術說明 / Technical Details
=========
選舉黃頁目前是以 CakePHP 2.5.x 製作，為了方便管理已經將核心放入了專案中。

The system is built with CakePHP 2.5.x. The core framework has been included in the project for easier management.

安裝方式 / Installation
=========

下載 / Download：

```
$ cd /var/www
$ git clone https://github.com/kiang/elections.git
$ cd elections
```

環境設定 / Environment Setup：

```
$ cp -R tmp_default/ tmp
$ cp .htaccess.default .htaccess
$ cp webroot/.htaccess.default webroot/.htaccess
$ cp webroot/index.php.default webroot/index.php
$ cp Config/core.php.default Config/core.php
$ cp Config/database.php.default Config/database.php
```

資料庫處理 / Database Setup：

1. 在 MySQL 建立資料庫 / Create a MySQL database
2. 將資料庫的設定寫入 Config/database.php / Configure database settings in Config/database.php
3. 匯入 Config/sql/db_*.sql.gz 資料（需要解壓縮）/ Import data from Config/sql/db_*.sql.gz (needs decompression)：
   例如 / Example：
   1. `gzip -d db_20140731.sql.gz`
   2. `mysql -uroot -p your_db < db_20140731.sql`
4. 匯入的資料庫會需要重設管理者帳號，請先清空下面幾個資料表 / Reset admin account by clearing these tables：
   1. `TRUNCATE acos;`
   2. `TRUNCATE aros;`
   3. `TRUNCATE aros_acos;`
   4. `TRUNCATE groups;`
   5. `TRUNCATE group_permissions;`
   6. `TRUNCATE members;`
5. 如果只需要一個空的資料庫，可以匯入 Config/schema/schema.sql / For an empty database, import Config/schema/schema.sql
6. 透過瀏覽器開啟網頁，進入登入畫面時會引導建立新的帳號、密碼 / Open the website in a browser, you'll be guided to create a new account and password when accessing the login screen
