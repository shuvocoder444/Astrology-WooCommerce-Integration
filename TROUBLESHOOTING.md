# 🔧 Troubleshooting Guide - Report Display Issues

## কোথায় রিপোর্ট দেখবেন? (Where to See Reports?)

### ✅ Customer View (কাস্টমার ভিউ):

**Step 1: Go to My Account**
- Log in to your WordPress site
- Go to: **My Account** (আমার অ্যাকাউন্ট)
- Click **Orders** (অর্ডার)

**Step 2: View Order**
- Find the completed order
- Click **View** (দেখুন)
- Scroll down - you will see **"Your Astrology Reports"** section

**Screenshot Location:**
```
My Account → Orders → View Order → Scroll Down
```

### ✅ Admin View (অ্যাডমিন ভিউ):

**Step 1: Go to Orders**
- Log in to WordPress Admin
- Go to: **WooCommerce → Orders**

**Step 2: Open Order**
- Click on any order
- Scroll to **Order Details** section
- You will see **"Your Astrology Reports"** section

**Screenshot Location:**
```
WP Admin → WooCommerce → Orders → Click Order → Scroll Down
```

---

## 🚨 Common Problems & Solutions

### Problem 1: Reports Not Showing
**Symptoms:** "Your Astrology Reports" section doesn't appear

**Solutions:**
1. **Check Order Status**
   - Reports only generate when order status is **"Completed"**
   - Change order status: Edit Order → Order Status → Completed → Update

2. **Verify Product Setup**
   - Edit the product
   - Check if "Astrology Service" checkbox is enabled
   - Verify form type is selected
   - Save product

3. **Check if Form Was Filled**
   - Only orders with filled forms generate reports
   - Check order meta data for `_astro_form_data`

4. **Manual Regenerate**
   - Edit Order
   - Order Actions dropdown → "Regenerate Astrology Reports"
   - Click Update

### Problem 2: API Error Messages
**Symptoms:** Red error box instead of report

**Solutions:**
1. **Check API Credentials**
   ```
   WooCommerce → Astrology API → Settings
   - Verify API URL
   - Verify User ID
   - Verify API Key
   - Click "Test API Connection"
   ```

2. **Enable Debug Mode**
   ```
   WooCommerce → Astrology API → Enable Debug Mode → Save
   ```

3. **Check Logs**
   ```
   WooCommerce → Status → Logs → Select "astrology-woo"
   ```

### Problem 3: Empty/Blank Reports
**Symptoms:** Report section shows but content is empty

**Solutions:**
1. **Check API Response**
   - Enable Debug Mode
   - View Logs to see actual API response
   - Verify API is returning data

2. **Check Form Data**
   - Edit Order
   - Check Custom Fields for `_astro_form_data`
   - Verify data was saved correctly

### Problem 4: WooCommerce Compatibility Warning
**Symptoms:** "Some plugins are incompatible" warning

**Solutions:**
1. **Update Plugin** (if you have latest version, this is already fixed)
2. **Ignore Warning** - Plugin uses standard WooCommerce functions
3. **Check WooCommerce Version** - Requires WooCommerce 5.0+

---

## 📊 Debug Checklist

Run through this checklist to identify issues:

- [ ] Is WooCommerce active and version 5.0+?
- [ ] Is order status "Completed"?
- [ ] Is "Astrology Service" enabled on product?
- [ ] Is form type selected on product?
- [ ] Did customer fill the form before checkout?
- [ ] Are API credentials configured correctly?
- [ ] Does "Test API Connection" work?
- [ ] Is Debug Mode enabled?
- [ ] Are there any errors in logs?

---

## 🔍 How to Check If Data Exists

### Method 1: Database Check (Advanced)
```sql
SELECT * FROM wp_postmeta 
WHERE meta_key LIKE '%astro%' 
AND post_id = [ORDER_ID];
```

### Method 2: Check Order Meta (Easy)
1. Install plugin: "Post Meta Inspector" or similar
2. View order in admin
3. Look for meta keys:
   - `_astro_reports`
   - `_astro_reports_generated`
   - `_astro_form_type`
   - `_astro_form_data`

