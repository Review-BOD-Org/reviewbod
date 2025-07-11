<?php

use App\Http\Controllers\FrontController;
use App\Http\Controllers\Manager;
use App\Http\Middleware\CheckLinked;
use App\Http\Middleware\CheckManager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Optimizer;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dash;
use App\Http\Controllers\AuthData;
use App\Http\Middleware\CheckUser;
use App\Http\Controllers\LinearCalendarController;
use App\Http\Controllers\LinearCustomerController;
use App\Http\Controllers\BotSender;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrelloReportController;
use App\Http\Controllers\TrelloCalendarController;
use App\Http\Controllers\ReportDispatcherController;

use App\Http\Controllers\TaskAnalyzerController;


Route::get('/', [FrontController::class, 'index']);
Route::get('/pricing', [FrontController::class, 'pricing']);

Route::get('/how', [FrontController::class, 'how']);
Route::get('/faq', [FrontController::class, 'faq']);
Route::get('/support', [FrontController::class, 'support']);



Route::get('/optimizer', [Optimizer::class, 'get_data_from_linear']);


Route::prefix('/auth')->group(function () {
    Route::get('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'login']);
    Route::get('/verification', [AuthController::class, 'verification']);
    Route::get('/choose', [AuthController::class, 'choose']);
    Route::get('/pricing', [AuthController::class, 'pricing']);


    //server side
    Route::post('/login', [AuthController::class, 'plogin']);
    Route::post('/register', [AuthController::class, 'pregister']);
    Route::post('/verification', [AuthController::class, 'pverification']);
    Route::get('/resend-otp', [AuthController::class, 'resend_otp']);
    Route::post('/choose', [AuthController::class, 'pchoose']);
    Route::post('/update_password', [AuthController::class, 'update_password']);
    Route::get('/linking', [AuthController::class, 'waiting']);
    Route::get('/workspace', [AuthController::class, 'workspace']);
    Route::post('/create', [AuthController::class, 'create']);


});


Route::prefix('/dashboard')->middleware(CheckUser::class)->group(function () {

    //managers 
    Route::get('/managers', [Dash::class, 'managers'])->name("user.managers");
    Route::post('/upload_file', [Dash::class, 'upload_file'])->name("user.upload_file");
    Route::post('/add_manager', [Dash::class, 'add_manager'])->name("user.add_manager");
    Route::post('/delete_bulk_managers', [Dash::class, 'delete_bulk_managers'])->name('user.delete_bulk_managers');
    Route::post('/user_manager', [Dash::class, 'user_manager'])->name("user.user_manager");
    Route::post('/remove_manager', [Dash::class, 'remove_manager'])->name("user.remove_manager");

    Route::post('/delete_manager', [Dash::class, 'delete_manager'])->name("user.delete_manager");
    Route::post('/edit_manager', [Dash::class, 'edit_manager'])->name("user.edit_manager");
    Route::post('/manager_setstatus', [Dash::class, 'manager_setstatus'])->name('user.manager_setstatus');
    Route::post('/bulk_block_managers', [Dash::class, 'bulk_block_managers'])->name('user.bulk_block_managers');



    Route::get('/users', [Dash::class, 'users'])->name("user.users");
    Route::post('/getUserAnalysis', [Dash::class, 'getUserAnalysis'])->name("user.getUserAnalysis");

    Route::get('/loadusers', [Dash::class, 'loadusers'])->name("user.loadusers");
    Route::post('/task-performance', [Dash::class, 'getTaskPerformance'])->name('user.getTaskPerformance');
    Route::post('/loadusertasks', [Dash::class, 'loadusertasks'])->name('user.loadusertasks');


    Route::get('/task-completion-trend', [Dash::class, 'getTaskCompletionTrend']);
    // Existing route
    Route::get('/task-completion-trend', [Dash::class, 'getTaskCompletionTrend']);

    // New routes for the charts
    Route::get('/task-distribution', [Dash::class, 'getTaskDistribution']);
    Route::get('/project-status', [Dash::class, 'getProjectStatus']);

    Route::post('/chat', [Dash::class, 'chat'])->name("user.chatt");
    Route::post('/get_template', [Dash::class, 'get_template'])->name("user.get_template");

    Route::get('/chat', [Dash::class, 'chatuser'])->name("user.chat");
    Route::get('/sidebar-chats', [Dash::class, 'getSidebarChats'])->name('user.sidebar_chats');
    Route::get('/last-sidebar-chats', [Dash::class, 'getSidebarChatsLast'])->name('user.getSidebarChatsLast');

    Route::get('/chats', [Dash::class, 'chats'])->name("user.chats");
    Route::get('/last_chat', [Dash::class, 'last_chat'])->name("user.last_chat");
    Route::get('/members', [Dash::class, 'members'])->name("user.members");
    Route::get('/member/{id}', [Dash::class, 'member'])->name("user.member");
    Route::get('/teams', [Dash::class, 'teams'])->name("user.teams");
    Route::get('/settings', [Dash::class, 'settings'])->name("user.settings");


    Route::post('/save_config_metrics', [Dash::class, 'save_config_metrics'])->name("metrics.save");
    Route::get('/chats', [Dash::class, 'chat_data'])->name('user.chat_data');
    Route::post('/chats/create', [Dash::class, 'createChat'])->name('user.create_chat');
    Route::get('/chats/load-more', [Dash::class, 'loadMoreMessages'])->name('user.loadMore');
    Route::delete('/chats/delete-chat', [Dash::class, 'deleteChat'])->name('user.delete_chat');

    Route::get('/', [Dash::class, 'dash'])->name("dashboard");
    Route::get('/{any}', [Dash::class, 'dash'])
        ->where('any', '.*');
    Route::get('/new', [Dash::class, 'new_user'])->name("user.new");
    Route::get('/customers', [LinearCustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [LinearCustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{linearUserId}', [LinearCustomerController::class, 'show'])->name('customers.show');
    Route::get('/edit/{id}', [Dash::class, 'edit'])->name("user.edit");
    Route::post('/send_invite', [LinearCustomerController::class, 'send_invite'])->name('customers.send_invite');
    Route::post('delete_linked', [Dash::class, 'delete_linked'])->name('customers.delete_linked');
    Route::post('update_user', [Dash::class, 'update_user'])->name('customers.personal.update');
    Route::post('update_password', [Dash::class, 'update_password'])->name('customers.personal.password');
    Route::post('delete_invitation', [Dash::class, 'delete_invitation'])->name('customers.delete_invitation');
    Route::post('/reports/data', [ReportDispatcherController::class, 'getReportData'])->name('reports.data');
    Route::post('/reports/analysis', [ReportDispatcherController::class, 'getAnalysis'])->name('reports.analysis');
    Route::post('/reports/export', [ReportDispatcherController::class, 'exportReport'])->name('reports.export');
    Route::post('/save_notification', [Dash::class, 'save_notification'])->name('save_notification');
    Route::post('/reaction', [Dash::class, 'reaction'])->name('user.reaction');
    Route::delete('/delete_account', [Dash::class, 'delete_account'])->name("user.delete_account");
    Route::get('/reports', [ReportDispatcherController::class, 'reports'])->name("user.reports");
    Route::post('/setstatus', [LinearCustomerController::class, 'setstatus'])->name('user.setstatus');
    Route::post('/bulk_send_invites', [LinearCustomerController::class, 'bulk_send_invites'])->name('user.bulk_send_invites');
    Route::post('/bulk_block_users', [LinearCustomerController::class, 'bulk_block_users'])->name('user.bulk_block_users');

    Route::post('/metrics/save-custom', [Dash::class, 'saveCustomMetric'])->name('metrics.save-custom');
    Route::delete('/metrics/delete-custom/{id}', [Dash::class, 'deleteCustomMetric'])->name('metrics.delete-custom');


    //managers



    // Route for getting calendar data
    Route::get('/get_data', function () {
        $service = Auth::user()->service ?? 'linear';
        $controller = $service === 'trello'
            ? new TrelloCalendarController()
            : new LinearCalendarController();

        return $controller->getCalendarData(request());
    })->name('user.get_data')->middleware('auth');

    // Route for linear dashboard
    Route::get('/linear-dashboard', function () {
        $service = Auth::user()->service ?? 'linear';
        $controller = $service === 'trello'
            ? new TrelloCalendarController()
            : new LinearCalendarController();

        return $controller->getDashboardData(request());
    })->name('user.linear-dashboard')->middleware('auth');

    // Route for dashboard data
    Route::get('/dashboard-data', function () {
        $service = Auth::user()->service ?? 'linear';
        $controller = $service === 'trello'
            ? new TrelloCalendarController()
            : new LinearCalendarController();

        return $controller->getDashboardData(request());
    })->name('linear.dashboard-data')->middleware('auth');
});
Route::get('/invite/{workspace}/{id}', [LinearCustomerController::class, 'invite'])->name("user.invite");


Route::prefix('/linear')->group(function () {
    Route::get('/auth', [AuthData::class, 'redirectToLinear']);

    Route::get('/callback', [AuthData::class, 'handleLinearCallback']);
    Route::get('/get_data', [AuthData::class, 'get_data']);
});

Route::prefix('/trello')->group(function () {
    Route::get('/auth', [AuthData::class, 'redirectToTrello']);

    Route::get('/callback', [AuthData::class, 'handleTrelloCallback']);
});


Route::prefix('/jira')->group(function () {
    Route::get('/auth', [AuthData::class, 'redirectToJira']);

    Route::get('/callback', [AuthData::class, 'handleJiraCallback']);
});

Route::prefix('/slack')->group(function () {
    Route::get('/auth', [AuthData::class, 'slack_auth']);

    Route::get('/callback', [AuthData::class, 'slack_callback']);


    Route::get('/channels', [AuthData::class, 'showChannelList'])->name('slack.channels');
    Route::post('/save-channel', [AuthData::class, 'saveSelectedChannel'])->name('slack.save_channel');

});


Route::post('/searchuser', [AuthData::class, 'streamChat']);


Route::get('/zoho-token', function () {
    // $scope = 'ZohoMail.organization.accounts.READ';
    // $auth_url = "https://accounts.zoho.eu/oauth/v2/auth?scope=$scope&client_id=1000.IXHYLAMYBC8WN6CK9FSE672SL8MB7I&response_type=code&redirect_uri=https://reviewbod.com/callbk&access_type=offline";
    // return redirect()->away($auth_url);

    $client_id = '1000.IXHYLAMYBC8WN6CK9FSE672SL8MB7I';
    $scope = 'ZohoMail.accounts.READ,ZohoMail.messages.ALL,ZohoMail.folders.ALL,ZohoMail.settings.ALL,ZohoMail.attachments.ALL';
    $redirect_uri = urlencode('https://reviewbod.com/callbk');
    $auth_url = "https://accounts.zoho.com/oauth/v2/auth?scope=$scope&client_id=$client_id&response_type=code&redirect_uri=$redirect_uri&access_type=offline";

    return redirect()->away($auth_url);

    return;
    $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
        'client_id' => '1000.IXHYLAMYBC8WN6CK9FSE672SL8MB7I',
        'client_secret' => 'fdba6ba70a41e54bd3008f961f26ad1dc0f99744fd',
        'redirect_uri' => 'https://reviewbod.com/callbk',
        'grant_type' => 'authorization_code',
        'code' => '1000.52ee637b57651b5f8fab309fe3f2b450.ff3a82f0249abdf35ff17c78b959e913',
    ]);

    return $response->json();
});


Route::get('/callbk', function (Request $request) {



    $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
        'refresh_token' => '1000.23383108e6e35c70f6b1cc9b77c8f1ef.72989308362135650f67ee24a6673c79',
        'client_id' => '1000.IXHYLAMYBC8WN6CK9FSE672SL8MB7I',
        'client_secret' => 'fdba6ba70a41e54bd3008f961f26ad1dc0f99744fd',
        'grant_type' => 'refresh_token',
    ]);

    return $response->json();


    // $accessToken = '1000.b13d9df62ee1f65cab952a2328d1b57a.24969fa180446ae7530dd91e4b82759f'; // Your access token
    // $apiDomain = 'https://www.zohoapis.eu';

    // $response = Http::withHeaders([
    //     'Authorization' => "Bearer $accessToken",
    // ])->get("$apiDomain/crm/v2/org");

    // return $response->json();
    // return;

    //     $auth_url = "https://accounts.zoho.eu/oauth/v2/auth?"
//     . "scope=ZohoMail.messages.ALL ZohoMail.accounts.READ&"
//     . "client_id=1000.IXHYLAMYBC8WN6CK9FSE672SL8MB7I&"
//     . "response_type=code&"
//     . "redirect_uri=https://reviewbod.com/callbk&"
//     . "access_type=offline&" // 🔥 Required for refresh token
//     . "prompt=consent"; // 🔥 Forces Zoho to return a new refresh token

    // return redirect($auth_url);

    $code = request()->query('code');

    $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
        'code' => $code,
        'client_id' => '1000.IXHYLAMYBC8WN6CK9FSE672SL8MB7I',
        'client_secret' => 'fdba6ba70a41e54bd3008f961f26ad1dc0f99744fd',
        'redirect_uri' => 'https://reviewbod.com/callbk',
        'grant_type' => 'authorization_code',
        'access_type' => 'offline', // 🔥 Ensures refresh token

    ]);

    return $response->json();
});


