# 🏢 Room Booking Management System

A web-based room booking system for internal use in institutions or organizations. This system allows authenticated users to book rooms (such as meeting rooms or auditoriums) by specifying the date and time. Built with Laravel for maintainability, scalability, and clarity.

---

## 📌 Overview

This application enables:

-   Room booking
-   Role-based access (Admin & Customer)
-   CRUD operations for room management
-   Uploading room images
-   Approval workflow for bookings
-   Soft delete & audit tracking

---

## 🛠️ Tech Stack

| Layer          | Technology                       |
| -------------- | -------------------------------- |
| Framework      | Laravel                          |
| Database       | MySQL                            |
| Authentication | Sanctum / SSO Kemenag            |
| File Storage   | Laravel Filesystem (Public Disk) |
| UI             | React                            |

---

## 🧱 Database Schema Summary

**Entities**:

-   `users`: System users with roles (admin/customer)
-   `rooms`: Rooms available for booking
-   `room_images`: Associated images per room
-   `bookings`: Booking records for room usage

**Core Attributes (per entity)**:

-   `created_by`, `updated_by`, `deleted_by`
-   `created_at`, `updated_at`, `deleted_at`

**Booking Fields**:

-   `booking_date`, `start_time`, `end_time`
-   `status`: `pending`, `approved`, `rejected`, `cancelled`
-   `purpose`: meeting purpose/agenda

Refer to the full ERD diagram in `/docs/erd.png`.

---

## ⚙️ Installation Guide

### 1. Clone & Setup

```bash
git clone git@github.com:faizmaruf/booking-room-be.git
cd room-booking-system
composer install
cp .env.example .env
php artisan key:generate
```
