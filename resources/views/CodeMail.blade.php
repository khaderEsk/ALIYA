<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من البريد الإلكتروني</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #4c6baf;
            padding: 20px;
            text-align: center;
            color: #fff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        .content p {
            font-size: 16px;
            color: #333;
            line-height: 1.5;
        }

        .verification-code {
            font-size: 20px;
            font-weight: bold;
            color: #4c6baf;
            margin: 20px 0;
            padding: 10px;
            background-color: #f0f0f0;
            display: inline-block;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>التحقق من البريد الإلكتروني</h1>
        </div>
        <div class="content">
            <p>مرحبًا،</p>
            <p>شكرًا لتسجيلك معنا! يرجى استخدام كود التحقق التالي لإتمام عملية التسجيل:</p>
            <div class="verification-code">{{ $mailData['code'] }}</div>
            <p>إذا لم تطلب هذا الكود، يرجى تجاهل الرسالة.</p>
        </div>
    </div>
</body>

</html>


{{-- 
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <title>{{ $mailData['title'] }}</title>
</head>

<body>
    <p>The Code is:{{ $mailData['code'] }}</p>
    <p>Thank you</p>
</body>

</html> --}}
