# 📬 Nurse-Healthcare Chat System - Job Application Based

## ✅ Updated Flow

The chat system is now **job application-based**. Here's how it works:

---

## 🔄 Complete Workflow

### 1️⃣ Nurse Applies to a Job
When a nurse submits a job application:
- Application is saved in `nurse_applications` table
- **Automatically creates a chat conversation** with the healthcare facility
- Initial system message is added to the conversation
- Both nurse and healthcare are added as participants

### 2️⃣ Chat Appears in Nurse's Dashboard
After applying:
- Healthcare facility appears in the nurse's chat list
- Shows the job title associated with the conversation
- Nurse can see all healthcare facilities they've applied to

### 3️⃣ Start Messaging
Nurse can:
- Click on the healthcare facility in chat list
- Send messages about their application
- Ask questions about the position
- Share additional documents

---

## 📊 Database Structure

### Conversations are linked to:
- **Nurse** (`nurse_id`) - The nurse who applied
- **Healthcare** (`healthcare_id`) - The facility/employer
- **Job** (`job_id`) - The specific job posting

### Example Data Flow:
```
Nurse (ID: 100) 
  ↓ applies to
Job (ID: 50) 
  ↓ posted by
Healthcare (ID: 25)
  ↓ creates
Conversation (nurse_id=100, healthcare_id=25, job_id=50)
```

---

## 🎯 Key Features

### Automatic Conversation Creation
**File:** `app/Http/Controllers/nurse/JobsController.php`

When `applyJobs()` is called:
```php
// 1. Save application
$applicationId = DB::table('nurse_applications')->insertGetId([...]);

// 2. Auto-create chat conversation
$conversation = Conversation::create([
    'subject' => 'Job Application: ' . $jobTitle,
    'nurse_id' => $nurse->id,
    'healthcare_id' => $healthcare->id,
    'job_id' => $job->id,
]);

// 3. Add system message
Message::create([
    'message' => 'You have submitted your application...',
    'message_type' => 'system',
]);
```

---

### Chat List Shows Applied Jobs
**File:** `app/Http/Controllers/nurse/ChatController.php`

The `index()` method queries:
```php
// Get healthcare facilities from nurse's job applications
$healthcareFacilities = DB::table('nurse_applications')
    ->join('users', 'nurse_applications.employer_id', '=', 'users.id')
    ->join('job_boxes', 'nurse_applications.job_id', '=', 'job_boxes.id')
    ->where('nurse_applications.nurse_id', $user->id)
    ->select(
        'users.*',
        'job_boxes.title as job_title',
        'nurse_applications.id as application_id'
    )
    ->get();
```

---

## 📍 Entry Points

### For Nurses:

1. **Chat Dashboard**
   ```
   /nurse/chat
   ```
   Shows all healthcare facilities from job applications

2. **From Job Details Page**
   ```
   /nurse/chat/start/{jobId}
   ```
   Opens chat for a specific job

3. **After Applying to Job**
   - Automatic redirect to chat
   - Or chat appears in conversation list

---

## 💬 Conversation Display

### In Chat List:
```
┌─────────────────────────────────────┐
│ 🏥 Test Hospital                    │
│    Registered Nurse - ICU           │ ← Job Title
│    "Your application is under..."   │ ← Last Message
│                             2m ago  │
└─────────────────────────────────────┘
```

### In Conversation View:
```
Header:
  🏥 Test Hospital
  📍 Registered Nurse - ICU Position
  
Messages:
  [System] You have submitted your application for 
           the position of Registered Nurse - ICU.
  
  [Nurse] Hello, I wanted to ask about the shift 
          schedule...
  
  [Healthcare] Hi! The shifts are 12 hours...
```

---

## 🔧 Code Changes Summary

### Modified Files:

| File | Changes |
|------|---------|
| `nurse/ChatController.php` | Updated to load healthcare from applications |
| `nurse/JobsController.php` | Added auto-conversation creation on job apply |
| `nurse/chat/index.blade.php` | Shows job titles in dropdown |
| `routes/web.php` | Added route for creating conversations |

---

## 🧪 Testing Steps

### 1. Apply to a Job as Nurse:
```javascript
// Example AJAX call
$.ajax({
    url: '/nurse/applyJobs',
    type: 'POST',
    data: {
        user_id: 100,  // Nurse ID
        job_id: 50,    // Job ID
        _token: '...'
    },
    success: function(response) {
        console.log(response.message);
        // "Job applied successfully. You can now chat..."
    }
});
```

### 2. Check Chat Dashboard:
```
http://localhost/mediqa_nurse/nurse/chat
```

You should see:
- Healthcare facility in the list
- Job title displayed
- Conversation thread ready

### 3. Send a Message:
- Click on healthcare facility
- Type and send message
- Message is saved and linked to the job application

---

## 🎨 UI Updates

### Dropdown Shows Job Info:
```html
<select name="recipient_id">
    <option value="25" data-job-title="ICU Nurse">
        Test Hospital - ICU Nurse
    </option>
    <option value="30" data-job-title="ER Nurse">
        Royal Women's - ER Nurse
    </option>
</select>
```

### Empty State Message:
```
You haven't applied to any jobs yet.
[Browse jobs] and apply to start chatting.
```

---

## 📬 Notifications

### Future Enhancements (Optional):
1. **Email Notification** to healthcare when nurse applies
2. **Push Notification** for new messages
3. **Status Updates** (application reviewed, shortlisted, etc.)
4. **Quick Replies** for common healthcare responses

---

## 🐛 Troubleshooting

### Chat not appearing after application?

1. **Check if application was created:**
   ```sql
   SELECT * FROM nurse_applications 
   WHERE nurse_id = YOUR_NURSE_ID 
   ORDER BY created_at DESC;
   ```

2. **Check if conversation was created:**
   ```sql
   SELECT * FROM conversations 
   WHERE nurse_id = YOUR_NURSE_ID 
   AND job_id IS NOT NULL;
   ```

3. **Clear cache:**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

### Error on applying to job?

Check Laravel logs:
```
storage/logs/laravel.log
```

Look for: "Failed to create chat conversation"

---

## ✅ Benefits of This Approach

1. **Context-Aware**: Chat is linked to specific job application
2. **Organized**: Separate conversation per job
3. **Professional**: Healthcare knows which position nurse applied for
4. **Trackable**: Can reference application status in chat
5. **Automatic**: No manual conversation creation needed

---

## 🚀 Next Steps

1. ✅ Test job application flow
2. ✅ Verify conversation is created automatically
3. ✅ Test messaging between nurse and healthcare
4. ✅ Add "Chat" button on application status page
5. ✅ Add notifications for new messages
6. ✅ Add file sharing for certificates/documents

---

**Last Updated:** March 21, 2026  
**Status:** ✅ Ready for Testing
