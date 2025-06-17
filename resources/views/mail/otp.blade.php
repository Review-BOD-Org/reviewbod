<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header -->
        <tr>
            <td style="background-color: #5C83F0; padding: 20px; text-align: center; color: white;">
                <h1 style="margin: 0; font-size: 24px;">OTP Verification</h1>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td style="padding: 30px 20px;">
                <p style="margin-top: 0; color: #333333; font-size: 16px;">Hello,</p>
                
                <div style="background-color: #f9f9f9; border-left: 4px solid #FF4B4B; padding: 15px; margin: 20px 0;">
                    <p style="margin: 5px 0; color: #333333; font-size: 16px;"><strong>Your OTP:</strong> {{$otp}}</p>
                    <p style="margin: 5px 0; color: #333333; font-size: 16px;"><strong>Expires:</strong> 10 minutes</p>
                </div>
                
                <p style="color: #333333; font-size: 16px;">Please use this one-time password to verify your account.</p>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background-color: #f7f7f7; padding: 15px; text-align: center; color: #666666; font-size: 12px; border-top: 1px solid #eeeeee;">
                <p style="margin: 0;">If you didn't request this OTP, please ignore this email.</p>
                <p>© {{Date("Y")}} ReviewBOD. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>