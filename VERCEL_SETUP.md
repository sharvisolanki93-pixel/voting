# Vercel Deployment Instructions

Your project is now configured to run on Vercel using the `vercel-php` runtime. However, for it to work correctly, you MUST set up a remote database and configure environment variables in the Vercel Dashboard.

## 1. Get a Remote MySQL Database
Since Vercel does not support local MySQL, you need a cloud-hosted database. You can use:
- **Aiven.io** (Free tier MySQL)
- **Supabase** (PostgreSQL, but your code is MySQL-focused)
- **PlanetScale** (MySQL-compatible)
- **DigitalOcean Managed MySQL**

## 2. Add Environment Variables in Vercel
Go to your project on **vercel.com** -> **Settings** -> **Environment Variables** and add the following:

| Key | Value |
|-----|-------|
| `DB_HOST` | Your remote database hostname (e.g., `mysql-123.aivencloud.com`) |
| `DB_USER` | Your database username |
| `DB_PASS` | Your database password |
| `DB_NAME` | Your database name (e.g., `defaultdb` or `voting_system`) |

## 3. Important Notes
- **Database Schema**: You must import the `database_schema.sql` file into your remote database using a tool like phpMyAdmin, MySQL Workbench, or the provider's SQL console.
- **PHP Runtime**: We are using `vercel-php@0.7.2`. If you encounter issues, you might need to update this version in `vercel.json`.
- **Filesystem**: Remember that Vercel functions are stateless and have a read-only filesystem (except for `/tmp`). If you plan to upload images (like candidates), you should use a service like **Cloudinary** or **AWS S3** instead of local `uploads/` folder.

## 4. Test the Connection
After deploying, visit your site at:
`https://your-project-name.vercel.app/test_connection.php`
This will help verify if your Vercel app can talk to your remote database.