Route::get('/send_test_mail', function () {



    try {
        \Mail::raw('Simple test email body', function ($message) {
            $message->to('prebad50@gmail.com')
                ->subject('Test Email');
        });

        return "Text email sent successfully!";
    } catch (\Exception $e) {
        return "Email sending failed: " . $e->getMessage();
    }
});

Route::get('logout', function () {
    if (!Auth::check()) {
        return redirect('/auth/login');
    }
    Auth::logout();
    return redirect('/auth/login');
})->name('user.logout');

Route::get('/bot-mg', [BotSender::class, 'showChannelList']);
Route::get('/bot-xx', [BotSender::class, 'getLinearUser']);



// Task Analyzer Chat Routes
Route::post('/task-analyzer/chat/stream', [TaskAnalyzerController::class, 'streamTaskAnalyzerChat']);
Route::post('/task-analyzer/search/test', [TaskAnalyzerController::class, 'testSimilaritySearch']);
Route::get('/task-analyzer/components', [TaskAnalyzerController::class, 'getComponentTypes']);

// Alternative route grouping (optional)
Route::prefix('task-analyzer')->group(function () {
    Route::post('/chat/stream', [TaskAnalyzerController::class, 'streamTaskAnalyzerChat']);
    Route::post('/search/test', [TaskAnalyzerController::class, 'testSimilaritySearch']);
    Route::get('/components', [TaskAnalyzerController::class, 'getComponentTypes']);
});


