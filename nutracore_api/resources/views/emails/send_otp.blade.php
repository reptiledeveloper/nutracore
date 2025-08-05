<p>Hi {{ $user->name ?? '' }},</p>
<p>Here is your one-time password (OTP) to log in to your BuyBuyCart account:<br />{{ $otp ?? '' }}</p>
<p>For your security, this OTP is valid for the next 5 minutes. Please use it to complete your login.</p>
<p>If you didn&rsquo;t request this, please ignore this message.</p>
<p>Thank you,<br />BuyBuyCart Team<br />+91 7669900247</p>
