# 🎓 SiPinjam 🚀
### **Smart Item & Room Reservation Enterprise System**

SiPinjam is a state-of-the-art, high-performance web application designed to automate and streamline the reservation process for items, assets, and rooms. Built on a powerful enterprise architecture, the system provides real-time conflict resolution, transactional numbering, automated PDF letters, and intelligent auto-sanctions for overdue checkouts.

---

## 🎨 Design Philosophy: Modern Minimalist
SiPinjam features a premium, state-of-the-art **Modern Minimalist** user interface. Designed with visual excellence and user experience in mind, it implements:
- **Clean Typography & Ample Whitespace** using tailored high-quality font families.
- **Harmonious Palettes** featuring smooth slate scales, elegant slate-border separations, and clean backgrounds.
- **Micro-Animations & Hover Scaling** (built with CSS and GSAP) that bring components to life.
- **Aspect-Constrained Banners (4:3)** coupled with elegant fallback image placeholders, completely eliminating standard browser layout shifts.

---

## 🛡️ Core Features

*   📅 **Interactive Calendar & Schedule Conflict Detection**: Fully integrated with FullCalendar Vue 3, allowing instant schedule previews and guaranteeing that no two bookings overlap on identical slots.
*   ⏳ **Delayed Deduction Approval System**: Reservations go through a rigorous validation and verification pipeline. Admin reviews do not prematurely lock stock or rooms unless verified.
*   📄 **Auto-Generated PDF Letters**: Approved reservations immediately generate custom-stamped letters with transactional, locked serialized serial numbers (`nomor_surat`).
*   ⚖️ **Overtime Auto-Sanction Scheduler**: A daily cron validator combines scheduled dates and times, checks them against a strict **12-hour grace period**, and automatically locks negligent user accounts for **30 days**.
*   🖼️ **GD Image Crop Pipeline**: Center-crops uploaded rooms and item photos to a standard `4:3` layout (800x600), preserving alpha transparency channels for PNG/WebP files.

---

## 🛠️ Tech Stack & Integrations

![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/vuejs-%2335495e.svg?style=for-the-badge&logo=vuedotjs&logoColor=%234FC08D)
![Inertia.js](https://img.shields.io/badge/inertia.js-%239575CD.svg?style=for-the-badge&logo=inertia&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)

| Layer | Technologies & Libraries |
| :--- | :--- |
| **Backend Core** | Laravel 11 (PHP 8.2+), Eloquent ORM |
| **Frontend Core** | Vue 3 (Composition API), Inertia.js |
| **Styling & UI** | Tailwind CSS, Reka UI, Radix Vue, Lucide Icons |
| **Integrations** | Laravel Socialite (Google SSO), SMTP Mail, Fonnte WhatsApp Gateway |
| **PDF Reporting** | Barryvdh Laravel DOMPDF |

---

## 🚀 Step-by-Step Installation

Follow these copy-pasteable terminal commands to set up the development environment locally.

### **Step 1: Clone the Repository & Install Dependencies**
```bash
# 1. Clone the repository
git clone https://github.com/your-username/sipinjam-core.git
cd sipinjam-core

# 2. Install backend PHP packages
composer install

# 3. Install frontend Node modules
npm install
```

### **Step 2: Copy Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

### **Step 3: Connect Public Storage Symlink**
```bash
php artisan storage:link
```

### **Step 4: Seed the Database**
Ensure your local MySQL server is running, then populate the schema and mock seeders:
```bash
php artisan migrate:fresh --seed
```

---

## ⚙️ Environment (`.env`) Configuration Guide

Configure your `.env` variables to enable all real-time mailers and notifications.

### **1. Database Configuration**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipinjam
DB_USERNAME=root
DB_PASSWORD=o0o0 # Set your MySQL user password
```

### **2. SMTP / Mailer Setup (Critical for Notification Mailers)**
This is required to dispatch mail notifications like `BookingCreatedNotification` and `BookingStatusUpdated` when booking states are created or modified.
To configure Gmail SMTP:
1. Enable **2-Step Verification** on your Google account.
2. Navigate to Google Account > Security > App Passwords.
3. Generate a new App Password (a 16-letter code).
4. Fill in the parameters:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME="your-gmail-account@gmail.com"
MAIL_PASSWORD="your-sixteen-letter-app-password"
MAIL_ENCRYPTION=smtps
MAIL_FROM_ADDRESS="your-gmail-account@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### **3. Third-Party Integrations**

#### **Google OAuth (SSO) Credentials**
```env
GOOGLE_CLIENT_ID="your-client-id.apps.googleusercontent.com"
GOOGLE_CLIENT_SECRET="your-client-secret"
GOOGLE_REDIRECT_URI="http://localhost:8000/auth/google/callback"
```

#### **Fonnte WhatsApp Gateway**
```env
FONNTE_TOKEN="your-fonnte-token-here"
FONNTE_URL="https://api.fonnte.com/send"
```

---

## 🚥 Running the Application

For a fully automated workspace (including notifications, PDF exports, and background validations), launch the following processes in separate terminal instances:

*   **Terminal 1: Laravel Web Server**
    ```bash
    php artisan serve
    ```
*   **Terminal 2: Vite Compilation**
    ```bash
    npm run dev
    ```
*   **Terminal 3: Background Worker Queues (Mailers & WA notifications)**
    ```bash
    php artisan queue:work
    ```
*   **Terminal 4: Cron Scheduler**
    ```bash
    php artisan schedule:work
    ```

---

### **💡 Default Administrator Account**
- **Email:** `admin@sipinjam.ac.id`
- **Password:** `password`
