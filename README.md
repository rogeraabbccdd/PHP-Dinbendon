# 訂便當系統
泰山職訓訂便當系統

## 功能
- **店家管理**：新增、編輯店家資訊與菜單，顯示評分、準時送達率與價格區間
- **團購**：以店家開團，開團時保存菜單快照，可設定為公開團購讓其他班級參與
- **訂餐**：選擇品項、數量並填寫備註，團購開放期間可修改或取消訂單
- **結單**：開團者可結單、關閉或重新開啟團購，並記錄是否準時送達
- **訂單統計**：依品項統計數量與訂購者，可下載 csv
- **隨機店家**：不知道吃什麼時隨機抽一家店
- **評論**：對店家評分與留言

## 技術架構
- 後端：[Laravel 12](https://laravel.com/)
- 前端：[Vue 3](https://vuejs.org/) + [Inertia.js](https://inertiajs.com/) + [Quasar](https://quasar.dev/)，模板使用 [Pug](https://pugjs.org/)
- 建置工具：[Vite](https://vite.dev/)

## 環境需求
- [PHP](https://www.php.net/) >= 8.2
- [Composer](https://getcomposer.org/)
- [Laravel 系統需求](https://laravel.com/docs/12.x/deployment#server-requirements)
- [MySQL](https://www.mysql.com/) 或 [MariaDB](https://mariadb.org/)
- [Bun](https://bun.sh/)

## 本地開發環境設定步驟
1. **安裝 PHP 相依套件**
   ```bash
   composer install
   ```

2. **安裝 JavaScript 相依套件**
   ```bash
   bun install
   ```

3. **設定環境變數**
   - 複製 `.env.example` 檔案為 `.env`
     ```bash
     # Windows
     copy .env.example .env
     # macOS / Linux
     cp .env.example .env
     ```
   - 產生應用程式金鑰 (APP_KEY)
     ```bash
     php artisan key:generate
     ```
   - 開啟 `.env` 檔案，根據環境設定資料庫連線資訊
     ```ini
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=laravel
     DB_USERNAME=root
     DB_PASSWORD=
     ```
   - 將假資料語系設為繁體中文
     ```ini
     APP_FAKER_LOCALE=zh_TW
     ```

4. **建立資料表**
   ```bash
   php artisan migrate
   ```

5. **建立假資料**
   ```bash
   php artisan db:seed
   ```
   會建立班級、學生、店家、菜單、團購、訂單與評論等測試資料，可使用以下帳號登入
   | 學號 | 密碼 |
   | --- | --- |
   | 1111 | 1111 |

   其他假資料學生的密碼與學號相同。若要清空資料庫並重新建立，可執行
   ```bash
   php artisan migrate:fresh --seed
   ```
   > ⚠️ `migrate:fresh` 會刪除資料庫中的所有資料表，請勿在正式環境執行

6. **啟動開發伺服器**
   ```bash
   composer dev
   ```
   啟動後開啟 <http://localhost:8000>

## 部署
1. 安裝相依套件
   ```bash
   composer install --no-dev --optimize-autoloader
   bun install
   ```
2. 設定 `.env`，並將 `APP_ENV` 設為 `production`、`APP_DEBUG` 設為 `false`
3. 建立資料表
   ```bash
   php artisan migrate --force
   ```
4. 建置前端資源
   ```bash
   bun run build
   ```
5. 快取設定與路由
   ```bash
   php artisan optimize
   ```

店家圖片會上傳至 `public/storage/store`，請確認網頁伺服器對該目錄有寫入權限。

## 常用指令
| 指令 | 說明 |
| --- | --- |
| `composer dev` | 啟動開發伺服器 |
| `bun run build` | 建置前端資源 |
| `bun run lint` | 執行 ESLint 並自動修正 |
| `bun run format` | 使用 Prettier 格式化前端程式碼 |
| `php artisan user:generate-insert --file=<檔案>` | 從檔案產生新增使用者的 SQL |

### 產生新增使用者的 SQL
系統沒有註冊功能，使用者需由管理者新增。準備一個文字檔，每行一位使用者，格式為 `學號,姓名,班級 ID,座號`
```text
115001,王小明,1,1
115002,陳小華,1,2
```
執行後會輸出 `INSERT` 語法，密碼預設與學號相同
```bash
php artisan user:generate-insert --file=users.txt
```
