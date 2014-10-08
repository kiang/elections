選舉黃頁
=========

選舉黃頁試著要製作一個介面去呈現在台灣出現過的所有選舉以及候選人資訊，目前主要聚焦在 2014 發生的九合一選舉

選舉黃頁目前是以 CakePHP 2.5.x 製作，為了方便管理已經將核心放入了專案中

安裝方式
=========

下載：

```
$ cd /var/www
$ git clone https://github.com/kiang/elections.git
$ cd elections
```

環境設定：

```
$ cp -R tmp_default/ tmp
$ cp .htaccess.default .htaccess
$ cp webroot/.htaccess.default webroot/.htaccess
$ cp webroot/index.php.default webroot/index.php
$ cp Config/core.php.default Config/core.php
$ cp Config/database.php.default Config/database.php
```

資料庫處理：

1. 在 MySQL 建立資料庫
2. 將資料庫的設定寫入 Config/database.php
3. 匯入 Config/sql/db_*.sql.gz 資料（需要解壓縮），例如：
  1. `gzip -d db_20140731.sql.gz`
  2. `mysql -uroot -p your_db < db_20140731.sql`
4. 匯入的資料庫會需要重設管理者帳號，請先清空下面幾個資料表
  1. `TRUNCATE acos;`
  2. `TRUNCATE aros;`
  3. `TRUNCATE aros_acos;`
  4. `TRUNCATE groups;`
  5. `TRUNCATE group_permissions;`
  6. `TRUNCATE members;`
5. 如果只需要一個空的資料庫，可以匯入 Config/schema/schema.sql
6. 透過瀏覽器開啟網頁，進入登入畫面時會引導建立新的帳號、密碼
