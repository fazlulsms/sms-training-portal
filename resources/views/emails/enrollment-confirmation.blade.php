<x-mail::message>
# 🎓 Enrollment Confirmed!

Dear **{{ $user->name }}**,

Your enrollment has been successfully registered. We look forward to welcoming you to the training!

---

## 📋 Enrollment Details

| | |
|---|---|
| **Course** | {{ $courseName }} |
| **Batch** | {{ $batchCode ?? 'TBA' }} |
| **Start Date** | {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'TBA' }} |
| **Reference** | {{ $enrollment->enrollment_code ?? 'ENR-' . str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }} |

---

@if($tempPassword)
## 🔑 Your Account Credentials

A participant account has been created for you:

- **Email:** {{ $user->email }}
- **Password:** `{{ $tempPassword }}`

**Please log in and change your password immediately.**

<x-mail::button :url="$loginUrl" color="primary">
Login to My Account
</x-mail::button>

@endif

## 💳 Payment Information

Our team will contact you with payment instructions within 24 hours. Payment can be made via:
- Bank transfer (details will be provided)
- bKash / Nagad
- Online payment gateway

## 📞 Need Help?

If you have any questions, please contact us:
- 📧 training@smscert.com

Thank you for choosing SMS Training Services!

**SMS Training Services**
*Professional Training & Certification*

---
<small>This is an automated message. Please do not reply directly to this email.</small>
</x-mail::message>
