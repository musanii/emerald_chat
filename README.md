# Emerald Chat 🌿

**Emerald Chat** is a high-performance, real-time, embeddable chat engine designed specifically for seamless integration into existing Laravel applications and custom web platforms. Built with a modern micro-modular architecture, Emerald Chat provides Slack/Discord-style workspace communication with real-time messaging, department scoping, channel channels, thread support, and file attachments.

---

## 🚀 Key Features

- **Multi-Tenant Scoping:** Built-in organization and department-level workspace isolation.
- **Real-Time Engine:** Native support for WebSockets via **Laravel Reverb** or **Pusher**.
- **Embeddable UI:** Modular Vue 3 / Web Component interface designed for zero-CSS leak integration.
- **Granular Access Control:** Public, private, and direct messaging (DM) channels with pivot-level permission checks.
- **Asynchronous Storage:** Direct-to-S3/MinIO chunked file uploads with strict file type validation.
- **Searchable Archives:** Elastic-ready message indexing for fast history queries.

---

## 🏗️ Architecture Stack

- **Backend Framework:** Laravel 11 / 12 (Headless REST API)
- **Real-Time Driver:** Laravel Reverb (WebSocket Protocol)
- **Authentication:** Laravel Sanctum (Token-Based / Stateful Session)
- **Database Engine:** PostgreSQL / MySQL (InnoDB)
- **In-Memory Cache & WebSockets:** Redis
- **Frontend Layer:** Vue 3 (Composition API, Pinia, Tailwind CSS)
- **Storage Driver:** AWS S3 / MinIO S3 API

---

## 📦 Quick Start Guide

### Prerequisites
- PHP `>= 8.2`
- Composer `>= 2.5`
- Node.js `>= 18.0` & npm
- PostgreSQL or MySQL server
- Redis server

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-org/emerald-chat.git
   cd emerald-chat
   ```

2. **Install Backend Dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Set up your database credentials and real-time settings in `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=emerald_chat
   DB_USERNAME=postgres
   DB_PASSWORD=secret

   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=emerald-chat-app
   REVERB_APP_KEY=emerald-key
   REVERB_APP_SECRET=emerald-secret
   REVERB_HOST=127.0.0.1
   REVERB_PORT=8080
   ```

4. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Start Reverb & Queue Workers:**
   ```bash
   php artisan reverb:start
   php artisan queue:work
   ```

6. **Serve API:**
   ```bash
   php artisan serve
   ```

---

## 📄 Documentation Links

- [System Architecture Specification (`architecture.md`)](./architecture.md)
- [Development Plan & Commit Cycles (`plan.md`)](./plan.md)

---

## 🛡️ License

Emerald Chat is open-sourced software licensed under the [MIT License](LICENSE).