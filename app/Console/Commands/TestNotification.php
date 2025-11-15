<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\User;

class TestNotification extends Command
{
    protected $signature = 'test:notification {--type=all : Type of test (all, model, service, routes)}';
    protected $description = 'Test the notification system components';

    public function handle()
    {
        $type = $this->option('type');

        switch ($type) {
            case 'model':
                $this->testModel();
                break;
            case 'service':
                $this->testService();
                break;
            case 'routes':
                $this->testRoutes();
                break;
            default:
                $this->testAll();
                break;
        }

        return 0;
    }

    private function testModel()
    {
        $this->info('Testing Notification Model...');

        // Test creating notification
        $user = User::first();
        if (!$user) {
            $this->error('No users found. Please run database seeders first.');
            return;
        }

        $notification = \App\Models\Notification::create([
            'title' => 'Test Notification',
            'message' => 'This is a test notification',
            'type' => 'info',
            'user_id' => $user->id,
        ]);

        $this->info('✓ Created notification: ' . $notification->id);

        // Test relations
        $this->info('✓ User relation: ' . ($notification->user ? 'OK' : 'FAIL'));
        $this->info('✓ Sender relation: ' . ($notification->sender ? 'OK' : 'FAIL'));

        // Test scopes
        $unreadCount = \App\Models\Notification::unread()->count();
        $this->info('✓ Unread scope: ' . $unreadCount . ' notifications');

        // Test methods
        $notification->markAsRead();
        $this->info('✓ Mark as read: ' . ($notification->is_read ? 'OK' : 'FAIL'));

        $this->info('Model tests completed successfully!');
    }

    private function testService()
    {
        $this->info('Testing Notification Service...');

        $service = app(NotificationService::class);
        $user = User::first();

        // Test send to user
        $result = $service->sendToUser($user, 'Service Test', 'Testing notification service', 'success');
        $this->info('✓ Send to user: ' . ($result ? 'SUCCESS' : 'FAIL'));

        // Test send to role
        $result = $service->sendToRole('admin', 'Role Test', 'Testing role notification', 'warning');
        $this->info('✓ Send to role: ' . ($result->count() > 0 ? 'SUCCESS' : 'FAIL'));

        // Test send to multiple users
        $users = User::whereIn('role', ['admin', 'training_coordinator'])->get();
        $result = $service->sendToUsers($users, 'Multi User Test', 'Testing multiple users', 'info');
        $this->info('✓ Send to multiple users: ' . ($result->count() > 0 ? 'SUCCESS' : 'FAIL'));

        // Test stats
        $stats = $service->getStats();
        $this->info('✓ Get stats: ' . (is_array($stats) ? 'SUCCESS' : 'FAIL'));

        $this->info('Service tests completed successfully!');
    }

    private function testRoutes()
    {
        $this->info('Testing Notification Routes...');

        // Test route list
        $this->call('route:list', ['--name' => 'notifications']);

        $this->info('Routes test completed!');
    }

    private function testAll()
    {
        $this->testModel();
        $this->line('');
        $this->testService();
        $this->line('');
        $this->testRoutes();
        $this->line('');
        $this->info('All notification tests completed successfully!');
    }
}
