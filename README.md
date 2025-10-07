# PGold API - Crypto & Gift Card Trading Platform

A Laravel-based REST API for cryptocurrency and gift card trading with user authentication, OTP verification, referral system, and rate calculation.

# Instructions:
1. Clone the repository
2. Copy the contents of env.example to .env
3. Make sure you have docker installed
4. Run the command below to spin it up
5. Command: docker compose up --build -d

# To access the endpoints via Postman
1. Open Postman
2. Add the API Base URL: http://localhost:8000
3. Add the API Key: 100211000_test_api_key
4. Collection link: https://www.postman.com/digiswitch/workspace/pgold-api/example/29219503-637df81e-62cb-4970-a0c9-d301b3548fe1?action=share&creator=29219503&ctx=documentation

### Access the Application

- **API Base URL:** http://localhost:8000
- **Database:** localhost:5432
- **Health Check:** http://localhost:8000/api/v1/rates

### Authentication & User Management
- ✅ User registration with email verification
- ✅ OTP-based email verification (10-minute expiry)
- ✅ Login with Sanctum token authentication
- ✅ Username availability validation
- ✅ Referral system with relationship tracking

### Rate Calculator
- ✅ Real-time crypto rates (BTC, ETH, USDT, USDC, BNB)
- ✅ Gift card rates (Amazon, iTunes, Google Play, Steam, Visa, eBay)
- ✅ Buy/Sell rate calculations
- ✅ Min/Max amount validation

### Security & Performance
- ✅ API key authentication
- ✅ Rate limiting (configurable per endpoint)
- ✅ Database queue for background jobs
- ✅ Comprehensive logging with data sanitization

---

## Tech Stack

- **Framework:** Laravel 11.x
- **Language:** PHP 8.2+
- **Database:** PostgreSQL 16
- **Queue:** Database driver
- **Authentication:** Laravel Sanctum
- **Server:** Nginx + PHP-FPM (Docker)
- **Container:** Docker & Docker Compose

---

## 📡 API Endpoints

### Authentication Endpoints
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/v1/accounts/register` | Register new user | API Key |
| POST | `/api/v1/accounts/verify` | Verify OTP | API Key |
| POST | `/api/v1/accounts/resend-otp` | Resend OTP | API Key |
| POST | `/api/v1/accounts/login` | User login | API Key |
| POST | `/api/v1/accounts/validate-username` | Check username availability | API Key |

### Rate Calculator Endpoints
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/v1/rates` | Get all rates (or filter by type) | API Key |
| POST | `/api/v1/rates/calculate` | Calculate conversion | API Key |
