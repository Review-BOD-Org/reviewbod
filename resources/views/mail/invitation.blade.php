<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReviewBod Invitation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header -->
        <tr>
            <td style="background-color: #4285F4; padding: 20px; text-align: center; color: white;">
                <h1 style="margin: 0; font-size: 24px;">You're Invited!</h1>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td style="padding: 30px 20px;">
                <p style="margin-top: 0; color: #333333; font-size: 16px;">Hello <strong>{{ $name }}</strong>,</p>
                <p style="color: #333333; font-size: 16px;">You've been invited to join ReviewBod. Use the link below to accept your invitation and get started.</p>
                <p style="color: #666666; font-size: 14px;">Invitation ID: {{ $id }}</p>
                
                <!-- Button -->
                <table align="center" border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
                    <tr>
                        <td style="background-color: #4285F4; border-radius: 4px; padding: 12px 24px; text-align: center;">
                            <a href="{{url("/invite/$id")}}" style="color: white; text-decoration: none; font-weight: bold; display: inline-block;">Accept Invitation</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background-color: #f7f7f7; padding: 15px; text-align: center; color: #666666; font-size: 12px; border-top: 1px solid #eeeeee;">
                &copy; {{Date("Y")}} ReviewBod. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>