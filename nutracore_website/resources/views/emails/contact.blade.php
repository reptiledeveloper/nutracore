<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Enquiry</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333;">
<h2 style="color:#0066cc;">New Contact Enquiry</h2>

<p><strong>Name:</strong> {{ $name }}</p>
<p><strong>Email:</strong> {{ $email }}</p>
<p><strong>Phone:</strong> {{ $telephone }}</p>
<p><strong>Subject:</strong> {{ $subject }}</p>
<p><strong>Message:</strong><br>{!! nl2br(e($message)) !!}</p>

<hr>
<p style="font-size:12px;color:#777;">This email was sent from your website Contact Us form.</p>
</body>
</html>
