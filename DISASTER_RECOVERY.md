# Disaster Recovery and Backup System Documentation

## Overview

This Laravel multi-tenant SaaS implements a hospital-grade automated backup and disaster recovery system using `spatie/laravel-backup`. All backups are encrypted and stored off-server using S3-compatible storage for maximum security and compliance.

## Security Features

- **Encryption**: All backups are encrypted using AES-256 encryption (ZipArchive::EM_AES_256)
- **Off-Server Storage**: Backups are stored on S3-compatible storage, separate from the application server
- **Audit Compliance**: Retention policies designed for hospital-grade audit requirements (7-year retention for yearly backups)
- **Automated Monitoring**: Daily health checks ensure backups are created and accessible

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
# Backup Storage (S3-compatible)
BACKUP_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-backup-bucket-name
AWS_ENDPOINT=  # Leave empty for AWS S3, or set for S3-compatible services (e.g., DigitalOcean Spaces, MinIO)
AWS_USE_PATH_STYLE_ENDPOINT=false  # Set to true for S3-compatible services

# Backup Encryption (REQUIRED)
BACKUP_ARCHIVE_PASSWORD=your-strong-encryption-password-here
# IMPORTANT: Use a strong password (minimum 32 characters, mix of letters, numbers, symbols)
# Store this password securely - you'll need it to restore backups

# Backup Notifications
BACKUP_NOTIFICATION_EMAIL=admin@example.com  # Email for backup failure notifications
BACKUP_FAILURE_NOTIFICATIONS=mail  # Notification channels for failures (comma-separated: mail,slack)
BACKUP_HEALTH_NOTIFICATIONS=mail   # Notification channels for health checks

# Optional: Success notifications (leave empty to disable)
BACKUP_SUCCESS_NOTIFICATIONS=  # Leave empty to disable success notifications

# Backup Monitoring
BACKUP_MONITOR_DISKS=s3  # Comma-separated list of disks to monitor
BACKUP_MAX_AGE_DAYS=1    # Maximum age in days before backup is considered unhealthy
BACKUP_MAX_STORAGE_MB=10240  # Maximum storage in MB (10GB default)

# Backup Retention (optional overrides)
BACKUP_KEEP_ALL_DAYS=7        # Keep all backups for 7 days
BACKUP_KEEP_DAILY_DAYS=30     # Keep daily backups for 30 days
BACKUP_KEEP_WEEKLY_WEEKS=12   # Keep weekly backups for 12 weeks (3 months)
BACKUP_KEEP_MONTHLY_MONTHS=12 # Keep monthly backups for 12 months (1 year)
BACKUP_KEEP_YEARLY_YEARS=7    # Keep yearly backups for 7 years (audit compliance)
```

### S3-Compatible Storage Setup

#### AWS S3

1. Create an S3 bucket in your AWS account
2. Set bucket policies to ensure only your application can access it
3. Enable versioning for additional protection
4. Consider enabling S3 Object Lock for compliance requirements

#### DigitalOcean Spaces

```env
AWS_ENDPOINT=https://nyc3.digitaloceanspaces.com
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_DEFAULT_REGION=nyc3
```

#### MinIO (Self-Hosted)

```env
AWS_ENDPOINT=https://minio.example.com
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_DEFAULT_REGION=us-east-1
```

## Backup Schedule

Automated backups run daily via Laravel's task scheduler:

- **Daily Backup**: Runs at 2:00 AM server time
- **Cleanup**: Runs at 2:30 AM (after backup completes)
- **Health Monitoring**: Runs at 3:00 AM

### Manual Backup

You can create a backup manually at any time:

```bash
php artisan backup:run
```

### Backup Verification

Check backup health status:

```bash
php artisan backup:monitor
```

List all backups:

```bash
php artisan backup:list
```

## Restore Process

### Prerequisites

1. **Backup Password**: You must have the `BACKUP_ARCHIVE_PASSWORD` used to encrypt the backup
2. **Access to S3**: You must have access to the S3 bucket where backups are stored
3. **Fresh Laravel Installation**: For full restore, you need a fresh Laravel installation with the same application codebase

### Step 1: Download Backup from S3

#### Using AWS CLI

```bash
# Install AWS CLI if not already installed
# Configure AWS credentials
aws configure

# List available backups
aws s3 ls s3://your-backup-bucket/your-app-name/ --recursive

