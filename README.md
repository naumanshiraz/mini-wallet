# MiniWallet

A Laravel + Vite + Pusher powered mini wallet application.

## Requirements

* PHP >= 8.1
* Composer
* Node.js & npm
* MySQL
* Redis (optional, for caching/queues)

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-username/mini-wallet.git
   cd mini-wallet
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JS dependencies**

   ```bash
   npm install
   ```

4. **Create `.env` file**
   Copy `.env.example` (or use the snippet below):

   ```bash
   cp .env.example .env
   ```

   Example `.env` (with placeholders):

   ```env
   APP_NAME=MiniWallet
   APP_ENV=local
   APP_KEY=base64:generate-this-using-php-artisan-key-generate
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mini_wallet
   DB_USERNAME=root
   DB_PASSWORD=

   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your-pusher-app-id
   PUSHER_APP_KEY=your-pusher-app-key
   PUSHER_APP_SECRET=your-pusher-app-secret
   PUSHER_APP_CLUSTER=your-pusher-app-cluster
   PUSHER_APP_DEBUG=true

   VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
   VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
   ```

5. **Generate app key**

   ```bash
   php artisan key:generate
   ```

6. **Run migrations**

   ```bash
   php artisan migrate
   ```

7. **Run backend (Laravel)**

   ```bash
   php artisan serve
   ```

8. **Run frontend (Vite)**

   ```bash
   npm run dev
   ```

## Development

* **Queues**
  If using queues:

  ```bash
  php artisan queue:work
  ```

* **Broadcasting**
  Ensure you have a [Pusher](https://pusher.com/) account and update your `.env` with valid credentials.

## Notes

* Never commit real `.env` credentials. Use placeholders in public repos.
* The app defaults to using **database sessions** and **database queues**.
* Frontend uses **Vite** with hot reloading.

---

Made with ❤️ using Laravel, Vite, and Pusher.