Route::prefix('/invited')->group(function () {
    Route::get('/', [LinearCustomerController::class, 'login']);
    Route::post('/update_status', [LinearCustomerController::class, 'update_status']);

    Route::get('/login', [LinearCustomerController::class, 'login']);
    Route::post('/login', [LinearCustomerController::class, 'plogin']);

    Route::get('/space', [LinearCustomerController::class, 'space']);

    Route::post('/choose', [LinearCustomerController::class, 'choose']);
    Route::middleware(CheckLinked::class)->group(function () {
        Route::get('/dash', [LinearCustomerController::class, 'dash']);
        Route::get('/sidebar_chats', [LinearCustomerController::class, 'sidebar_chats'])->name("invited.sidebar_chats");
        Route::get('/dash/{any}', [LinearCustomerController::class, 'dash'])
            ->where('any', '.*');


        Route::get('/chat_data', [LinearCustomerController::class, 'chat_data']);
        Route::get('/loadMoreMessages', [LinearCustomerController::class, 'loadMoreMessages']);
        Route::delete('/deleteChat', [LinearCustomerController::class, 'deleteChat']);


        Route::post('/reaction', [LinearCustomerController::class, 'reaction']);
        Route::get('/logout', [LinearCustomerController::class, 'logout']);
        Route::get('/user', [LinearCustomerController::class, 'user']);
        Route::post('/getTaskPerformance', [LinearCustomerController::class, 'getTaskPerformance']);
        Route::post('/get_template', [LinearCustomerController::class, 'get_template']);
        Route::get('/settings', [LinearCustomerController::class, 'settings']);
        Route::post('/update_user_profile', [LinearCustomerController::class, 'update_user_profile']);
        Route::post('/update_password', [LinearCustomerController::class, 'update_password']);


    });
});