# Download a specific backup
aws s3 cp s3://your-backup-bucket/your-app-name/2024-01-15-02-00-00.zip ./backup.zip
```

#### Using S3 Console

1. Log into your S3 provider's console
2. Navigate to your backup bucket
3. Download the backup ZIP file you want to restore

#### Using Laravel Backup List

```bash
# This requires the application to be running and connected to S3
php artisan backup:list
# Note the backup path, then download manually from S3
```

### Step 2: Extract Backup Archive

The backup is encrypted, so you'll need to decrypt it first:

```bash
# Create a restore directory
mkdir -p /tmp/restore
cd /tmp/restore

# Extract the encrypted backup
# You'll be prompted for the password
unzip -P "your-backup-password" /path/to/backup.zip
```

**Note**: If `unzip` doesn't support encrypted archives, you may need to use a different tool or restore via Laravel commands (see Step 3b).

### Step 3: Restore Database

#### Option A: Direct Database Restore

1. Extract the backup to see database dump files
2. The database dump will be in the `db-dumps` directory

**For MySQL/MariaDB:**
```bash
# Drop existing database (BE CAREFUL - this deletes all data!)
mysql -u root -p -e "DROP DATABASE IF EXISTS your_database_name;"
mysql -u root -p -e "CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Restore database
mysql -u root -p your_database_name < db-dumps/your_database_name.sql
```

**For PostgreSQL:**
```bash
# Drop and recreate database
dropdb -U postgres your_database_name
createdb -U postgres your_database_name

