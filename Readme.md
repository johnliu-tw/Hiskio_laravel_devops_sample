# Laravel 雲端部署課程示範專案

## 1. 課程專案介紹

本課程將帶領學員了解如何將 Laravel 應用程式，透過不同的方式部署在正式的伺服器上。這篇 Readme 會介紹示範用專案的基礎架構，此專案需要資料庫，並結合 Redis 來管理佇列任務。以下是專案中重要的文件與其功能介紹：

- `api/app/Console/Commands/GeneratePostNotifications.php`：定義生成文章通知的 Artisan 指令。
- `api/app/Http/Controllers/PostController.php`：負責呈現最基本的 RestAPI 行為。
- `api/app/Jobs/SendNewPostNotification.php`：處理發送新文章通知的佇列任務。
- `api/routes/console.php`：定義 Schedular 排成器工作。
- `api/database/seeders/DatabaseSeeder.php`：負責 Posts 種子資料的建立。
- `api/tests/Feature/ExampleTest.php`：範例測試檔案，驗證應用程式的功能。


此專案所有應用程式功能都在 `api/` 目錄下，第一層未來課程會安排新增部署相關的程式碼。

## 2. 基本安裝步驟

請按照以下步驟安裝並設定此專案：

1. 複製環境設定檔案：
    ```bash
    cp .env.example .env
    ```

2. 設定環境變數 (`.env` 文件)：
    - `APP_URL=http://localhost:8000`
    - `DB_CONNECTION=mysql`: 或是任何你習慣的資料庫
    - `DB_DATABASE=laravel_devops`: 或是任何你習觀的名稱
    - `DB_PASSWORD` （請設定您的資料庫密碼）
    - `QUEUE_CONNECTION=redis`: 請使用 redis 作為 Queue 任務載體
    - `REDIS_CLIENT=predis`

3. 安裝相關套件：
    ```bash
    composer install
    ```

4. 產生應用程式金鑰：
    ```bash
    php artisan key:generate
    ```

5. 建立符號連結到儲存目錄：
    ```bash
    php artisan storage:link
    ```

6. 執行資料庫遷移：
    ```bash
    php artisan migrate
    ```

7. 執行資料庫預設資料產生指令：
    ```bash
    php artisan db:seed
    ```

8. 啟動伺服器：
    ```bash
    php artisan serve
    ```

## 3. 測試應用程式

### 3.1 網頁測試

1. 訪問以下 URL 測試文章相關功能：
    - `http://localhost:8000/posts`
    - `http://localhost:8000/posts/latest-report`

2. 根據 `http://localhost:8000/posts/latest-report` 畫面指示，試著打開產生的檔案，例如: http://localhost:8000/storage/latest_post_report_1724566876.txt

### 3.2 佇列工作測試

1. 在終端機使用指令啟動 Redis server，例如:
    ```bash
    > redis-server
    
        69505:C 25 Aug 2024 14:02:59.505 # oO0OoO0OoO0Oo Redis is starting oO0OoO0OoO0Oo
    69505:C 25 Aug 2024 14:02:59.505 # Redis version=5.0.7, bits=64, commit=00000000, modified=0, pid=69505, just started
    69505:C 25 Aug 2024 14:02:59.505 # Warning: no config file specified, using the default config. In order to specify a config file use redis-server /path/to/redis.conf
    69505:M 25 Aug 2024 14:02:59.506 * Increased maximum number of open files to 10032 (it was originally set to 256).
                    _._
               _.-``__ ''-._
          _.-``    `.  `_.  ''-._           Redis 5.0.7 (00000000/0) 64 bit
      .-`` .-```.  ```\/    _.,_ ''-._
     (    '      ,       .-`  | `,    )     Running in standalone mode
     |`-._`-...-` __...-.``-._|'` _.-'|     Port: 6379
     |    `-._   `._    /     _.-'    |     PID: 69505
      `-._    `-._  `-./  _.-'    _.-'
     |`-._`-._    `-.__.-'    _.-'_.-'|
     |    `-._`-._        _.-'_.-'    |           http://redis.io
      `-._    `-._`-.__.-'_.-'    _.-'
     |`-._`-._    `-.__.-'    _.-'_.-'|
     |    `-._`-._        _.-'_.-'    |
      `-._    `-._`-.__.-'_.-'    _.-'
          `-._    `-.__.-'    _.-'
              `-._        _.-'
                  `-.__.-'

    69505:M 25 Aug 2024 14:02:59.507 # Server initialized
    69505:M 25 Aug 2024 14:02:59.507 * Ready to accept connections

    ```


2. 使用 Artisan 指令生成通知佇列：
    ```bash
    > php artisan generate:notifications 2 3 normal
    > php artisan generate:notifications 2 3 high
    ```

3. 在使用 Redis CLI 查看佇列：
    ```bash
    > redis-cli
    ```

    在 Redis CLI 中執行以下命令來檢視佇列中的任務：
    ```bash
    127.0.0.1:6379> KEYS *
    ```

    結果應包括以下內容：
    ```
    1) "laravel_database_queues:default:notify"
    2) "laravel_database_queues:default"
    3) "laravel_database_queues:high"
    4) "laravel_database_queues:high:notify"
    ```

    可以使用以下命令檢視特定佇列中的任務：
    ```bash
    127.0.0.1:6379> LRANGE laravel_database_queues:high 0 -1
    1) "{\"uuid\":\"1aa5634c-eb7d-49f8-bb30-af3a21578e41\",\"displayName\":\"App\\\\Jobs\\\\SendNewPostNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendNewPostNotification\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\SendNewPostNotification\\\":1:{s:5:\\\"queue\\\";s:4:\\\"high\\\";}\"},\"id\":\"DAoJUTCyKNmwRiglIUxdITHKP0tVZvzy\",\"attempts\":0}"
    ...
    ...
    
    127.0.0.1:6379> LRANGE laravel_database_queues:default 0 -1
    ```

4. 執行佇列工作：
    ```bash
    > php artisan queue:work --queue=high,default
    
    INFO  Processing jobs from the [high,default] queues.  

      2024-08-25 06:08:27 App\Jobs\SendNewPostNotification ........................... RUNNING
      2024-08-25 06:08:28 App\Jobs\SendNewPostNotification ........................... 1s DONE
      2024-08-25 06:08:28 App\Jobs\SendNewPostNotification ........................... RUNNING
      2024-08-25 06:08:29 App\Jobs\SendNewPostNotification ........................... 1s DONE
      2024-08-25 06:08:29 App\Jobs\SendNewPostNotification ........................... RUNNING
      2024-08-25 06:08:30 App\Jobs\SendNewPostNotification ........................... 1s DONE
    ```

    確認所有任務已處理完成，使用以下命令確認 Redis 中的佇列清單，預期結果應為空：
    ```bash
    127.0.0.1:6379> KEYS *
    (empty list or set)
    ```


### 3.3 排程測試

執行 Artisan 指令以測試排程：
```bash
> php artisan schedule:run

  2024-08-25 06:39:57 Running [Callback] string(22) "schedule call finished"
...................................... 7.98ms DONE
```


### 3.4 PHPUnit 測試
執行 Artisan 指令執行 PHPUnit 單元測試:
```bash
> php artisan test'

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                     0.11s  

  Tests:    2 passed (2 assertions)
  Duration: 0.18s
```