Route::get('/manager/invite/{workspace}/{id}', [Manager::class, 'invite'])->name("user.invite");


Route::prefix('manager')->group(function () {
    Route::get('/', [Manager::class, 'login']);
    Route::get('/login', [Manager::class, 'login']);

    Route::post('/update_password', [Manager::class, 'update_password']);
    Route::post('/update_status', [Manager::class, 'update_status']);
    Route::post('/login', [Manager::class, 'plogin']);


    Route::middleware(CheckManager::class)->group(function () {
        Route::get('/dash', [Manager::class, 'dash']);
        Route::get('/dash/{any}', [Manager::class, 'dash'])->where('any', '.*');




        Route::get('/chat_data', [Manager::class, 'chat_data']);
        Route::get('/loadMoreMessages', [Manager::class, 'loadMoreMessages']);
        Route::delete('/deleteChat', [Manager::class, 'deleteChat']);


        Route::post('/reaction', [Manager::class, 'reaction']);

        Route::get('/sidebar_chats', [Manager::class, 'sidebar_chats']);

        Route::post('/get_template', [Manager::class, 'get_template']);
        Route::get('/users', [Manager::class, 'users']);
        Route::get('/user/{id}', [Manager::class, 'member']);
           Route::post('/getTaskPerformance', [Manager::class, 'getTaskPerformance']);
        Route::get('/logout', [Manager::class, 'logout']);
        Route::get('/settings', [Manager::class, 'settings']);
           Route::post('/update_user', [Manager::class, 'update_user']);
           Route::post('/_update_password', [Manager::class, '_update_password']);
           Route::post('/save_notification', [Manager::class, 'save_notification']);

    });


});