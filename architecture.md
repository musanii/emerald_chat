# Emerald Chat - System Architecture Specification

## 1. System Overview

Emerald Chat is engineered as a headless, real-time communications micro-module. It bridges host applications (like Laravel SaaS monoliths, ERP systems, or custom portals) with a scalable WebSocket messaging backbone.

```
+-----------------------------------------------------------------------+
|                         HOST APPLICATION                              |
|  (Passes Authenticated User & Department Context via JWT / Sanctum)    |
+-----------------------------------------------------------------------+
                                   |
                                   v
+-----------------------------------------------------------------------+
|                        EMERALD CHAT ENGINE                            |
|                                                                       |
|   +-----------------------+               +-----------------------+   |
|   |   REST API Layer      |               |  Real-Time WS Server  |   |
|   |  (Laravel 11 / 12)    |               |   (Laravel Reverb)    |   |
|   +-----------------------+               +-----------------------+   |
|               |                                       |               |
|               +-------------------+-------------------+               |
|                                   |                                   |
|                                   v                                   |
|   +---------------------------------------------------------------+   |
|   |                     PERSISTENCE & CACHE                       |   |
|   |   PostgreSQL / MySQL  |  Redis (Presence)  | S3 File Storage  |   |
|   +---------------------------------------------------------------+   |
+-----------------------------------------------------------------------+
```

---

## 2. Entity Relationship Diagram (ERD) & Database Schema

```
+------------------+         +-------------------+         +------------------+
|   departments    | <------ |     channels      | <------ |   channel_user   |
+------------------+         +-------------------+         +------------------+
| id (PK)          |         | id (PK)           |         | channel_id (FK)  |
| name             |         | department_id(FK) |         | user_id (FK)     |
| slug             |         | name              |         | role             |
| created_at       |         | type (pub/priv/dm)|         | last_read_at     |
+------------------+         +-------------------+         +------------------+
                                       |
                                       v
                             +-------------------+         +------------------+
                             |     messages      | ------> |   attachments    |
                             +-------------------+         +------------------+
                             | id (PK)           |         | id (PK)          |
                             | channel_id (FK)   |         | message_id (FK)  |
                             | user_id (FK)      |         | file_name        |
                             | parent_id (FK)    |         | file_path        |
                             | body              |         | file_size        |
                             | created_at        |         | mime_type        |
                             +-------------------+         +------------------+
```

### Schema Detailed Specifications

1. **`departments`**: Logical tenant isolation.
2. **`channels`**: Scoped to departments or globally shared. `type` column holds `public`, `private`, or `direct`.
3. **`channel_user`**: Pivot handling channel memberships, role (`owner`, `member`), and unread indicator tracking using `last_read_at`.
4. **`messages`**: Multi-threaded conversation table. Self-referencing `parent_id` foreign key supports Slack-like thread sidebars.
5. **`attachments`**: Holds S3 meta metadata for direct streaming and visual previews.

---

## 3. Real-Time WebSocket Channel Architecture

Broadcasting routes are secured via Laravel Sanctum channel authorization:

- **Department Channel:** `private-department.{departmentId}`
  - Events: `ChannelCreated`, `DepartmentAnnouncement`
- **Room / Chat Channel:** `presence-channel.{channelId}`
  - Events: `MessageSent`, `MessageUpdated`, `UserTyping`, `ReactionAdded`
  - Presence payload tracks live active users (online green dot).
- **User Direct Notification Channel:** `private-user.{userId}`
  - Events: `DirectMessageReceived`, `MentionTriggered`

---

## 4. Integration Blueprint for Host Applications

Emerald Chat exposes two primary integration patterns:

### Pattern A: Headless API + Vue 3 Embed Widget
Include the compiled standalone custom element in your host app layout:
```html
<script src="https://cdn.your-domain.com/emerald-chat.js"></script>
<emerald-chat 
    api-url="https://api.chat.yourdomain.com/api" 
    auth-token="BEARER_SANCTUM_TOKEN"
    department-id="12">
</emerald-chat>
```

### Pattern B: Native Laravel Package Injection
For monolithic apps, composer-install `emerald-chat/core`, which injects routes, models, and migrations directly into the host database namespace.