<?php
phpinfo();
?>
```

Then visit: `http://localhost/anti_sleep/phpinfo.php`

- **If you see a purple PHP info page:** ✅ PHP is working!
- **If you see the code or get an error:** ❌ PHP is NOT working

## 🔧 **Quick Fix: Use XAMPP**

Since this is the easiest solution, here's a step-by-step:

1. **Download XAMPP:** https://www.apachefriends.org/download.html

2. **Install it** (just click Next, Next, Finish)

3. **Copy your entire `anti_sleep` folder to:**
```
   C:\xampp\htdocs\anti_sleep\
```

4. **Open XAMPP Control Panel** (search for it in Windows)

5. **Start Apache and MySQL:**
   - Click "Start" button next to Apache
   - Click "Start" button next to MySQL
   - Wait for them to turn green

6. **Open browser and go to:**
```
   http://localhost/anti_sleep/test_connection.php
```

7. **If that works, go to:**
```
   http://localhost/anti_sleep/create_admin.php
```

8. **Then try logging in:**
```
   http://localhost/anti_sleep/login.html