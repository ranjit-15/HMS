# Hive - InfinityFree Deployment Guide

## Website: https://hive.page.gd

## Database Credentials (Pre-configured)
```
Host:     sql306.infinityfree.com
Port:     3306
Database: if0_41016418_hive
Username: if0_41016418
Password: YJf7AUEJYX
```

---

## Step 1: Upload Files

1. Login to InfinityFree Control Panel
2. Open **File Manager** → Navigate to `htdocs/`
3. **Delete** any existing files in `htdocs/`
4. Upload the **entire HMS folder contents** to `htdocs/`

**Important:** Upload files directly TO `htdocs/`, not inside a subfolder.

After upload, your structure should be:
```
htdocs/
├── .htaccess
├── index.php
├── .env.production
├── app/
├── bootstrap/
├── build/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
└── vendor/
```

---

## Step 2: Configure Environment

### 2.1 Rename .env.production to .env
In File Manager:
1. Right-click `.env.production`
2. Select **Rename**
3. Change to `.env`

### 2.2 Generate APP_KEY
Since InfinityFree doesn't have SSH, you need to generate the key manually:

**Option A:** Use online tool
- Visit: https://generate-random.org/laravel-key-generator
- Copy the generated key
- Edit `.env` and paste into `APP_KEY=`

**Option B:** Generate locally and copy
```bash
php artisan key:generate --show
```
Copy the output (e.g., `base64:xxxxx...`) to your `.env` file.

### 2.3 Update .env Values
Edit `.env` and update:
- `GOOGLE_CLIENT_ID` - Your Google OAuth client ID
- `GOOGLE_CLIENT_SECRET` - Your Google OAuth secret
- `ADMIN_EMAIL` - Your admin email
- `ADMIN_PASSWORD` - Your admin password
- `MAIL_*` settings if you want email notifications

---

## Step 3: Set Permissions

In File Manager:
1. Right-click `storage/` → **Permissions** → Set to `755` (recursive)
2. Right-click `bootstrap/cache/` → **Permissions** → Set to `755`

---

## Step 4: Create Storage Link

Since you can't run artisan commands, create the symlink manually:

1. In File Manager, navigate to `htdocs/`
2. Create a new folder called `storage` (if uploading public files)
3. Or just ensure `storage/app/public/` exists

**Note:** For uploaded files to work, you may need to reference them via `/storage/app/public/filename` in your code.

---

## Step 5: Test the Website

1. Visit: https://hive.page.gd/
2. You should see the login page
3. Visit: https://hive.page.gd/admin/login
4. Login with your admin credentials

---

## Troubleshooting

### Error 500 / Blank Page
1. Check if `.env` file exists and has APP_KEY
2. Verify `storage/` folder permissions are 755
3. Check `storage/logs/laravel.log` for errors

### Database Connection Error
- Verify database credentials in `.env`
- InfinityFree requires exact hostname: `sql306.infinityfree.com`

### CSS/JS Not Loading
- Verify `build/` folder was uploaded completely
- Clear browser cache (Ctrl+Shift+R)

### 404 Errors on Routes
- Verify `.htaccess` file was uploaded
- Check if mod_rewrite is enabled (it should be on InfinityFree)

### Session Issues
- The config uses `file` session driver which works on InfinityFree
- Clear browser cookies if having login issues

### Google OAuth Not Working
1. Go to Google Cloud Console
2. Update Authorized Redirect URIs to: `https://hive.page.gd/auth/google/callback`
3. Update `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` in `.env`

---

## InfinityFree Limitations

- **No SSH access** - Can't run artisan commands
- **No cron jobs** - Scheduler won't run (queue is set to `sync`)
- **File upload limit** - 10MB max
- **Execution time** - 60 seconds max
- **Database connections** - Limited concurrent connections

---

## Quick Checklist

- [ ] All files uploaded to `htdocs/`
- [ ] `.env.production` renamed to `.env`
- [ ] `APP_KEY` generated and added to `.env`
- [ ] `storage/` permissions set to 755
- [ ] `bootstrap/cache/` permissions set to 755
- [ ] Website accessible at https://hive.page.gd/
- [ ] Admin login works at https://hive.page.gd/admin/login
