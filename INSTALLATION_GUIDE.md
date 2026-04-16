# Astrology WooCommerce Integration Plugin

## 📦 Installation Guide

### Step 1: Upload Plugin
1. Download the `astrology-woocommerce.zip` file
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin"
4. Choose the ZIP file and click "Install Now"
5. Click "Activate Plugin"

**OR** manually upload:
1. Extract the ZIP file
2. Upload the `astrology-woocommerce` folder to `/wp-content/plugins/`
3. Activate from WordPress Admin → Plugins

### Step 2: Configure API Settings
1. Go to **WooCommerce → Astrology API**
2. Enter your API credentials:
   - **API Base URL**: `https://json.astrologyapi.com/v1`
   - **API User ID**: Your username
   - **API Key**: Your password/key
3. Enable/disable **Debug Mode** (logs API requests)
4. Enable/disable **Email Reports**
5. Click "Save Settings"
6. Click "Test API Connection" to verify

---

## 🎯 Usage Guide

### Creating Astrology Products

1. **Create a New Product**
   - Go to Products → Add New
   - Enter product name (e.g., "Birth Chart Reading")
   - Set price and description

2. **Select Form Type**
   - Scroll to Product Data section
   - Check "Astrology Service"
   - Select **Astrology Form Type**:
     - **Birth Chart (Single Person)** - For natal chart readings
     - **Love Compatibility (Two People)** - For relationship reports
     - **Numerology Report** - For name-based numerology
     - **Tarot Card Reading** - For tarot predictions
     - **Zodiac Horoscope** - For zodiac-based forecasts

3. **Add Form Instructions** (Optional)
   - Add custom instructions that appear above the form
   - Example: "Please enter accurate birth details for best results"

4. **Publish Product**

---

## 📋 Form Types & Features

### 1. Birth Chart (single_person)
**Fields:**
- Full Name *
- Date of Birth *
- Time of Birth *
- Place of Birth *
- Latitude (optional)
- Longitude (optional)
- Timezone (optional)

**API Endpoint:** `/natal_chart`

---

### 2. Love Compatibility (two_person)
**Fields:**
- Person 1: Name, DOB, Time, Place *
- Person 2: Name, DOB, Time, Place *

**API Endpoint:** `/match_making_report`

---

### 3. Numerology (numerology)
**Fields:**
- Full Name *
- Date of Birth (optional)

**API Endpoint:** `/numerology_report`

---

### 4. Tarot Reading (tarot)
**Fields:**
- Your Question *
- Card Spread Selection *
  - Three Card Spread
  - Celtic Cross
  - Single Card
  - Relationship Spread
  - Career Spread

**API Endpoint:** `/tarot_card_prediction`

---

### 5. Zodiac Horoscope (zodiac)
**Fields:**
- Zodiac Sign * (dropdown with all 12 signs)
- Report Type *
  - Daily
  - Weekly
  - Monthly
  - Yearly

**API Endpoint:** `/horoscope_report`

---

## 🔄 How It Works

### Customer Flow:
1. Customer visits product page
2. Sees dynamic form based on product type
3. Fills in required details
4. Clicks "Add to Cart"
5. Form data saved to cart item
6. Proceeds to checkout
7. Completes payment

### Automatic Processing:
8. Order status changes to "Completed"
9. Plugin automatically calls API for each product
10. Report generated and saved to order
11. Report displayed in My Account → Orders
12. Optional: Email sent with report

### Admin Features:
- View all reports in order details
- Regenerate reports manually
- Debug mode for troubleshooting
- API connection testing

---

## 📧 Email Integration

When enabled, reports are automatically sent via email:
- Attached to WooCommerce completion email
- HTML formatted for better readability
- Plain text fallback included

**Enable/Disable:**
WooCommerce → Astrology API → Email Reports checkbox

---

## 🔍 Viewing Reports

### For Customers:
1. Go to My Account → Orders
2. Click "View" on any order
3. Scroll to "Your Astrology Reports" section
4. View detailed report
5. Print report if needed

### For Admins:
1. Go to WooCommerce → Orders
2. Open any order
3. Scroll to order details
4. See "Your Astrology Reports" section
5. Option to regenerate: Order Actions → "Regenerate Astrology Reports"

---

## 🛠️ Troubleshooting

### API Connection Failed
1. Verify credentials in settings
2. Click "Test API Connection"
3. Check API URL format
4. Enable Debug Mode
5. Check WooCommerce → Status → Logs

### Form Not Showing
1. Ensure "Astrology Service" is checked
2. Verify form type is selected
3. Clear cache if using caching plugin
4. Check theme compatibility

### Report Not Generated
1. Check order status is "Completed"
2. Enable Debug Mode
3. Check logs for API errors
4. Try manual regenerate from order actions
5. Verify API credentials

### Debug Mode
Enable in settings to log:
- API requests
- API responses
- Error messages
- Form submissions

**View Logs:**
WooCommerce → Status → Logs → Select "astrology-woo"

---

## 🎨 Customization

### Override Form Templates
Copy templates to your theme:
```
/wp-content/themes/your-theme/astrology-woocommerce/form-single_person.php
/wp-content/themes/your-theme/astrology-woocommerce/form-two_person.php
/wp-content/themes/your-theme/astrology-woocommerce/form-numerology.php
/wp-content/themes/your-theme/astrology-woocommerce/form-tarot.php
/wp-content/themes/your-theme/astrology-woocommerce/form-zodiac.php
/wp-content/themes/your-theme/astrology-woocommerce/order-reports.php
```

### Custom CSS
Add to your theme's CSS:
```css
.astro-woo-form-wrapper {
    /* Your custom styles */
}
```

---

## 📊 API Payload Examples

### Birth Chart
```json
{
  "day": 15,
  "month": 8,
  "year": 1990,
  "hour": 14,
  "min": 30,
  "lat": 23.8103,
  "lon": 90.4125,
  "tzone": 6.0
}
```

### Compatibility
```json
{
  "m_day": 15, "m_month": 8, "m_year": 1990,
  "m_hour": 14, "m_min": 30,
  "m_lat": 23.8103, "m_lon": 90.4125, "m_tzone": 6.0,
  "f_day": 20, "f_month": 3, "f_year": 1992,
  "f_hour": 10, "f_min": 0,
  "f_lat": 23.8103, "f_lon": 90.4125, "f_tzone": 6.0
}
```

---

## 🔐 Security Features

- Nonce verification on all forms
- AJAX request validation
- Input sanitization
- SQL injection protection
- XSS prevention
- Capability checks for admin functions

---

## ✅ Requirements

- **WordPress:** 5.8+
- **WooCommerce:** 5.0+
- **PHP:** 7.4+
- **MySQL:** 5.6+
- Valid astrology API account

---

## 📞 Support

For issues, questions, or feature requests:
1. Check this documentation
2. Enable Debug Mode and check logs
3. Test API connection
4. Contact plugin support

---

## 🚀 Future Updates

Planned features:
- PDF report generation
- More form types
- Custom report templates
- Bulk report regeneration
- Analytics dashboard
- Multi-language support

---

**Plugin Version:** 1.0.0  
**Last Updated:** 2024  
**License:** GPL v2 or later