# Restore database
psql -U postgres your_database_name < db-dumps/your_database_name.sql
```

**For SQLite:**
```bash
# Copy the database file to the database directory
cp db-dumps/database.sqlite database/database.sqlite
```

#### Option B: Using Laravel Backup Restore (Recommended)

Laravel Backup package doesn't have a built-in restore command, so you'll need to:

1. Extract the backup archive (as shown in Step 2)
2. Follow the database restore steps in Option A
3. Restore files (Step 4)

### Step 4: Restore Files

1. Extract the backup archive if you haven't already
2. Files will be in the backup directory structure
3. Copy files to their respective locations:

```bash
# Restore application files (if needed)
cp -r files/your-app-name/* /path/to/your/laravel/app/

# Restore storage files
cp -r files/your-app-name/storage/* /path/to/your/laravel/app/storage/

# Restore public files (if any)
cp -r files/your-app-name/public/* /path/to/your/laravel/app/public/
```

**Important**: Only restore files if you're recovering from a complete loss. If you're just restoring data, skip file restoration.

### Step 5: Verify Restore

1. **Check database connection:**
```bash
php artisan migrate:status
```

2. **Verify data integrity:**
```bash
# Check tenant data
php artisan tinker
>>> \App\Models\Tenant::count()
>>> \App\Models\User::count()
>>> \App\Models\Clinic::count()
```

3. **Test application:**
   - Access the application in browser
   - Verify tenants can log in
   - Check critical functionality

### Step 6: Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Partial Restore (Specific Tenant)

If you need to restore data for a specific tenant only:

1. Download and extract the backup as described above
2. Restore the database dump to a temporary database
3. Export only the tenant's data:

```bash
# MySQL example - export specific tenant
mysqldump -u root -p temporary_db \
  --where="tenant_id=1" \
  tenants \
  users \
  clinics \
  queues \
  sub_queues \
  # ... other tenant-scoped tables

# Import to production database
mysql -u root -p production_db < tenant_1_dump.sql
```

**Warning**: This is complex and risky. Test thoroughly in a staging environment first.

## Backup Verification Steps

### Daily Verification (Automated)

The system automatically verifies backups daily. Check logs:

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log | grep backup

# Check scheduled task logs
php artisan schedule:list
```

### Manual Verification

1. **Verify backup exists:**
```bash
php artisan backup:list
```

2. **Verify backup health:**
```bash
php artisan backup:monitor
```

3. **Test backup download:**
   - Download a recent backup from S3
   - Verify you can extract it with the password
   - Verify database dump is readable

4. **Test restore in staging:**
   - Perform a full restore in a staging environment
   - Verify all data is correct
   - Document any issues

### Monthly Verification Checklist

- [ ] Verify backups are being created daily
- [ ] Verify backups are stored on S3
- [ ] Test backup download from S3
- [ ] Verify backup encryption is working
- [ ] Perform test restore in staging environment
- [ ] Verify retention policy is working correctly
- [ ] Check backup storage usage
- [ ] Review backup failure notifications
- [ ] Update documentation if needed

## Troubleshooting

### Backup Failures

**Error: "Backup failed because: No valid credential provider chain found"**
- Solution: Check AWS credentials in `.env` file
- Verify `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` are correct
- Verify AWS IAM user has S3 write permissions

**Error: "Backup failed because: Access Denied"**
- Solution: Check S3 bucket permissions
- Ensure IAM user/role has `s3:PutObject` permission
- Verify bucket name is correct

**Error: "Backup failed because: Encryption password not set"**
- Solution: Set `BACKUP_ARCHIVE_PASSWORD` in `.env` file
- Use a strong password (minimum 32 characters)

**Error: "Backup failed because: Database connection failed"**
- Solution: Check database credentials in `.env`
- Verify database server is accessible
- Check database user has backup permissions

### Restore Issues

**Error: "Cannot extract encrypted backup"**
- Solution: Verify you're using the correct password
- Try using 7-Zip or WinRAR if `unzip` doesn't support encryption
- Verify backup file isn't corrupted (download again)

**Error: "Database restore fails"**
- Solution: Verify database dump file isn't corrupted
- Check database user has CREATE/DROP permissions
- Verify database server has enough disk space

**Error: "Missing tables after restore"**
- Solution: Run migrations: `php artisan migrate`
- Check Laravel logs for errors
- Verify database dump contains all tables

## Backup Retention Policy

The system implements a hospital-grade retention policy:

- **All backups**: Kept for 7 days
- **Daily backups**: Kept for 30 days (1 month)
- **Weekly backups**: Kept for 12 weeks (3 months)
- **Monthly backups**: Kept for 12 months (1 year)
- **Yearly backups**: Kept for 7 years (audit compliance)

This ensures:
- Quick recovery from recent issues (7 days of all backups)
- Monthly recovery capability (30 days)
- Quarterly recovery capability (3 months)
- Annual recovery capability (1 year)
- Long-term audit compliance (7 years)

## Security Best Practices

1. **Backup Password Security**:
   - Use a strong password (minimum 32 characters)
   - Store password securely (password manager)
   - Rotate password periodically
   - Never commit password to version control

2. **S3 Bucket Security**:
   - Enable bucket encryption at rest
   - Use bucket policies to restrict access
   - Enable S3 access logging
   - Consider S3 Object Lock for compliance

3. **Access Control**:
   - Use IAM roles with least privilege
   - Enable MFA for S3 access
   - Regularly audit S3 access logs
   - Rotate AWS access keys periodically

4. **Monitoring**:
   - Monitor backup failures via email
   - Set up CloudWatch alarms for S3 bucket
   - Review backup logs regularly
   - Document all restore operations

## Compliance and Audit

### Audit Trail

All backup operations are logged:
- Backup creation timestamps
- Backup file names and sizes
- Storage locations (S3 paths)
- Cleanup operations
- Health check results

### Documentation Requirements

Maintain documentation for:
- Backup schedule and configuration
- Restore procedures (this document)
- Backup password storage location
- S3 bucket access credentials
- Test restore procedures and results
- Incident reports (backup failures, restore operations)

### Regulatory Compliance

This backup system is designed to meet:
- **HIPAA**: Encrypted backups, off-server storage, audit trails
- **GDPR**: Secure storage, retention policies, data recovery procedures
- **SOC 2**: Automated backups, monitoring, access controls

Adjust retention policies based on your specific regulatory requirements.

## Emergency Contact

In case of data loss or backup failure:

1. **Immediate Action**: Check backup status
   ```bash
   php artisan backup:monitor
   php artisan backup:list
   ```

2. **If backups are failing**: 
   - Check Laravel logs: `tail -f storage/logs/laravel.log`
   - Check S3 connectivity
   - Verify credentials
   - Contact system administrator

3. **If data loss occurs**:
   - DO NOT perform any operations on the database
   - Immediately stop the application if possible
   - Document the incident
   - Follow restore procedures in this document
   - Notify stakeholders

## Additional Resources

- [Spatie Laravel Backup Documentation](https://spatie.be/docs/laravel-backup)
- [AWS S3 Documentation](https://docs.aws.amazon.com/s3/)
- [Laravel Task Scheduler](https://laravel.com/docs/scheduling)

## Change Log

- **2024-01-XX**: Initial backup system implementation
  - Encrypted backups enabled
  - S3 storage configured
  - Daily automated backups
  - 7-year retention policy