### Method 3: Enable Debug Mode
1. Go to: WooCommerce → Astrology API
2. Enable "Debug Mode"
3. Complete a test order
4. Check: WooCommerce → Status → Logs → astrology-woo
5. Look for:
   - "API Request"
   - "API Response"
   - Any error messages

---

## 📧 Email Not Received?

**Check:**
1. Is "Email Reports" enabled in settings?
2. Is WooCommerce email working? (Test other emails)
3. Check spam/junk folder
4. Verify SMTP configuration
5. Test with plugin: "WP Mail SMTP"

---

## 🎯 Test Workflow

Follow this to ensure everything works:

### Step 1: Setup
1. Go to WooCommerce → Astrology API
2. Enter API credentials
3. Enable Debug Mode
4. Save Settings
5. Click "Test API Connection" - should show success

### Step 2: Create Product
1. Products → Add New
2. Name: "Test Birth Chart"
3. Regular Price: $1
4. Product Data → Check "Astrology Service"
5. Select Form Type: "Birth Chart (Single Person)"
6. Publish

### Step 3: Test Order
1. Log out (or open incognito window)
2. Add product to cart
3. Fill the birth chart form:
   - Name: John Doe
   - DOB: 1990-01-15
   - Time: 14:30
   - Place: Dhaka, Bangladesh
4. Proceed to checkout
5. Complete order (use Cash on Delivery for testing)

### Step 4: Complete Order
1. Log in to admin
2. WooCommerce → Orders
3. Find the test order
4. Change Status to "Completed"
5. Click Update

### Step 5: View Report
1. Stay on order page
2. Scroll down
3. Look for "Your Astrology Reports" section
4. Should see:
   - ✅ Green checkmark
   - "Birth Chart Report"
   - Your Details section
   - Report section with data

### Step 6: Check Logs
1. WooCommerce → Status → Logs
2. Select "astrology-woo"
3. Should see:
   - API Request with payload
   - API Response with status 200
   - No errors

---

## 💡 Quick Fixes

### Fix 1: Force Regenerate
```php
// Add to functions.php temporarily
add_action('init', function() {
    if (isset($_GET['regen_reports']) && current_user_can('manage_woocommerce')) {
        $order_id = intval($_GET['order_id']);
        $order = wc_get_order($order_id);
        if ($order) {
            do_action('woocommerce_order_status_completed', $order_id);
            wp_die('Reports regenerated! Check order #' . $order_id);
        }
    }
});

// Then visit: yoursite.com/?regen_reports=1&order_id=123
```

### Fix 2: Clear Cache
- If using caching plugin, clear all caches
- WP Rocket: Clear cache
- W3 Total Cache: Empty all caches
- Browser: Hard refresh (Ctrl+Shift+R)

### Fix 3: Template Override
If reports not showing, theme might be overriding. Create:
```
/wp-content/themes/your-theme/woocommerce/order/order-details.php
```

Add after order table:
```php
do_action('woocommerce_order_details_after_order_table', $order);
```

---

## 📞 Still Having Issues?

1. **Enable Debug Mode** - Most important!
2. **Check Logs** - WooCommerce → Status → Logs
3. **Send This Info:**
   - WordPress version
   - WooCommerce version
   - PHP version
   - Active theme
   - Active plugins
   - Error messages from logs
   - Screenshot of settings page
   - Screenshot of product configuration

---

## ✅ Success Indicators

You'll know it's working when:

✅ "Test API Connection" shows green success message
✅ Form appears on product page
✅ Form data shows in cart
✅ Order meta contains `_astro_form_data`
✅ After completing order, `_astro_reports` meta appears
✅ "Your Astrology Reports" section displays on order page
✅ Green checkmark shows next to report title
✅ Report contains actual data from API
✅ Email received with report (if enabled)

---

**Last Updated:** April 2024
**Plugin Version:** 1.0.0
