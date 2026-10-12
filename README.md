# Wireless WPA3 and Enterprise Authentication Security Assessment Tool

![Project Status](https://img.shields.io/badge/Status-Completed-success?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-7.4%20|%208.x-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-Academic-blue?style=flat-square)

An authorized configuration-based cybersecurity assessment platform designed to evaluate and enhance the security posture of wireless networks. This tool focuses on validating modern **WPA3-Personal** and **WPA3-Enterprise** standards, auditing critical parameters like Protected Management Frames (PMF), RADIUS configurations, EAP authentication methods, and cryptographic hygiene.

Developed as an **Ethical Hacking & Information Security Capstone Project**.

---

## 📌 Features

- **Wi-Fi Configuration Auditing**: Evaluates wireless network configurations against industry security benchmarks and standards (802.11ax / WPA3).
- **WPA3 Personal & Enterprise Coverage**:
  - Checks for WPA3 transition mode vs. pure WPA3 enforcement.
  - Validates Protected Management Frames (PMF / 802.11w) configuration to mitigate deauthentication and disassociation attacks.
  - Assesses Wi-Fi password strength and complexity.
  - Audits Access Point firmware update status.
  - Inspects Enterprise parameters: RADIUS server setup, certificate status, and EAP methods (e.g., EAP-TLS vs. PEAP).
- **Automated Risk Scoring & Diagnosis**: Calculates an overall security score (0–100%) and categorizes security posture into `Secure`, `Needs Improvement`, or `Poor`.
- **Actionable Remediation Guidance**: Generates tailored mitigation recommendations based on detected vulnerabilities or misconfigurations.
- **Role-Based Access Control**:
  - **User Portal**: Run security audits, view previous assessment history, and export security reports.
  - **Admin Dashboard**: System-wide analytics, total assessment statistics, user management, and security score distributions.
- **Reporting & History**: Generates detailed, printable security audit reports with granular check results.
- **Modern UI/UX**: Responsive glassmorphism dark-mode interface with real-time feedback.

---

## 🏗️ Project Architecture & Directory Structure

```text
Ethical capstone/
│
├── api/                     # Backend REST API endpoints (PHP)
│   ├── config.php           # Database connection & PDO setup
│   ├── auth.php             # Session authentication checks
│   ├── login.php            # User and Admin authentication endpoint
│   ├── assess.php           # Security evaluation engine & database persistence
│   ├── results.php          # Assessment details & check breakdown endpoint
│   ├── history.php          # User assessment history retrieval
│   ├── stats.php            # System-wide statistics for dashboards
│   └── admin.php            # Administrative control endpoints
│
├── css/
│   └── style.css            # Custom CSS styling (Glassmorphism & Neon Dark Theme)
│
├── js/
│   └── main.js              # Client-side validation, API requests & dynamic UI handling
│
├── admin.html               # Administrator dashboard & metrics
├── assessment.html          # Interactive security assessment form
├── history.html             # Past assessment logs
├── home.html                # Project landing page & project overview
├── index.html               # User main dashboard
├── login.html               # User sign-in page
├── register.html            # New user registration page
├── reports.html             # Printable audit reports generator
├── results.html             # Assessment scoring & recommendation results page
├── database.sql             # MySQL schema definitions and seed data
└── README.md                # Project documentation
```

---

## 🛠️ Tech Stack

- **Backend**: PHP (PDO prepared statements, secure password hashing)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, Vanilla CSS3 (Custom glassmorphism design tokens), Vanilla JavaScript (ES6 Fetch API)
- **Environment**: Apache / XAMPP on Windows/Linux

---

## 🚀 Installation & Setup Guide

### 1. Prerequisites
- [XAMPP](https://www.apachefriends.org/) (with Apache and MySQL / MariaDB installed)
- Modern web browser (Chrome, Edge, Firefox)
- Git

### 2. Setup the Repository
Clone this repository into your XAMPP web directory (`htdocs`):

```bash
cd C:\xampp\htdocs
git clone https://github.com/karthik143526/Ethical-Hacking-Capstone.git "Ethical capstone"
```

### 3. Setup the Database
1. Launch **XAMPP Control Panel** and start both **Apache** and **MySQL**.
2. Open your browser and go to `http://localhost/phpmyadmin/`.
3. Click on the **Import** tab.
4. Choose the [`database.sql`](database.sql) file located inside the project root and click **Import** (or run the SQL script directly).
   - This creates the `wpa3_security` database and tables (`users`, `assessments`, `assessment_results`, `recommendations`).

### 4. Database Configuration
If your MySQL credentials differ from the default XAMPP settings (`root` with no password), update [`api/config.php`](api/config.php):

```php
$host = 'localhost';
$db   = 'wpa3_security';
$user = 'root';
$pass = ''; // Set your password if configured
```

### 5. Access the Application
Open your browser and navigate to:
```text
http://localhost/Ethical%20capstone/home.html
```

---

## 🔐 Default Admin Credentials

For evaluation and testing purposes, a default administrator account is included in `database.sql`:

- **Admin Email**: `karthikvenkat062@gmail.com`
- **Admin Password**: `192372092`
- **Portal**: [admin.html](http://localhost/Ethical%20capstone/admin.html)

---

## 🛡️ Security Evaluation Criteria

The system assesses Wi-Fi network settings based on key cybersecurity metrics:

| Check Item | Passing Condition | Weight / Penalty | Impact of Failure |
| :--- | :--- | :--- | :--- |
| **WPA3 Enforcement** | WPA3 Enabled (`Yes`) | -25 points | Vulnerable to offline dictionary & brute-force handshake cracking. |
| **PMF (802.11w)** | `Required` | -10 / -20 points | Allows deauthentication spoofing and DoS attacks against clients. |
| **Password Quality** | `Strong` (≥12 chars, mixed case, symbols) | -5 / -15 points | Weak credentials subject to rainbow tables or dictionary attacks. |
| **Firmware Status** | Up to date (`Yes`) | -15 points | Outdated router/AP firmware exposing unpatched CVEs. |
| **RADIUS Configuration** *(Enterprise)* | Configured | -25 points | Enterprise authentication cannot authenticate securely without AAA server. |
| **Certificate Status** *(Enterprise)* | Valid & Trusted | -20 points | Risk of Man-in-the-Middle (MitM) and rogue AP spoofing. |
| **EAP Method** *(Enterprise)* | `EAP-TLS` | -5 / -15 points | Legacy or insecure outer/inner auth methods (MSCHAPv2 vulnerabilities). |

---

## 📜 Disclaimer
This software is developed strictly for **academic, educational, and authorized configuration auditing purposes**. It evaluates configuration settings to strengthen wireless defensive security postures and promote best practices in network administration.

---

## 👨‍💻 Author
**Karthik Venkat**  
GitHub: [@karthik143526](https://github.com/karthik143526)
