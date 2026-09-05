# Emerald Chat - Master Development Roadmap & Git Commit Plan

This document outlines the step-by-step engineering plan divided into sequential cycles. Each cycle ends with structured, conventional Git commits to keep the repository history clean, pedagogical, and enterprise-grade.

---

## 🔄 Cycle 1: Core Foundation & Database Schema Setup
**Goal:** Initialize the Laravel API foundation, configure database migrations, and establish model relationships.

- [x] Step 1.1: Project initialization & API route publishing (`php artisan install:api`)
- [ ] Step 1.2: Create `departments` migration, model, factory, and seeder
- [ ] Step 1.3: Create `channels` and `channel_user` pivot migration & models
- [ ] Step 1.4: Create `messages` (threaded) and `attachments` migration & models
- [ ] Step 1.5: Define Eloquent relationships (HasMany, BelongsToMany, Parent/Child Threads)

### 📌 Cycle 1 Expected Git Commits
```bash
git commit -m "chore: initialize emerald chat api structure with sanctum support"
git commit -m "feat(database): create departments table migration and model"
git commit -m "feat(database): add channels and channel_user pivot schema"
git commit -m "feat(database): add threaded messages and attachments schema"
git commit -m "feat(models): establish eloquent relationships across core chat entities"
```

---

## 🔄 Cycle 2: Authentication, Authorization & Department API
**Goal:** Build endpoints for managing departments, channels, and member access rules.

- [ ] Step 2.1: Sanctum token auth endpoints (Login/Issue Token/User Profile)
- [ ] Step 2.2: Department CRUD APIs and user assignment logic
- [ ] Step 2.3: Channel management APIs (Create channel, Join channel, Invite users)
- [ ] Step 2.4: Form Request validation classes (`StoreChannelRequest`, `JoinChannelRequest`)
- [ ] Step 2.5: Policy classes (`ChannelPolicy`, `MessagePolicy`) for access control

### 📌 Cycle 2 Expected Git Commits
```bash
git commit -m "feat(auth): implement sanctum API token authorization endpoints"
git commit -m "feat(api): implement department management routes and controllers"
git commit -m "feat(api): add channel creation and member invitation endpoints"
git commit -m "feat(security): implement policies for channel and message authorization"
```

---

## 🔄 Cycle 3: Real-Time WebSockets Engine (Laravel Reverb)
**Goal:** Configure broadcasting driver, WebSocket events, and live presence tracking.

- [ ] Step 3.1: Install and configure Laravel Reverb (`php artisan install:broadcasting`)
- [ ] Step 3.2: Create broadcastable events: `MessageSent`, `UserTyping`, `UserPresence`
- [ ] Step 3.3: Configure broadcast channels (`routes/channels.php`) with authorization checks
- [ ] Step 3.4: Set up Redis queue listener for handling broadcast background jobs

### 📌 Cycle 3 Expected Git Commits
```bash
git commit -m "feat(realtime): install and configure laravel reverb websocket driver"
git commit -m "feat(events): create MessageSent and UserTyping broadcast events"
git commit -m "feat(broadcasting): secure presence and private channel authorization routes"
```

---

## 🔄 Cycle 4: File Attachments & Media Pipeline
**Goal:** Handle asynchronous file uploads, validation, and cloud storage streaming.

- [ ] Step 4.1: Create `AttachmentController` for chunked/signed upload URLs
- [ ] Step 4.2: Implement file mime validation (Images, PDFs, Office Documents)
- [ ] Step 4.3: S3/MinIO disk integration with image thumb generation queue
- [ ] Step 4.4: Link attachments to messages within DB transactions

### 📌 Cycle 4 Expected Git Commits
```bash
git commit -m "feat(storage): configure S3/MinIO driver for file attachments"
git commit -m "feat(api): create attachment upload endpoint with mime and size validation"
git commit -m "feat(jobs): add asynchronous image thumbnail processing job"
```

---

## 🔄 Cycle 5: Vue 3 Embeddable Frontend Engine
**Goal:** Build the single-page chat interface component ready for embedding into any application.

- [ ] Step 5.1: Initialize Vue 3 + Vite + Tailwind CSS layout (Sidebar, Main Chat, Drawer)
- [ ] Step 5.2: Configure Laravel Echo + Pusher-JS client connection to Reverb
- [ ] Step 5.3: Build message stream with thread sidebars and typing indicators
- [ ] Step 5.4: Package Vue app as a Custom Element (Web Component) for easy embedding

### 📌 Cycle 5 Expected Git Commits
```bash
git commit -m "feat(frontend): initialize vue 3 workspace with vite and tailwind css"
git commit -m "feat(frontend): integrate laravel echo for real-time message listening"
git commit -m "feat(frontend): build 3-pane chat layout with active threads support"
git commit -m "feat(build): export chat interface as embeddable web component"
```