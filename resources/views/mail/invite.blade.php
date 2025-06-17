<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Notification</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header -->
        <tr>
            <td style="background-color: #5C83F0; padding: 20px; text-align: center; color: white;">
                <h1 style="margin: 0; font-size: 24px;">{{ $taskData['due_status'] }}</h1>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td style="padding: 30px 20px;">
                <p style="margin-top: 0; color: #333333; font-size: 16px;">Hello <strong>{{ $taskData['user'] }}</strong>,</p>
                
                <div style="background-color: #f9f9f9; border-left: 4px solid #FF4B4B; padding: 15px; margin: 20px 0;">
                    <p style="margin: 5px 0; color: #333333; font-size: 16px;"><strong>Task:</strong> {{ $taskData['task_title'] }}</p>
                    
                    @if(!empty($taskData['raw_due_date']))
                    <p style="margin: 5px 0; color: #333333; font-size: 16px;"><strong>Due Date:</strong> {{ $taskData['raw_due_date'] }}</p>
                    @else
                    <p style="margin: 5px 0; color: #333333; font-size: 16px;"><strong>Due Date:</strong> No Due Date</p>
                    @endif
                    
                    <p style="margin: 5px 0; color: #333333; font-size: 16px;"><strong>Project:</strong> {{ $taskData['project'] }}</p>
                    <p style="margin: 5px 0; color: #333333; font-size: 16px;"><strong>Status:</strong> {{ $taskData['state'] }}</p>
                </div>
                
                <p style="color: #333333; font-size: 16px;">Please take action on this task as soon as possible.</p>
                
                <!-- Button -->
                <table align="center" border="0" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
                    <tr>
                        <td style="background-color: #4285F4; border-radius: 4px; padding: 12px 24px; text-align: center;">
                            <a href="https://linear.app/issue/{{ $taskData['task_id'] }}" style="color: white; text-decoration: none; font-weight: bold; display: inline-block;">View Task in Linear</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background-color: #f7f7f7; padding: 15px; text-align: center; color: #666666; font-size: 12px; border-top: 1px solid #eeeeee;">
                &copy; {{Date("Y")}} ReviewBOD System. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>