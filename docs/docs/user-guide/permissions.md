---
sidebar_position: 6
---

# Permissions

Databasement uses a role-based access control system with three user roles. Each role has different permissions for managing resources.

## User Roles

| Role       | Description                                                                |
|------------|----------------------------------------------------------------------------|
| **Admin**  | Full access to all features including user management                      |
| **Member** | Can manage database servers, volumes, and backups, but cannot manage users |
| **Viewer** | Read-only access to view resources and monitor backup status               |

## Permissions by Resource

### Database Servers

| Action            | Viewer | Member | Admin |
|-------------------|:------:|:------:|:-----:|
| View list         |   ✅    |   ✅    |   ✅   |
| Create            |   ❌    |   ✅    |   ✅   |
| Edit              |   ❌    |   ✅    |   ✅   |
| Delete            |   ❌    |   ✅    |   ✅   |
| Run backup        |   ❌    |   ✅    |   ✅   |
| Restore to server |   ❌    |   ✅    |   ✅   |

### Volumes

| Action    | Viewer | Member | Admin |
|-----------|:------:|:------:|:-----:|
| View list |   ✅    |   ✅    |   ✅   |
| Create    |   ❌    |   ✅    |   ✅   |
| Edit      |   ❌    |   ✅    |   ✅   |
| Delete    |   ❌    |   ✅    |   ✅   |

### Snapshots

| Action       | Viewer | Member | Admin |
|--------------|:------:|:------:|:-----:|
| View list    |   ✅    |   ✅    |   ✅   |
| View details |   ✅    |   ✅    |   ✅   |
| Download     |   ❌    |   ✅    |   ✅   |
| Delete       |   ❌    |   ✅    |   ✅   |

### Users

| Action               | Viewer | Member | Admin |
|----------------------|:------:|:------:|:-----:|
| View list            |   ✅    |   ✅    |   ✅   |
| Invite new user      |   ❌    |   ❌    |   ✅   |
| Edit user role       |   ❌    |   ❌    |   ✅   |
| Delete user          |   ❌    |   ❌    |   ✅   |
| Copy invitation link |   ❌    |   ❌    |   ✅   |

## Special Rules

### User Deletion Restrictions

Even admins have some restrictions when deleting users:

- **Cannot delete yourself**: An admin cannot delete their own account
- **Cannot delete the last admin**: The system must always have at least one admin user
